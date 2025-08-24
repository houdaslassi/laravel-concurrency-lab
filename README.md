# Laravel Concurrency Lab 🚦

This is a **demo package** for learning.  
It shows how **race conditions** can happen in Laravel queues, and how to stop them with **Locks** and **Funnels**.  

⚠️ **Note:** This package is not published on Packagist.  
It is for **education and testing**, not for production use.  

---

## 🛠 Install (local path)

1. Put this repo next to your Laravel project.  

```
/projects/laravel-app
/projects/laravel-concurrency-lab
```

2. In your Laravel app, open `composer.json` and add:  

```json
"repositories": [
  {
    "type": "path",
    "url": "../laravel-concurrency-lab"
  }
]
```

3. Require the package:

```bash
composer require "houdaslassi/laravel-concurrency-lab:dev-main" --dev
```

---

## 🚀 Usage

### 1. Start Redis

```bash
# Mac
brew services start redis

# Linux
sudo service redis-server start

# Docker
docker run -p 6379:6379 redis
```

### 2. Configure Environment

In your `.env`:

```env
CACHE_STORE=redis
QUEUE_CONNECTION=redis
REDIS_CLIENT=predis   # or phpredis if extension installed
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### 3. Start Workers

```bash
php artisan queue:work --queue=default --verbose
```

### 4. Run Demo Commands

```bash
php artisan race:demo --jobs=10 --mode=none     # no protection (race condition)
php artisan race:demo --jobs=10 --mode=lock     # one by one
php artisan race:demo --jobs=10 --mode=funnel   # limited parallel (5 at a time)
```

### 5. Check Results

Check results in `storage/logs/laravel.log`.

**Example log output with `--mode=funnel` (limit = 5):**

```
[deploy_1] Started Deploying...
[deploy_2] Started Deploying...
[deploy_3] Started Deploying...
[deploy_4] Started Deploying...
[deploy_5] Started Deploying...
...
[deploy_1] Finished Deploying...
[deploy_6] Started Deploying...
```

---

## 🎯 Why This Repo?

- To see race conditions in action (not just read theory)
- To learn difference between no lock, lock, and funnel

---

## 📌 Important

- This package is for **education only**
- Not a production solution
- Keep it simple, use it to learn, share with others

---

## 🔧 How It Works

The package provides a `RaceDemoCommand` that dispatches multiple `Deploy` jobs with different concurrency control modes:

- **`--mode=none`**: No protection - race conditions can occur
- **`--mode=lock`**: Uses Laravel's `Cache::lock()` - one job at a time
- **`--mode=funnel`**: Uses a custom funnel implementation - limited parallel execution

Each `Deploy` job simulates a deployment process with logging to demonstrate the different behaviors.

---

## 📁 Project Structure

```
laravel-concurrency-lab/
├── src/
│   ├── ConcurrencyLabServiceProvider.php
│   ├── Console/
│   │   └── RaceDemoCommand.php
│   └── Jobs/
│       └── Deploy.php
├── config/
│   └── concurrency-lab.php
└── composer.json
```

---

## 🤝 Contributing

This is an educational project. Feel free to:

- Fork and experiment
- Submit issues for bugs
- Suggest improvements

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
