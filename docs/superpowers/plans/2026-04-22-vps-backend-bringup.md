# VPS Backend Bring-Up Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Turn `backend/` into real Laravel app that can run from Ubuntu VPS with Nginx + MySQL, then support repeatable updates with `git pull`.

**Architecture:** Use VPS as canonical Laravel runtime because local machine cannot run PHP/Composer/Docker. First restore missing Laravel base files inside tracked `backend/`, then replace mock auth and mock AYAN payloads with real Sanctum + MySQL persistence. After that, VPS deploy flow becomes `git pull` + Composer + migrations + cache refresh.

**Tech Stack:** Ubuntu, Nginx, MySQL, PHP-FPM, Composer, Laravel 11, Laravel Sanctum

---

## File Structure

**Existing custom backend files that must be preserved:**
- `backend/app/Http/Controllers/AuthController.php`
- `backend/app/Http/Controllers/UserController.php`
- `backend/app/Http/Controllers/Ayan/TripController.php`
- `backend/app/Http/Controllers/Ayan/RequestController.php`
- `backend/app/Http/Controllers/Ayan/ResponseController.php`
- `backend/app/Http/Controllers/Ayan/MyController.php`
- `backend/app/Models/User.php`
- `backend/app/Models/Trip.php`
- `backend/app/Models/AyanRequest.php`
- `backend/app/Models/AyanResponse.php`
- `backend/routes/api.php`
- `backend/database/migrations/2026_04_22_000001_create_users_table.php`
- `backend/database/migrations/2026_04_22_000002_create_trips_table.php`
- `backend/database/migrations/2026_04_22_000003_create_requests_table.php`
- `backend/database/migrations/2026_04_22_000004_create_responses_table.php`

**Missing Laravel runtime files that must be created and then committed to git:**
- `backend/artisan`
- `backend/composer.json`
- `backend/bootstrap/app.php`
- `backend/config/*.php`
- `backend/public/index.php`
- `backend/routes/console.php`
- `backend/app/Providers/*`
- `backend/storage/*`
- `backend/resources/*`
- `backend/tests/*`

**Server-only files that must never be committed:**
- `backend/.env`
- `backend/vendor/*`
- `backend/storage/logs/*`
- DB data

---

## Chunk 1: Server Bootstrap

### Task 1: Install VPS Packages

**Files:**
- Create: system packages on VPS

- [ ] **Step 1: Install base packages**

Run:

```bash
sudo apt update
sudo apt install -y nginx mysql-server git unzip curl rsync composer php php-cli php-fpm php-mysql php-mbstring php-xml php-curl php-zip php-bcmath php-intl
```

- [ ] **Step 2: Verify tools exist**

Run:

```bash
php -v
composer --version
mysql --version
nginx -v
```

Expected:
- PHP `8.2+`
- Composer installed
- MySQL client installed
- Nginx installed

- [ ] **Step 3: Record PHP-FPM service name**

Run:

```bash
systemctl list-units --type=service | grep php.*fpm
```

Expected:
- one service like `php8.3-fpm.service` or `php8.2-fpm.service`

### Task 2: Clone Repo on VPS

**Files:**
- Create: `/var/www/iindiinda-app`

- [ ] **Step 1: Create app directory**

Run:

```bash
sudo mkdir -p /var/www
sudo chown -R $USER:$USER /var/www
cd /var/www
```

- [ ] **Step 2: Clone repo and checkout working branch**

Run:

```bash
git clone <YOUR_REPO_URL> iindiinda-app
cd /var/www/iindiinda-app
git checkout front/ayan
git status --short --branch
```

Expected:
- branch `front/ayan`
- clean worktree before bootstrap

---

## Chunk 2: Restore Laravel Runtime in `backend/`

### Task 3: Generate Real Laravel Base Without Overwriting Custom AYAN Files

