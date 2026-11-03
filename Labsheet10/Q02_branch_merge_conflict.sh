#!/bin/bash
# Q2: Feature branch, deliberate merge conflict, resolve, push to GitHub
cd myproject
git branch -M main

# 1. Create feature branch and change a file
git checkout -b feature/new-heading
echo "<h1>Feature Heading</h1>" > public/page.html
git add . && git commit -m "feat: heading from feature branch"

# 2. Change SAME line on main to create a conflict
git checkout main
echo "<h1>Main Heading</h1>" > public/page.html
git add . && git commit -m "feat: heading from main"

# 3. Merge -> CONFLICT
git merge feature/new-heading
# Git reports: CONFLICT (add/add) in public/page.html

# 4. Resolve: open file, remove <<<<<<< ======= >>>>>>> markers, keep desired content
cat > public/page.html <<'HTML'
<h1>Main + Feature Heading (resolved)</h1>
HTML
git add public/page.html
git commit -m "merge: resolve conflict between main and feature/new-heading"

# 5. Push to GitHub (create an empty repo on github.com first)
git remote add origin https://github.com/<your-username>/myproject.git
git push -u origin main
git push origin feature/new-heading
git branch -d feature/new-heading
