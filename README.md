# Translator API

A Laravel API that accepts user input, queues a translation job, sends it to the OpenAI Responses API, and stores the translated JSON.

---

## Project Notes

1. **Custom REST Facade**  
   A custom `Rest` facade is included to standardize all JSON API responses across the application.  
   This keeps controller methods clean and ensures consistent return formats for success, errors, and custom responses.

2. **Submission Status Tracking**  
   Submissions include a `status` field for better monitoring.
    - New submissions start as **pending**.
    - Once the queued job runs, they are marked **completed** or **failed**.
    - If a job fails, the error message is stored in the database for easier debugging.

---

## Setup

To run this project locally:

1. **Install dependencies**  
   Make sure you have PHP 8.2+, Composer, and Redis installed. Then:

   ```bash
   composer install
   ```

2. **Configure environment**  
   Copy the example env file and generate the app key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Update `.env` with your DB + Redis connection, and your OpenAI key:

   ```dotenv
   DB_CONNECTION=mysql   # or sqlite
   QUEUE_CONNECTION=redis

   OPENAI_API_KEY=your_api_key_here
   OPENAI_MODEL=gpt-5-mini
   ```

3. **Run migrations & seeders**

   ```bash
   php artisan migrate --seed
   ```

4. **Start the app**

   ```bash
   php artisan serve
   php artisan queue:work
   ```

---

## API Endpoints

### Create a submission

**POST** `https://translator.test/api/submissions`

**Payload:**

```json
{
  "name": "Ali Rahgoshay",
  "title": "Testing API",
  "description": "I'm using postman to send a request",
  "target_lang": "fr"
}
```

Response will be `202 Accepted` with the submission object.  
The translation runs in the background.

---

### Fetch a submission

**GET** `https://translator.test/api/submissions/44`

**Response:**

```json
{
  "data": {
    "id": 45,
    "status": "completed",
    "base_lang": "en",
    "target_lang": "fr",
    "original": {
      "name": "Ali Rahgoshay",
      "title": "Testing API",
      "description": "I'm using postman to send a request"
    },
    "translated": {
      "name": "Ali Rahgoshay",
      "title": "Test de l'API",
      "description": "J'utilise Postman pour envoyer une requête."
    },
    "error": null,
    "created_at": "2025-09-03T14:24:51.000000Z"
  }
}
```

---

## Monitoring & Debugging

For simplicity this project doesn’t include additional monitoring, but in real-world apps it’s highly recommended to add:

- **Laravel Telescope** → for real-time monitoring of requests, jobs, exceptions.
- **Laravel Pulse** → for performance insights and bottleneck detection.

Both integrate seamlessly with Laravel and make debugging queues much easier.

---

## Running Tests

Tests are written with **Pest** and use Saloon’s `MockClient` to fake OpenAI responses.

Run all tests:

```bash
php artisan test
```

Or directly with Pest:

```bash
./vendor/bin/pest
```

---