**Files:**
- Create: `backend/artisan`
- Create: `backend/composer.json`
- Create: `backend/bootstrap/app.php`
- Create: `backend/config/*.php`
- Create: `backend/public/index.php`
- Modify: tracked `backend/` by adding missing framework files only

- [ ] **Step 1: Confirm current repo still lacks runtime base**

Run:

```bash
test -f backend/composer.json || echo missing-composer
test -f backend/artisan || echo missing-artisan
test -f backend/bootstrap/app.php || echo missing-bootstrap
```

Expected:
- missing markers print now

- [ ] **Step 2: Generate temporary clean Laravel app**

Run:

```bash
composer create-project laravel/laravel /tmp/iind-laravel '^11.0'
```

Expected:
- `/tmp/iind-laravel` contains full Laravel base

- [ ] **Step 3: Copy only missing runtime files into tracked `backend/`**

Run:

```bash
rsync -a \
  --exclude '.env' \
  --exclude 'vendor' \
  --exclude 'app/Http' \
  --exclude 'app/Models' \
  --exclude 'routes/api.php' \
  --exclude 'database/migrations' \
  /tmp/iind-laravel/ /var/www/iindiinda-app/backend/
```

Reason:
- keep custom AYAN controllers, models, routes, migrations
- fill only missing Laravel runtime base

- [ ] **Step 4: Remove temporary Laravel app**

Run:

```bash
rm -rf /tmp/iind-laravel
```

- [ ] **Step 5: Verify missing files now exist**

Run:

```bash
test -f backend/composer.json && echo composer-ok
test -f backend/artisan && echo artisan-ok
test -f backend/bootstrap/app.php && echo bootstrap-ok
find backend/config -maxdepth 1 -type f | sort
```

Expected:
- `composer-ok`
- `artisan-ok`
- `bootstrap-ok`
- normal Laravel config files listed

### Task 4: Commit Laravel Runtime Base Back to Git

**Files:**
- Modify: git index with new tracked Laravel base files

- [ ] **Step 1: Review generated changes**

Run:

```bash
git status --short
```

Expected:
- new Laravel base files under `backend/`
- no `.env`
- no `vendor/`

- [ ] **Step 2: Commit missing runtime base**

Run:

```bash
git add backend
git reset backend/.env 2>/dev/null || true
git commit -m "feat(backend): restore laravel runtime base"
git push origin front/ayan
```

Reason:
- future VPS updates from git only work if Laravel base is tracked in repo

---

## Chunk 3: Database and App Config

### Task 5: Create MySQL Database and User

**Files:**
- Create: MySQL schema and DB user on VPS

- [ ] **Step 1: Create DB and DB user**

Run:

```sql
CREATE DATABASE iind CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'iind'@'127.0.0.1' IDENTIFIED BY 'CHANGE_ME_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON iind.* TO 'iind'@'127.0.0.1';
FLUSH PRIVILEGES;
```

Execute with:

```bash
sudo mysql
```

- [ ] **Step 2: Verify DB login works**

Run:

```bash
mysql -u iind -p -h 127.0.0.1 -e "SHOW DATABASES;"
```

Expected:
- `iind` listed

### Task 6: Create Production `.env`

**Files:**
- Create: `backend/.env`
- Modify: `backend/.env.example` later if new vars are required

- [ ] **Step 1: Create env file**

Run:

```bash
cd /var/www/iindiinda-app/backend
cp .env.example .env
```

- [ ] **Step 2: Fill required env values**

Set:

```env
APP_NAME=iind
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=iind
DB_USERNAME=iind
DB_PASSWORD=CHANGE_ME_STRONG_PASSWORD
```

- [ ] **Step 3: Generate app key**

Run:

```bash
php artisan key:generate
```

Expected:
- `APP_KEY` added to `.env`

---

## Chunk 4: Make Auth Real

### Task 7: Install and Wire Sanctum

