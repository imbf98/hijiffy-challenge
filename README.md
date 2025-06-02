# HiJiffy Calendar Booking Challenge

## Overview
A Laravel-based api to ingest property rooms availabilities and verify data existence for an availability.

## Features
- API
- Dialogflow Chatbot Integration
- Cache and Rate Limiter system

## Tech Stack
- PHP 8.2
- Laravel 12.0
- MySQL
- Redis (via Predis)

## Requirements
- PHP 8.2 or higher
- Composer
- Node.js and NPM
- MySQL database
- Redis server (for caching)

## Installation

1. Clone the repository
```bash
git clone https://github.com/imbf98/hijiffy-challenge.git
cd hijiffy-challenge
```

2. Install PHP dependencies
```bash
composer install
```

3. Install frontend dependencies
```bash
npm install
```

4. Configure your environment
```bash
cp .env.example .env
php artisan key:generate
```

5. Set up your database in the `.env` file
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hijiffy_calendar
DB_USERNAME=root
DB_PASSWORD=
```

6. Set up redis client in the `.env` file - for this challenge i used predis
```
CACHE_STORE=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_CACHE_DB=0
REDIS_PORT=6379
```

7. Run migrations and user Seeder to create a sample user
```bash
php artisan migrate
php artisan db:seed --class=UserSeeder
```

8. Start the development server
```bash
php artisan serve
```

## API Endpoints

### Authentication
- `POST /api/token` - Retrieves the bearer token to use all over the API 
    Request Body:
     ```json
    {
        "email": "test@example.com",
        "password": "password"
    }
    ```

### Availability
- `GET /api/availability` - Verifies availability for a property based on query parameters like property_id, check_in, check_out and guests
- `POST /api/availability` - Data ingestion for room availability based for a given property

### Dialogflow
- `GET /api/dialogflow/webhook` - Dialogflow agent handler endpoint

## Testing
Run the test suite with:
```bash
php artisan test
```

## Architecture and System Design decisions

### 1. Service layer Pattern Architecture
So for this challenge i decided to take abroad a Service layer Pattern where i separate logic by:

Controllers - just handles the request and sends data to services
Form Requests - For input validation and sanitization
Services - Implements the logic with interfaces for dependency injection
Models - The database entities 
Interfaces - Define contract for services, for testability and loose coupling

In the DialogFlowController i decided not to use the service layer just because it was a simple controller where is injected the AvailabilityService binded with his interface and for this challenge could have been a little "overkill"

Other approaches that could be use:
Service Repository pattern
Action pattern

### 2. API Authentication

For authentication i decided to use sanctum just because of the simplicity, security, its lightweight and have a good perfomance for microservices. For this case when a users authenticates it grants access to all endpoints but in a more robust project we could separate scopes/permissions for each user.

### 3. Data synchronization

For data synchronization it was created:

A simple command-line interface (SyncAvailabilityCommand) which for this challenge consumes a simple JSON file kept on the storage folder to ingest some availability for a property.
A rest API as mentioned above to ingest and also check availability.
A queued job (SyncAvailabilityJob) for handling background processing

### 4. Cache

So for cache i decided to go for redis a lot for the use of tags by each property and generate keys based on the query parameters provided.
The cache by each property is flushed everytime a new ingestion for that property is handled.

### 5. Rate Limit

For rate limiter it was used the approach of:

1. Limit by User/IP - Per hour limit by 100 requests by User/IP across all properties
2. Limit by property - Per hour limit by 50 request for any single property

Also included some custom messages for explaining which limit was exceeded


## License
This project is licensed under the MIT License - see the LICENSE file for details.