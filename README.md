# Oh My School

**One Platform. Unlimited School Checking Services.**

Oh My School is a dynamic student information lookup engine that allows schools to create and manage various self-service lookup services — without writing a single line of code for each new service.

Instead of building and maintaining separate applications for graduation status, exam results, admission selections, and other lookup needs, administrators can configure everything through a single admin panel: define the data structure, import records, and immediately publish a public search page.

## Features

- **Dynamic Services** — Create any number of lookup services (graduation, admission, student number, etc.) through the admin panel without code changes
- **Flexible Field Schema** — Define custom fields per service with types: Text, Date, Boolean, Link, and Attachment
- **Configurable Identifiers** — Set 1–5 identifier fields per service with OR mode (single input matching any identifier) or AND mode (all inputs must match)
- **Excel & CSV Import** — Bulk-import records with validation, duplicate detection, and per-row error reporting
- **Data Export** — Export records to Excel or CSV
- **Attachment Storage** — Support for local filesystem, S3-compatible storage, or database blob storage
- **Search Analytics** — Track total searches, success/failure rates, unique visitors, and peak hours
- **Rate Limiting** — Configurable max attempts per IP per service with temporary lockout
- **QR Code Generation** — Auto-generated QR codes for each service URL
- **End-to-End Encryption** — Optional RSA + AES encryption for public search payloads
- **Demo Mode** — Read-only public demo with auto-reset capability

## Requirements

- PHP 8.4
- MySQL 8.0+ or MariaDB 10.6+
- Composer 2
- Node.js 20+ with Bun

## Deployment

### Option 1: Manual (from source)

**1. Clone the repository**

```bash
git clone https://github.com/sooluh/oms.git
cd oms
```

**2. Install dependencies**

```bash
composer install --no-dev --optimize-autoloader
bun install
bun run build
```

**3. Configure environment**

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database credentials and other settings:

```env
APP_NAME="Oh My School"
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=oms
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

**4. Run migrations and seed initial data**

```bash
php artisan migrate
php artisan db:seed
```

**5. Log in to the admin panel**

The default admin credentials are:

- **Email:** `admin@oms.my.id`
- **Password:** `ohmyschool`

After logging in, go to your profile and change the password immediately.

**6. Set storage permissions and link**

```bash
chmod -R 775 storage bootstrap/cache
php artisan storage:link
```

**7. Serve the application**

For development:

```bash
php artisan serve
```

For production, point your web server (Nginx, Apache, or Caddy) to the `public/` directory and ensure `php-fpm` or equivalent is running.

---

### Option 2: Docker (single container)

The official image is available at `ghcr.io/sooluh/oms:latest`. It bundles PHP 8.4 (FrankenPHP), Caddy, and all application dependencies.

**1. Prepare your `.env` file**

```bash
cp .env.example .env
```

Update the relevant values — at minimum:

```env
APP_KEY=base64:your_generated_key_here
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=your_db_host
DB_PORT=3306
DB_DATABASE=oms
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

> Generate `APP_KEY` with: `php artisan key:generate --show`

**2. Run the container**

```bash
docker run -d \
  --name oms \
  --restart always \
  -p 8000:80 \
  --env-file .env \
  -v oms-storage:/app/storage \
  -v oms-cache:/app/bootstrap/cache \
  -v oms-tmp:/tmp \
  ghcr.io/sooluh/oms:latest
```

The container automatically runs migrations and caches configuration on startup via its entrypoint script.

**3. Log in to the admin panel**

The default admin credentials are:

- **Email:** `admin@oms.my.id`
- **Password:** `ohmyschool`

After logging in, go to your profile and change the password immediately.

---

### Option 3: Docker Compose

**1. Download the compose file**

```bash
curl -O https://raw.githubusercontent.com/sooluh/oms/main/compose.yml
```

Or use the `compose.yml` from this repository directly.

**2. Prepare your `.env` file**

```bash
cp .env.example .env
```

Edit the environment variables as described in Option 2. The container reads the `.env` file in the same directory as `compose.yml`.

**3. Start the stack**

```bash
docker compose up -d
```

**4. Log in to the admin panel**

The default admin credentials are:

- **Email:** `admin@oms.my.id`
- **Password:** `ohmyschool`

After logging in, go to your profile and change the password immediately.

**5. Stopping and updating**

```bash
# Stop
docker compose down

# Pull latest image and restart
docker compose pull && docker compose up -d
```

> The `compose.yml` exposes the app on port 80 by default. Place a reverse proxy (Nginx, Caddy, Traefik) in front of it to terminate TLS and serve over HTTPS.

---

## Contributing

Contributions are welcome. Please open an issue first to discuss what you would like to change, then submit a pull request against the `main` branch.

## License

This project is licensed under Apache 2.0 License.
