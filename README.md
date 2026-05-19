# Codecraft Homepage

This repository hosts a PHP-based wallet recovery demo with admin review pages.

## Local setup

1. Open PowerShell in `C:\Users\segun\OneDrive\Desktop\Codecraft Homepage`
2. Run:
   ```powershell
   php -S localhost:8000
   ```
3. Open the app:
   - `http://localhost:8000/dapps.html`
   - `http://localhost:8000/admin.php`

## GitHub + Render deployment

1. Initialize Git and commit your project:
   ```powershell
   git init
   git add .
   git commit -m "Initial commit"
   ```
2. Push to GitHub:
   - create a repository on GitHub
   - add the remote and push:
     ```powershell
     git remote add origin https://github.com/<your-username>/<repo>.git
     git branch -M main
     git push -u origin main
     ```
3. On Render:
   - create a new Web Service
   - connect your GitHub repo
   - select the `main` branch
   - the `render.yaml` file will configure:
     - `env: php`
     - `startCommand: php -S 0.0.0.0:$PORT`
   - deploy

## Notes

- `api/recovery-data.json` is ignored by `.gitignore` for safety.
- Keep the project root as the web root so `api/recover.php` and `api/recovery-download.php` resolve correctly.
- Do not commit real secrets into Git.