**Files:**
- Modify: `backend/composer.json`
- Modify: `backend/app/Models/User.php`
- Modify: `backend/bootstrap/app.php`
- Modify: `backend/config/auth.php`
- Modify: `backend/app/Http/Controllers/AuthController.php`
- Modify: `backend/app/Http/Controllers/UserController.php`

- [ ] **Step 1: Install Sanctum**

Run:

```bash
cd /var/www/iindiinda-app/backend
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

- [ ] **Step 2: Add token support on `User` model**

Change `backend/app/Models/User.php`:
- add `use Laravel\Sanctum\HasApiTokens;`
- use `HasApiTokens`
- make model extend authenticatable Laravel base, not plain data holder

- [ ] **Step 3: Make `/api/auth/telegram` issue real token**

Change `backend/app/Http/Controllers/AuthController.php`:
- stop returning `mock_token_*`
- validate incoming `init_data`
- find or create user by Telegram identity
- issue token with `$user->createToken('telegram')->plainTextToken`
- return token plus serialized user

- [ ] **Step 4: Make `/api/user` and `/api/user/switch-role` use authenticated user**

Change `backend/app/Http/Controllers/UserController.php`:
- replace hardcoded mock user
- use `$request->user()`
- persist role change to DB

- [ ] **Step 5: Run auth migrations**

Run:

```bash
php artisan migrate
```

Expected:
- Sanctum tables created
- existing AYAN tables created

---

## Chunk 5: Replace AYAN Mock Payloads With Persistence

### Task 8: Convert AYAN Controllers to Eloquent

**Files:**
- Modify: `backend/app/Http/Controllers/Ayan/TripController.php`
- Modify: `backend/app/Http/Controllers/Ayan/RequestController.php`
- Modify: `backend/app/Http/Controllers/Ayan/ResponseController.php`
- Modify: `backend/app/Http/Controllers/Ayan/MyController.php`
- Modify: `backend/app/Models/Trip.php`
- Modify: `backend/app/Models/AyanRequest.php`
- Modify: `backend/app/Models/AyanResponse.php`

- [ ] **Step 1: Replace in-memory sample arrays with DB queries**

Implement:
- `TripController@index` -> query open trips with optional `from`, `to`, `date`
- `TripController@store` -> create trip for authenticated user
- `TripController@show` -> load trip with driver and responses
- `TripController@update` -> update only owner trip

- [ ] **Step 2: Do same for requests**

Implement:
- `RequestController@index`
- `RequestController@store`
- `RequestController@show`
- `RequestController@update`

- [ ] **Step 3: Do same for responses**

Implement:
- `ResponseController@tripIndex`
- `ResponseController@requestIndex`
- `ResponseController@tripStore`
- `ResponseController@requestStore`
- `ResponseController@update`
- `ResponseController@destroy`

- [ ] **Step 4: Make `MyController` query authenticated user data**

Implement:
- `trips()` -> user-owned trips
- `requests()` -> user-owned requests
- `responses()` -> user-created responses

- [ ] **Step 5: Keep response shape stable for frontend**

All protected AYAN endpoints must return:

```json
{
  "success": true,
  "data": []
}
```

or single-object `data` where frontend expects object.

---

## Chunk 6: Runtime Verification

### Task 9: Verify Laravel Boots and Routes Exist

**Files:**
- Verify: `backend/routes/api.php`

- [ ] **Step 1: Install backend dependencies**

Run:

```bash
cd /var/www/iindiinda-app/backend
composer install
```

- [ ] **Step 2: Confirm app boots**

Run:

```bash
php artisan about
php artisan route:list
```

Expected routes include:
- `POST /api/auth/telegram`
- `GET /api/user`
- `GET /api/ayan/trips`
- `POST /api/ayan/trips`
- `GET /api/ayan/requests`
- `POST /api/ayan/requests`
- `POST /api/ayan/trips/{id}/responses`
- `POST /api/ayan/requests/{id}/responses`
- `GET /api/ayan/my/trips`
- `GET /api/ayan/my/requests`
- `GET /api/ayan/my/responses`

Must not exist:
- `/api/ayan/orders`

### Task 10: Smoke Test API

**Files:**
- Verify: running backend app

- [ ] **Step 1: Start local VPS test server once**

Run:

```bash
cd /var/www/iindiinda-app/backend
php artisan serve --host=127.0.0.1 --port=8000
```

- [ ] **Step 2: Health check**

Run from second shell:

```bash
curl http://127.0.0.1:8000/api/health
```

Expected:

```json
{"status":"ok","version":"0.1.0"}
```

- [ ] **Step 3: Login and capture token**

Run:

```bash
curl -X POST http://127.0.0.1:8000/api/auth/telegram \
  -H "Content-Type: application/json" \
  -d '{"init_data":"test"}'
