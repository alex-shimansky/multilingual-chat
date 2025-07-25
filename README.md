# Laravel Multilingual Chat with WebSockets

This is a real-time multilingual chat application built with Laravel, supporting automatic message translation using Google Cloud Translate API and WebSocket broadcasting via Laravel Echo and Socket.io. The project is fully dockerized using Docker Compose for easy local development.

---

## Features

- User registration, login, and authentication (using Laravel + Livewire)
- Real-time chat with WebSocket support (Laravel Echo + Socket.io)
- Automatic message translation to users' native languages via Google Cloud Translate
- Toggle between original and translated message text
- Persistent chat history stored in MySQL
- Redis caching for broadcasting and queueing
- Adminer for database management
- Nginx web server for serving the Laravel app
- Frontend asset management with Vite and Node.js

---

## Technologies Used

- **Laravel** — PHP framework for backend
- **Livewire** — Laravel full-stack framework for dynamic interfaces
- **MySQL** — Relational database for data persistence
- **Redis** — Cache and broadcasting backend
- **Laravel Echo + Socket.io** — Real-time WebSocket communication
- **Google Cloud Translate API** — Automatic message translation
- **Nginx** — Web server
- **Node.js & NPM** — Frontend dependencies and build tools
- **Vite** — Frontend build tool for assets bundling and HMR
- **Docker & Docker Compose** — Containerization and orchestration

---

## Requirements

- **Docker**
- **Docker Compose**
- **Google Cloud account** with enabled **Translate API**
- Google Cloud Translate **credentials JSON file**
- Basic knowledge of Docker and Laravel for customization and debugging

---

## Prerequisites

- Docker and Docker Compose installed on your machine
- Google Cloud Translate API credentials JSON file placed at `storage/app/google/translate-key.json`

---

## Installation & Setup

1. **Clone the repository:**

```bash
git clone https://github.com/alex-shimansky/multilingual-chat.git
cd multilingual-chat
```

2. **Configure environment variables**

```bash
cp .env.example .env
```

Make sure to configure your database and other settings in .env if needed. The defaults work with Docker Compose setup.

3. **Place Google Translate credentials JSON**

Download your Google Cloud Translate API key JSON and save it to:

```bash
storage/app/google/translate-key.json
```

4. **Build and start Docker containers**

```bash
docker compose up -d --build
```

5. **Install Composer and NPM dependencies inside the app container**

```bash
docker compose exec app composer install
docker compose exec app npm install
docker compose exec app npm run build
```

6. **Generate application encryption key**

```bash
docker compose exec app php artisan key:generate
```

7. **Run database migrations**

```bash
docker compose exec app php artisan migrate
```

8. **Start the Laravel queue worker**

This is required for broadcasting and background jobs.

```bash
docker compose exec app php artisan queue:work --sleep=3 --tries=3
```

9. **Access the application**

Open your browser and visit:

http://localhost:8000

10. **Adminer database UI**

You can access the Adminer interface to view and manage the MySQL database at:

http://localhost:8080

---

## Project Structure

app/Http/Controllers/ — Controllers for handling chat messages and UI

resources/views/ — Blade templates for the chat UI

routes/web.php — Web routes for the application

docker/ — Docker configuration files

laravel-echo-server.json — Configuration for the Laravel Echo server (WebSocket)

vite.config.js — Frontend asset bundling config with Vite

storage/app/google/translate-key.json — Google Translate API credentials (not included in repo)

---

## Notes

The WebSocket server (laravel-echo-server) is configured to use Redis and listen on port 6001.

Make sure the Redis container is running for broadcasting to work.

The Google Cloud Translate API requires billing enabled on your Google Cloud project.

Frontend uses Laravel Vite plugin with HMR enabled for development convenience.

User native language is stored in the user profile and used to auto-translate incoming messages.
