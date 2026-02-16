<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\OpenAIService;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Start session for storing test cases
session_start();

$error = null;
$testCases = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'generate') {
        try {
            $apiConfig = [
                'method' => $_POST['method'] ?? 'GET',
                'endpoint' => $_POST['endpoint'] ?? '',
                'authRequired' => isset($_POST['authRequired']) && $_POST['authRequired'] === 'yes',
                'authValue' => $_POST['authValue'] ?? '',
                'requestBody' => $_POST['requestBody'] ?? ''
            ];

            // Validate input
            if (empty($apiConfig['endpoint'])) {
                throw new \Exception('Endpoint URL is required');
            }

            if ($apiConfig['authRequired'] && empty($apiConfig['authValue'])) {
                throw new \Exception('Authentication value is required');
            }

            if (in_array($apiConfig['method'], ['POST', 'PUT', 'PATCH']) && empty($apiConfig['requestBody'])) {
                throw new \Exception('Request body is required for ' . $apiConfig['method'] . ' method');
            }

            $openAI = new OpenAIService();
            $testCases = $openAI->generateTestCases($apiConfig);

            // Store in session for export
            $_SESSION['test_cases'] = $testCases;
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv' && isset($_SESSION['test_cases'])) {
    require_once __DIR__ . '/../src/CSVExporter.php';
    App\CSVExporter::export($_SESSION['test_cases']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Test Case Generator</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
            font-size: 16px;
        }

        .content {
            padding: 30px;
        }

        .form-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            min-height: 120px;
            font-family: 'Courier New', monospace;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 15px;
            align-items: end;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 100%;
            justify-content: center;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #28a745;
            color: white;
        }

        .btn-secondary:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .results {
            margin-top: 30px;
        }

        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .results-header h2 {
            color: #333;
            font-size: 24px;
        }

        .stats {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .stat-item {
            text-align: center;
            padding: 10px 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
        }

        .stat-label {
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }

        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            white-space: nowrap;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
            vertical-align: top;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        .label {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .label-positive {
            background: #d4edda;
            color: #155724;
        }

        .label-negative {
            background: #f8d7da;
            color: #721c24;
        }

        .label-edge {
            background: #fff3cd;
            color: #856404;
        }

        .label-security {
            background: #d1ecf1;
            color: #0c5460;
        }

        .label-performance {
            background: #e2e3e5;
            color: #383d41;
        }

        .severity {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .severity-critical {
            background: #dc3545;
            color: white;
        }

        .severity-high {
            background: #fd7e14;
            color: white;
        }

        .severity-medium {
            background: #ffc107;
            color: #333;
        }

        .severity-low {
            background: #6c757d;
            color: white;
        }

        .priority {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #667eea;
            color: white;
        }

        .code-block {
            background: #f4f4f4;
            padding: 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            max-width: 300px;
            overflow-x: auto;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .hidden {
            display: none;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .stats {
                flex-direction: column;
                gap: 10px;
            }

            .results-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 API Test Case Generator</h1>
            <p>Powered by OpenAI - Generate comprehensive test cases for your API endpoints</p>
        </div>

        <div class="content">
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Configuration Form -->
            <form method="POST" action="">
                <input type="hidden" name="action" value="generate">
                
                <div class="form-section">
                    <div class="section-title">🌐 API Endpoint Configuration</div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="method">HTTP Method</label>
                            <select name="method" id="method" required>
                                <option value="GET" <?php echo (isset($_POST['method']) && $_POST['method'] === 'GET') ? 'selected' : ''; ?>>GET</option>
                                <option value="POST" <?php echo (isset($_POST['method']) && $_POST['method'] === 'POST') ? 'selected' : ''; ?>>POST</option>
                                <option value="PUT" <?php echo (isset($_POST['method']) && $_POST['method'] === 'PUT') ? 'selected' : ''; ?>>PUT</option>
                                <option value="PATCH" <?php echo (isset($_POST['method']) && $_POST['method'] === 'PATCH') ? 'selected' : ''; ?>>PATCH</option>
                                <option value="DELETE" <?php echo (isset($_POST['method']) && $_POST['method'] === 'DELETE') ? 'selected' : ''; ?>>DELETE</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="endpoint">Endpoint URL</label>
                            <input type="text" name="endpoint" id="endpoint" placeholder="https://api.example.com/users" value="<?php echo htmlspecialchars($_POST['endpoint'] ?? ''); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-title">🔐 Authentication</div>
                    
                    <div class="form-group">
                        <label for="authRequired">Authentication Required?</label>
                        <select name="authRequired" id="authRequired">
                            <option value="no" <?php echo (isset($_POST['authRequired']) && $_POST['authRequired'] === 'no') ? 'selected' : ''; ?>>No</option>
                            <option value="yes" <?php echo (isset($_POST['authRequired']) && $_POST['authRequired'] === 'yes') ? 'selected' : ''; ?>>Yes</option>
                        </select>
                    </div>

                    <div class="form-group <?php echo (!isset($_POST['authRequired']) || $_POST['authRequired'] !== 'yes') ? 'hidden' : ''; ?>" id="authGroup">
                        <label for="authValue">Authentication (e.g., Bearer token)</label>
                        <input type="text" name="authValue" id="authValue" placeholder="Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..." value="<?php echo htmlspecialchars($_POST['authValue'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-section <?php echo (!isset($_POST['method']) || !in_array($_POST['method'], ['POST', 'PUT', 'PATCH'])) ? 'hidden' : ''; ?>" id="bodySection">
                    <div class="section-title">📝 Request Body</div>
                    
                    <div class="form-group">
                        <label for="requestBody">Request Body (JSON)</label>
                        <textarea name="requestBody" id="requestBody" placeholder='{"name": "John Doe","email": "john@example.com"}'><?php echo htmlspecialchars($_POST['requestBody'] ?? ''); ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <span>✨ Generate Test Cases</span>
                </button>
            </form>

            <!-- Results Section -->
            <?php if ($testCases): ?>
            <div class="results">
                <div class="results-header">
                    <h2>Generated Test Cases</h2>
                    <div class="stats">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo count($testCases); ?></div>
                            <div class="stat-label">Total Tests</div>
                        </div>
                        <a href="?export=csv" class="btn btn-secondary">
                            📥 Export to CSV
                        </a>
                    </div>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Scenario</th>
                                <th>Label</th>
                                <th>Steps</th>
                                <th>Request Body</th>
                                <th>Severity</th>
                                <th>Priority</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($testCases as $testCase): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($testCase['id'] ?? ''); ?></strong></td>
                                <td><?php echo htmlspecialchars($testCase['scenario'] ?? ''); ?></td>
                                <td>
                                    <span class="label label-<?php echo strtolower($testCase['label'] ?? 'positive'); ?>">
                                        <?php echo htmlspecialchars($testCase['label'] ?? ''); ?>
                                    </span>
                                </td>
                                <td style="white-space: pre-line;"><?php echo htmlspecialchars($testCase['steps'] ?? ''); ?></td>
                                <td>
                                    <?php if (isset($testCase['requestBody']) && $testCase['requestBody'] !== 'N/A'): ?>
                                        <div class="code-block"><?php echo htmlspecialchars($testCase['requestBody']); ?></div>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="severity severity-<?php echo strtolower($testCase['severity'] ?? 'low'); ?>">
                                        <?php echo htmlspecialchars($testCase['severity'] ?? ''); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="priority">
                                        <?php echo htmlspecialchars($testCase['priority'] ?? ''); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Show/hide authentication field
        document.getElementById('authRequired').addEventListener('change', function() {
            document.getElementById('authGroup').classList.toggle('hidden', this.value !== 'yes');
        });

        // Show/hide request body section based on method
        document.getElementById('method').addEventListener('change', function() {
            const method = this.value;
            document.getElementById('bodySection').classList.toggle('hidden', 
                !['POST', 'PUT', 'PATCH'].includes(method));
        });
    </script>
</body>
</html>
