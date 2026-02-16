# 🚀 CI/CD Setup Guide

## Overview

This project uses GitHub Actions for Continuous Integration (CI). Every time you push code or create a pull request, the CI pipeline automatically validates your changes.

## What the CI Does

### Phase 1: Basic CI ✅

The CI workflow runs these checks on every push/PR:

1. **✅ Validate composer.json**
   - Ensures your Composer configuration is valid
   - Checks for syntax errors in dependencies

2. **🏗️ Build Docker image**
   - Builds the Docker image from your Dockerfile
   - Uses build cache for faster builds
   - Verifies the build completes successfully

3. **🏥 Run container health check**
   - Starts the Docker container
   - Waits for the application to be ready
   - Checks if the app responds to HTTP requests

4. **🧪 Verify the app starts successfully**
   - Tests the root endpoint (/)
   - Verifies HTML content is served correctly
   - Ensures "API Test Case Generator" title appears

## CI Workflow File

Location: `.github/workflows/ci.yml`

## How to View CI Results

### On GitHub:

1. Go to your repository on GitHub
2. Click the **"Actions"** tab
3. You'll see all workflow runs with status indicators:
   - ✅ Green check = All tests passed
   - ❌ Red X = Tests failed
   - 🟡 Yellow dot = Tests running

### Status Badge

Add this to your `README.md` to show the build status:

```markdown
![CI Status](https://github.com/YOUR_USERNAME/ai-api-test-case-generator/actions/workflows/ci.yml/badge.svg)
```

Replace `YOUR_USERNAME` with your GitHub username.

## What Triggers the CI

The CI runs automatically when:

- ✅ You push to `main` branch
- ✅ You push to `develop` branch
- ✅ Someone creates a Pull Request to `main` or `develop`

## Understanding the Build Process

### Step-by-step breakdown:

```
1. 📥 Checkout code
   └─ GitHub Actions downloads your repository

2. 🐘 Set up PHP 8.2
   └─ Installs PHP and required extensions

3. ✅ Validate composer.json
   └─ Checks if your dependencies are correctly defined

4. 🔧 Install Composer dependencies
   └─ Downloads all PHP packages (cached for speed)

5. 🔍 Check PHP syntax
   └─ Scans all .php files for syntax errors

6. 🐳 Set up Docker Buildx
   └─ Prepares Docker build environment

7. 🏗️ Build Docker image
   └─ Creates the Docker image (uses cache)

8. 🚀 Start Docker container
   └─ Runs your app in a container on port 8080

9. ⏳ Wait for container to be ready
   └─ Gives the app time to start (up to 30 seconds)

10. 🏥 Health check
    └─ Tests if the app responds to HTTP requests

11. 🧪 Verify HTML content
    └─ Checks if the correct page is served

12. 📊 Show logs (if needed)
    └─ Displays container logs for debugging

13. 🧹 Cleanup
    └─ Stops and removes the test container
```

## If CI Fails

### Common Issues:

#### ❌ Composer validation fails
- **Fix**: Check `composer.json` for syntax errors
- Run locally: `composer validate --strict`

#### ❌ Docker build fails
- **Fix**: Test Docker build locally
- Run: `docker build -t test .`

#### ❌ Container doesn't start
- **Fix**: Check Dockerfile and dependencies
- Run locally: `docker-compose up`

#### ❌ Health check fails
- **Fix**: Ensure the app starts on port 80 inside container
- Check Apache configuration

### Debugging Steps:

1. **Check the GitHub Actions logs**
   - Click on the failed workflow
   - Expand the failed step
   - Read the error messages

2. **Run the same commands locally**
   ```bash
   # Validate composer
   composer validate --strict

   # Build Docker image
   docker build -t test .

   # Run container
   docker run -d -p 8080:80 test

   # Test it
   curl http://localhost:8080
   ```

3. **Check container logs**
   ```bash
   docker logs <container-id>
   ```

## Local Testing Before Push

To test your changes before pushing:

```bash
# 1. Validate composer
composer validate --strict

# 2. Check PHP syntax
find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;

# 3. Build and test Docker
docker-compose build
docker-compose up -d
curl http://localhost:8080

# 4. Check logs
docker-compose logs
```

## CI Performance

- **First run**: ~3-5 minutes (downloads everything)
- **Subsequent runs**: ~1-2 minutes (uses cache)

The CI uses GitHub Actions cache to speed up:
- Composer dependencies
- Docker layer cache

## Next Steps (Phase 2)

Future CI improvements could include:

- 🧪 Automated testing (PHPUnit)
- 🔒 Security scanning
- 📊 Code quality checks (PHPStan)
- 🐳 Push Docker images to registry
- 🚀 Automated deployment

## Costs

✅ **FREE** - GitHub Actions is free for public repositories
- 2,000 minutes/month for private repos (free tier)

## Questions?

If the CI fails and you're not sure why:
1. Check the Actions tab on GitHub
2. Look at the step that failed
3. Read the error message
4. Try running the same command locally

---

**Built with ❤️ using GitHub Actions**
