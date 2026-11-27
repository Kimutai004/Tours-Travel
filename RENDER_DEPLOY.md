# Deploying ToursTravel Kenya on Render (Docker)

This guide deploys the Laravel app to [Render](https://render.com) as a single
Docker web service (PHP-FPM + Nginx in one container, listening on `$PORT`).

## What changed

| File | Purpose |
|------|---------|
| `Dockerfile` | Rewritten for Render: builds Laravel Mix assets, installs Composer deps, runs PHP-FPM + Nginx in one container, binds Nginx to `$PORT` (default `10000`). |
| `docker/nginx.conf` | PHP-FPM now listens on `127.0.0.1:9000` (same container) instead of a separate `app` container. |
| `docker/start.sh` | Entrypoint: runs `migrate`, caches config/routes/views, binds Nginx to `$PORT`, starts PHP-FPM + Nginx. |
| `render.yaml` | Render blueprint (infrastructure-as-code) for one-click deploy. |
| `RENDER_DEPLOY.md` | This file. |

## Prerequisites

1. A GitHub account with this repo pushed.
2. A Render account (https://render.com).
3. A **PostgreSQL database**. Render offers managed PostgreSQL:
   - Render dashboard → **New +** → **PostgreSQL** → create a database.
   - Copy the connection details (host, port, database, user, password) from the Render DB dashboard.

## Step 1 — Generate `APP_KEY`

Laravel requires an `APP_KEY`. Generate one locally:

```bash
php artisan key:generate --show
```

Copy the output (e.g. `base64:xxxx...`). You will paste it into Render as the `APP_KEY` env var.

## Step 2 — Push to GitHub

```bash
git add -A
git commit -m "Add Render Docker deployment config"
git push origin main
```

## Step 3 — Create the Render web service

### Option A: Blueprint (recommended)

1. Log in to Render → **New +** → **Blueprint**.
2. Connect your GitHub repo and select `render.yaml`.
3. Render will pre-fill the service. Click **Apply**.
4. In the Render dashboard, go to the service → **Environment** and replace all placeholder values (marked `REPLACE ME`) with real values:
   - `APP_KEY` — from Step 1
   - `APP_URL` — `https://<your-service>.onrender.com`
   - `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
   - `MAIL_*`, `STRIPE_*`, `MPESA_*` as needed
   - Mark secrets (passwords/API keys) as **Secret** so they aren't shown in plain text.

### Option B: Manual Web Service

1. Render → **New +** → **Web Service**.
2. Select your repo, branch (`main`).
3. Runtime: **Docker**.
4. Dockerfile path: `./Dockerfile`.
5. Add the same environment variables above.
6. **Advanced → Health check path:** `/` (optional).
7. Click **Create Web Service**.

## Step 4 — Provision the database first (important)

The entrypoint runs `php artisan migrate --force` on every deploy. Make sure your MySQL database is created and reachable **before** you create/deploy the Render web service, otherwise the first deploy will fail while connecting to the DB.

## Step 5 — Persistent storage for uploads (optional)

The `ImageService` stores uploaded images in `storage/app/public`. Render's container filesystem is ephemeral — uploads are lost on redeploy unless you add a **Persistent Disk**:

1. Render dashboard → your service → **Disks** → **Add Disk**.
   - Name: `tours-travel-storage`
   - Mount path: `/var/www/html/storage`
   - Size: 1 GB
2. (Optional) Uncomment the `disks:` block in `render.yaml` to document it.

## Environment variables cheat-sheet (set all in Render → Environment)

```text
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...                    # from `php artisan key:generate --show`
APP_URL=https://<service>.onrender.com

DB_CONNECTION=pgsql
DB_HOST=<your-postgres-host>
DB_PORT=5432
DB_DATABASE=<database>
DB_USERNAME=<user>
DB_PASSWORD=<password>

SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=database
LOG_CHANNEL=stack

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tourskenya.com
MAIL_FROM_NAME="ToursTravel Kenya"

STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...

MPESA_CONSUMER_KEY=...
MPESA_CONSUMER_SECRET=...
MPESA_BUSINESS_SHORT_CODE=...
MPESA_PASSKEY=...
MPESA_CALLBACK_URL=https://<service>.onrender.com/api
MPESA_PRODUCTION=false
```

## Troubleshooting

- **Deploy fails at `migrate`:** The DB isn't reachable yet. Verify `DB_*` vars and that the database server allows connections from Render's IPs. Check Render service logs.
- **502 / site not loading:** Confirm Nginx is binding to `$PORT`. The `start.sh` rewrites `listen 80;` → `listen ${PORT};`. Render health-checks the port it assigns.
- **Missing CSS/images:** Assets are built during `docker build` via `npm run production`. If they're missing, check the build logs in Render.
- **`APP_KEY` missing:** You'll get an encryption error. Set `APP_KEY` in Render and redeploy.
- **File uploads vanish after redeploy:** Add a Persistent Disk mounted at `/var/www/html/storage` (see Step 5).

## Local Docker test (optional)

If you have Docker Desktop locally:

```bash
docker build -t tours-travel .
docker run -p 10000:10000 \
  -e APP_ENV=production -e APP_DEBUG=false \
  -e APP_KEY=base64:... \
  -e DB_HOST=... -e DB_DATABASE=... -e DB_USERNAME=... -e DB_PASSWORD=... \
  tours-travel
# Visit http://localhost:10000
```

Then run `docker run ... php artisan migrate --seed` against a throwaway container to seed the database.
