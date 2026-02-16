# GitHub Actions Workflows

This directory contains automated workflows for CI/CD.

## Available Workflows

### 1. `ci.yml` - Continuous Integration

**Purpose**: Validates code quality and Docker builds on every push/PR

**Runs on**:
- Push to `main` or `develop` branches
- Pull requests to `main` or `develop` branches

**What it does**:
1. ✅ Validates composer.json
2. 🏗️ Builds Docker image
3. 🏥 Runs health checks
4. 🧪 Verifies app starts successfully

**Duration**: ~1-2 minutes (with cache)

---

## How to Use

### Viewing CI Results

1. Go to your GitHub repository
2. Click **"Actions"** tab
3. See all workflow runs and their status

### Adding Status Badge to README

```markdown
![CI](https://github.com/YOUR_USERNAME/REPO_NAME/actions/workflows/ci.yml/badge.svg)
```

---

## Workflow Secrets (if needed)

For future deployment workflows, you may need to add secrets:

1. Go to repository **Settings**
2. Click **Secrets and variables** → **Actions**
3. Click **New repository secret**
4. Add required secrets (e.g., `DOCKER_HUB_TOKEN`, `AWS_ACCESS_KEY`)

Currently, the CI workflow doesn't require any secrets.

---

## Troubleshooting

**If workflow fails:**

1. Click on the failed workflow run
2. Expand the failed step
3. Read error messages
4. Fix the issue locally
5. Push again

**Common fixes:**

- Composer issues: Run `composer validate` locally
- Docker issues: Run `docker build .` locally
- Syntax errors: Run `php -l yourfile.php`

---

## Future Workflows

Planned additions:
- Security scanning
- Code quality analysis
- Automated deployment
- Docker image publishing

---

**Last Updated**: 2026-02-16
