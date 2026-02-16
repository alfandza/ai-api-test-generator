<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class OpenAIService
{
    private $client;
    private $apiKey;
    private $model;

    public function __construct()
    {
        $this->apiKey = $_ENV['OPENAI_API_KEY'] ?? '';
        $this->model = $_ENV['OPENAI_MODEL'] ?? 'gpt-4o-mini';

        if (empty($this->apiKey)) {
            throw new \Exception('OPENAI_API_KEY is not set in environment variables');
        }

        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'timeout' => 60,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    public function generateTestCases(array $apiConfig): array
    {
        $prompt = $this->buildPrompt($apiConfig);

        try {
            $response = $this->client->post('chat/completions', [
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an expert QA engineer specializing in API testing. Generate comprehensive test cases in valid JSON format only. Do not include any markdown formatting or code blocks.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 4000,
                ]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (!isset($body['choices'][0]['message']['content'])) {
                throw new \Exception('Invalid response from OpenAI API');
            }

            $content = $body['choices'][0]['message']['content'];

            // Clean up the response - remove markdown code blocks if present
            $content = preg_replace('/```json\s*/i', '', $content);
            $content = preg_replace('/```\s*$/i', '', $content);
            $content = trim($content);

            $testCases = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Failed to parse test cases from OpenAI response: ' . json_last_error_msg());
            }

            if (!isset($testCases['testCases']) || !is_array($testCases['testCases'])) {
                throw new \Exception('Invalid test case format from OpenAI');
            }

            return $testCases['testCases'];

        } catch (GuzzleException $e) {
            throw new \Exception('OpenAI API request failed: ' . $e->getMessage());
        }
    }

    private function buildPrompt(array $apiConfig): string
    {
        $method = $apiConfig['method'];
        $endpoint = $apiConfig['endpoint'];
        $authRequired = $apiConfig['authRequired'] ? 'Yes' : 'No';
        $authValue = $apiConfig['authValue'] ?? 'N/A';
        $requestBody = $apiConfig['requestBody'] ?? 'N/A';

        $prompt = <<<PROMPT
Generate comprehensive test cases for the following API endpoint:

**API Details:**
- HTTP Method: {$method}
- Endpoint: {$endpoint}
- Authentication Required: {$authRequired}
- Authentication: {$authValue}
- Request Body: {$requestBody}

**Requirements:**
1. Generate test cases covering:
   - Positive scenarios (happy path)
   - Negative scenarios (error cases)
   - Edge cases (boundary conditions)
   - Security scenarios (authentication, authorization, injection attacks)
   - Performance scenarios (if applicable)

2. Each test case must include:
   - id: Unique identifier (e.g., TC001, TC002)
   - scenario: Brief description of what is being tested
   - label: One of (Positive, Negative, Edge, Security, Performance)
   - steps: Step-by-step instructions to execute the test
   - requestBody: The request body to use (use "N/A" for GET/DELETE methods)
   - severity: One of (Critical, High, Medium, Low)
   - priority: One of (P0, P1, P2, P3)

3. Return ONLY valid JSON in this exact format (no markdown, no code blocks):
{
  "testCases": [
    {
      "id": "TC001",
      "scenario": "Test scenario description",
      "label": "Positive",
      "steps": "1. Step one\\n2. Step two\\n3. Step three",
      "requestBody": "N/A",
      "severity": "High",
      "priority": "P0"
    }
  ]
}

Generate at least 10-15 comprehensive test cases covering all scenarios.
PROMPT;

        return $prompt;
    }
}
