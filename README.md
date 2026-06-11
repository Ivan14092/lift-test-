# Lift User Records API

REST API built with Symfony + MongoDB for managing user records.

## Tech Stack

- PHP 8.3
- Symfony 7
- MongoDB + Doctrine ODM
- Redis + Symfony Messenger (async queue)
- Docker + Docker Compose
- PHPUnit
- OpenAPI (NelmioApiDocBundle)

## Requirements

- Docker
- Docker Compose

## Installation

```bash
git clone git@github.com:Ivan14092/lift-test-.git
cd lift-test
docker compose up --build -d
docker compose exec php composer install
```

## Running the worker

```bash
docker compose exec php php bin/console messenger:consume async -vv
```

## API Documentation

Open in browser: http://localhost:8080/api/doc

## Endpoints

### POST /api/users
Create a new user record (processed asynchronously).

**Request body:**
```json
{
    "firstName": "Ivan",
    "lastName": "Petrenko",
    "phoneNumbers": ["+380971234567", "+380631234567"]
}
```

**Response (202 Accepted):**
```json
{
    "message": "Record is being processed"
}
```

### GET /api/users
Get all user records with optional sorting.

**Query parameters:**
- `sort` — field to sort by: `firstName`, `lastName`, `createdAt`, `country` (default: `createdAt`)
- `direction` — sort direction: `asc` or `desc` (default: `asc`)

**Example:**
```bash
curl "http://localhost:8080/api/users?sort=firstName&direction=asc"
```

## Running tests

```bash
docker compose exec php php bin/phpunit
```

## How it works

1. POST request hits the controller
2. Controller dispatches message to Redis queue (returns 202 immediately)
3. Worker picks up the message asynchronously
4. Worker calls iplocate.io to get country by IP
5. Worker saves the record to MongoDB