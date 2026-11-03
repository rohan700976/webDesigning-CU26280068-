#!/bin/bash
# Q1: Initialize a Git repo, add .gitignore, commit in logical steps
mkdir myproject && cd myproject
git init
git config user.name "Your Name"
git config user.email "you@example.com"

cat > .gitignore <<'IGN'
# Dependencies
/vendor/
/node_modules/
# Environment / secrets
.env
config/local.php
# Logs & uploads
*.log
/logs/
/storage/uploads/
# OS / IDE
.DS_Store
Thumbs.db
.idea/
.vscode/
IGN

echo "# My Project" > README.md
git add .gitignore README.md
git commit -m "chore: add .gitignore and README"

mkdir -p public && echo "<?php echo 'Hello World';" > public/index.php
git add public/index.php
git commit -m "feat: add initial index page"

echo "<?php // db config" > config.php
git add config.php
git commit -m "feat: add configuration file"

git log --oneline
git status
