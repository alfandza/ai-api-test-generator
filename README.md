# API Test Case Generator

A comprehensive API test case generator powered by OpenAI that automatically generates functional, negative, security, edge case, and performance test cases for your API endpoints.

## Features

- 🚀 **Automated Test Generation** - Generate 15-20 comprehensive test cases automatically
- 🔐 **Secure Configuration** - API keys stored in environment variables
- 🐳 **Docker Ready** - Fully containerized with Docker and Docker Compose
- 📊 **Multiple Test Types**:
  - Functional Testing (Positive scenarios)
  - Negative Testing (Invalid inputs, missing fields)
  - Security Testing (Authentication, authorization, injection attacks)
  - Edge Cases (Boundary values, special characters)
  - Performance Testing (Load, timeout scenarios)
- 📥 **CSV Export** - Export all test cases to CSV format
- 🎨 **Modern UI** - Clean, responsive interface

## Prerequisites

- Docker and Docker Compose
- OpenAI API Key

## Installation

### 1. Clone or Download the Project

```bash
git clone <your-repo-url>
cd api-test-generator
```

### 2. Set Up Environment Variables

Copy the example environment file and configure it:

```bash
cp .env.example .env
```

Edit `.env` and add your OpenAI API key:

```env
OPENAI_API_KEY=sk-your-actual-openai-api-key-here
OPENAI_MODEL=gpt-4
OPENAI_MAX_TOKENS=3000
OPENAI_TEMPERATURE=0.7
```

### 3. Build and Run with Docker

```bash
# Build the Docker image
docker-compose build

# Start the container
docker-compose up -d
```

The application will be available at: `http://localhost:8080`

### 4. Access the Application

Open your browser and navigate to:
```
http://localhost:8080
```

## Usage

### Generating Test Cases

1. **Configure API Endpoint**
   - Select HTTP Method (GET, POST, PUT, PATCH, DELETE)
   - Enter your Endpoint URL

2. **Authentication** (Optional)
   - Select "Yes" if authentication is required
   - Enter your Bearer token or other auth value

3. **Request Body** (For POST/PUT/PATCH)
   - The request body field appears automatically for POST, PUT, and PATCH methods
   - Enter your JSON request body

4. **Generate**
   - Click "Generate Test Cases"
   - Wait for the AI to generate comprehensive test cases

5. **Export**
   - Click "Export to CSV" to download all test cases

## Project Structure

```
api-test-generator/
├── public/
│   └── index.php           # Main application file
├── src/
│   ├── OpenAIService.php   # OpenAI API integration
│   └── CSVExporter.php     # CSV export functionality
├── .env.example            # Environment variables template
├── .gitignore             # Git ignore file
├── composer.json          # PHP dependencies
├── Dockerfile            # Docker configuration
├── docker-compose.yml    # Docker Compose configuration
└── README.md            # This file
```

## Docker Commands

```bash
# Start the application
docker-compose up -d

# Stop the application
docker-compose down

# View logs
docker-compose logs -f

# Rebuild after changes
docker-compose up -d --build

# Access container shell
docker-compose exec app bash
```

## Test Case Structure

Each generated test case includes:

| Field | Description |
|-------|-------------|
| **ID** | Unique test identifier (TC001, TC002, etc.) |
| **Scenario** | Clear description of what is being tested |
| **Label** | Test type: Positive, Negative, Edge, Security, Performance |
| **Steps** | Detailed steps to execute the test |
| **Request Body** | JSON request body if applicable, or "N/A" |
| **Severity** | Impact level: Critical, High, Medium, Low |
| **Priority** | Execution priority: P1, P2, P3 |

## Example

### Input
- **Method**: POST
- **Endpoint**: https://api.example.com/users
- **Auth**: Yes (Bearer token)
- **Request Body**: 
```json
{
  "name": "John Doe",
  "email": "john@example.com"
}
```

### Output
The tool will generate test cases including:
- Valid user creation with all required fields (Positive)
- Missing required fields (Negative)
- Invalid email format (Negative)
- SQL injection in name field (Security)
- Empty string values (Edge)
- Concurrent user creation (Performance)
- And many more...

## Troubleshooting

### Docker Issues

If port 8080 is already in use, edit `docker-compose.yml`:
```yaml
ports:
  - "8081:80"  # Change 8080 to any available port
```

### OpenAI API Errors

- Verify your API key is correct in `.env`
- Check your OpenAI account has available credits
- Ensure the model name is correct (default: gpt-4)

### Composer Dependencies

If you need to install dependencies manually:
```bash
docker-compose exec app composer install
```

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `OPENAI_API_KEY` | Your OpenAI API key | Required |
| `OPENAI_MODEL` | OpenAI model to use | gpt-4 |
| `OPENAI_MAX_TOKENS` | Maximum tokens for response | 3000 |
| `OPENAI_TEMPERATURE` | Response creativity (0-1) | 0.7 |

## Security Notes

- Never commit `.env` file to version control
- Keep your OpenAI API key confidential
- The `.gitignore` file is configured to exclude sensitive files
- API keys are only stored server-side, never exposed to the client

## Development

To modify the application:

1. Make changes to PHP files
2. Refresh the browser (PHP files are mounted as volumes)
3. For dependency changes, rebuild the container

## License

MIT License - Feel free to use and modify as needed.

## Support

For issues or questions, please open an issue in the repository.