```

Expected:
- non-mock token
- real user payload

- [ ] **Step 4: Test protected endpoints**

Run:

```bash
curl http://127.0.0.1:8000/api/user -H "Authorization: Bearer TOKEN"
curl http://127.0.0.1:8000/api/ayan/trips -H "Authorization: Bearer TOKEN"
curl -X POST http://127.0.0.1:8000/api/ayan/trips \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"from_address":"Якутск","to_address":"Намцы","date":"2026-04-23","time":"09:00","seats":3,"price":500}'
```

Expected:
- responses match frontend shape `{ success, data }`

---

## Chunk 7: Nginx + PHP-FPM

### Task 11: Expose Backend Through Nginx

**Files:**
- Create: `/etc/nginx/sites-available/iind-backend`
- Create: `/etc/nginx/sites-enabled/iind-backend` symlink

- [ ] **Step 1: Create Nginx site config**

Use this template and replace PHP-FPM socket version if needed:

```nginx
server {
    listen 80;
    server_name api.example.com;

    root /var/www/iindiinda-app/backend/public;
    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

- [ ] **Step 2: Enable site and validate config**

Run:

```bash
sudo ln -s /etc/nginx/sites-available/iind-backend /etc/nginx/sites-enabled/iind-backend
sudo nginx -t
sudo systemctl reload nginx
```

- [ ] **Step 3: Set writable permissions for Laravel**

Run:

```bash
cd /var/www/iindiinda-app/backend
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
```

- [ ] **Step 4: Restart PHP-FPM**

Run with detected version:

```bash
sudo systemctl restart php8.3-fpm
```

---

## Chunk 8: Repeatable Deploy Workflow

### Task 12: Standard Update Procedure for Future `git pull`

**Files:**
- Optionally create later: `backend/deploy.sh`

- [ ] **Step 1: Use this deploy sequence on each update**

Run:

```bash
cd /var/www/iindiinda-app
git pull --ff-only origin front/ayan
cd backend
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
sudo systemctl reload nginx
sudo systemctl reload php8.3-fpm
```

- [ ] **Step 2: Verify app after each deploy**

Run:

```bash
curl http://127.0.0.1/api/health
php artisan route:list | grep ayan
```

Expected:
- health endpoint works
- AYAN routes still present

- [ ] **Step 3: Add simple deploy script after backend is stable**

Create later:
- `backend/deploy.sh`

Script should do only:
- `composer install --no-dev --optimize-autoloader`
- `php artisan migrate --force`
- `php artisan optimize:clear`
- `php artisan optimize`

---

## Notes

1. Until Sanctum and Eloquent persistence replace mock logic, VPS is staging only, not real production.
2. One-time Laravel bootstrap must be committed back to repo. Otherwise next fresh server will be broken again.
3. Do not commit `backend/.env` or `backend/vendor`.
4. After backend is verified, frontend can switch `frontend/app/config/api.config.ts` from mock to real API.

Plan complete and saved to `docs/superpowers/plans/2026-04-22-vps-backend-bringup.md`. Ready to execute.
