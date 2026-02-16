# 🎯 Getting Started with CI - Quick Guide

## What I Just Created

✅ **GitHub Actions CI Workflow** that automatically tests your code!

## Files Created

```
.github/
  └── workflows/
      ├── ci.yml           ← The main CI workflow
      └── README.md        ← Workflow documentation
CI_SETUP.md                ← Detailed CI guide
GETTING_STARTED_CI.md      ← This file
.gitignore                 ← Updated to exclude vendor/
```

---

## 🚀 How to Enable CI (3 Simple Steps)

### Step 1: Push to GitHub

If you haven't already, push your project to GitHub:

```bash
# Initialize git (if not already done)
git init

# Add all files
git add .

# Create first commit
git commit -m "Initial commit with CI setup"

# Add GitHub remote (replace with your repo URL)
git remote add origin https://github.com/YOUR_USERNAME/ai-api-test-case-generator.git

# Push to GitHub
git push -u origin main
```

### Step 2: CI Runs Automatically! 🎉

That's it! Once you push, GitHub Actions will automatically:
- ✅ Run all CI checks
- ✅ Build your Docker image
- ✅ Test your application

### Step 3: View Results

1. Go to your GitHub repository
2. Click the **"Actions"** tab
3. See your CI workflow running!

---

## 📊 What Happens on Every Push

```
You push code to GitHub
        ↓
CI automatically starts
        ↓
┌─────────────────────────────────┐
│  ✅ Validate composer.json      │
│  🔧 Install dependencies        │
│  🔍 Check PHP syntax            │
│  🏗️ Build Docker image          │
│  🚀 Start container             │
│  🏥 Health check                │
│  🧪 Verify app works            │
└─────────────────────────────────┘
        ↓
✅ All checks pass → Green ✓
❌ Something fails → Red ✗
```

---

## 🎨 Add Status Badge to README

Make your project look professional! Add this to the top of your `README.md`:

```markdown
# AI API Test Case Generator

![CI Status](https://github.com/YOUR_USERNAME/ai-api-test-case-generator/actions/workflows/ci.yml/badge.svg)
![Docker](https://img.shields.io/badge/docker-ready-blue)
![PHP](https://img.shields.io/badge/php-8.2-purple)

Your project description here...
```

Replace `YOUR_USERNAME` with your actual GitHub username.

---

## 🧪 Testing CI Locally (Before Pushing)

Want to test if CI will pass before pushing?

```bash
# 1. Validate Composer
composer validate --strict

# 2. Check PHP syntax
find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;

# 3. Build Docker
docker-compose build

# 4. Start and test
docker-compose up -d
curl http://localhost:8080

# 5. Check logs
docker-compose logs
```

If all these work locally, CI will pass! ✅

---

## 🔧 Customizing the CI

### Change which branches trigger CI

Edit `.github/workflows/ci.yml`:

```yaml
on:
  push:
    branches: [ main, develop, staging ]  # Add more branches
  pull_request:
    branches: [ main ]
```

### Skip CI for specific commits

Add `[skip ci]` to your commit message:

```bash
git commit -m "Update README [skip ci]"
```

---

## 📈 CI Performance

- **First run**: ~3-5 minutes
- **Later runs**: ~1-2 minutes (cached)

The CI caches:
- ✅ Composer dependencies
- ✅ Docker build layers

---

## 🎯 What CI Checks

| Check | What it does | Why it matters |
|-------|-------------|----------------|
| ✅ Composer validation | Checks dependency config | Prevents broken dependencies |
| 🔍 PHP syntax | Scans for syntax errors | Catches typos before deploy |
| 🏗️ Docker build | Builds the image | Ensures Dockerfile works |
| 🚀 Container start | Runs the container | Verifies app starts |
| 🏥 Health check | Tests HTTP endpoint | Confirms app responds |
| 🧪 Content check | Verifies HTML | Ensures correct page loads |

---

## 🐛 If CI Fails

### Don't Panic! Here's What to Do:

1. **Click on the failed workflow** in GitHub Actions
2. **Find the red ✗ step** that failed
3. **Read the error message**
4. **Fix the issue** based on the error
5. **Push again** - CI reruns automatically

### Common Issues:

**❌ "composer.json is invalid"**
```bash
# Fix: Validate locally
composer validate --strict
```

**❌ "Docker build failed"**
```bash
# Fix: Build locally to see error
docker build -t test .
```

**❌ "Container health check failed"**
```bash
# Fix: Test locally
docker-compose up
curl http://localhost:8080
docker-compose logs
```

---

## 🎓 Next Steps

Now that you have basic CI:

### Immediate:
1. ✅ Push your code to GitHub
2. ✅ Watch CI run for the first time
3. ✅ Add status badge to README

### Soon:
- 🧪 Add automated tests (PHPUnit)
- 🔒 Add security scanning
- 📊 Add code quality checks

### Later:
- 🚀 Set up automated deployment
- 🐳 Publish Docker images
- 🌍 Deploy to production

---

## 💡 Pro Tips

1. **Always check CI before merging PRs**
   - Green ✓ = safe to merge
   - Red ✗ = needs fixes

2. **Use branches for features**
   ```bash
   git checkout -b feature/new-feature
   # Make changes
   git push origin feature/new-feature
   # CI runs on the branch!
   ```

3. **CI runs on Pull Requests**
   - Create PR → CI runs automatically
   - See results directly in PR

---

## 📚 Learn More

- 📖 Detailed CI docs: See `CI_SETUP.md`
- 🔧 Workflow docs: See `.github/workflows/README.md`
- 🌐 GitHub Actions: https://docs.github.com/actions

---

## ✅ Checklist

Before pushing to GitHub:

- [ ] All files committed
- [ ] `.env` file NOT committed (check .gitignore)
- [ ] Code tested locally
- [ ] Docker builds successfully locally
- [ ] Ready to push!

```bash
git status                    # Check what will be committed
git add .                     # Add all files
git commit -m "Add CI setup"  # Commit with message
git push                      # Push and trigger CI!
```

---

**🎉 That's it! Your CI is ready to go!**

Push your code and watch the magic happen! ✨

