# Documentación Wellezy Travel Backend - Sistema de Reservas de Vuelos

Wellezy Travel Backend is a platform to find and book flights depending on the departure and arrival city selected by the user.

## Table of Contents

-   [Requirements](#requirements)
-   [Installation](#installation)
-   [Configuration](#configuration)
-   [Database](#database)
-   [Endpoints API](#endpoints-api)
-   [Tests](#tests)
-   [Project Structure](#project-structure)

## Requirements

-   PHP >= 8.2
-   Compositor
-   MySQL/PostgreSQL (Previously created Database)
-   Laravel 11.x

## Installation

Once the repository has been cloned:

2. Install dependencies:

```bash
composer install
```

3. Copy the configuration file:

```bash
cp .env.example .env
```

4. Generate application key:

```bash
php artisan key:generate
```

## Configuration

### Environment variables

Configure the following variables in the file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_name
DB_USERNAME=user
DB_PASSWORD=password

API_BASE_URL=url_api_externa_vuelos
```

### Sanctum

The project uses Laravel Sanctum for API authentication. The configuration is in:

-   `config/sanctum.php`
-   `config/auth.php`

## Database

### Execute migrations

```bash
php artisan migrate
```

### Table structure

-   **users**: Store user information
-   **cache**: Create cache table
-   **reserves**: Save flight reservations
-   **itineraries**: Stores reserves itineraries
-   **personal_access_tokens**: Authentication tokens

## Endpoints API

### Authentication

-   **POST /api/register**

    ```json
    {
        "name": "string",
        "email": "string",
        "password": "string"
    }
    ```

-   **POST /api/login**
    ```json
    {
        "email": "string",
        "password": "string"
    }
    ```

### Flights They require authentication

-   **POST /api/airports**

    ```json
    {
        "code": "string"
    }
    ```

-   **POST /api/flights**
    ```json
    {
        "direct": "boolean",
        "currency": "string",
        "searchs": "integer",
        "class": "boolean",
        "qtyPassengers": "integer",
        "adult": "integer",
        "child": "integer",
        "baby": "integer",
        "seat": "integer",
        "itinerary": [
            {
                "departureCity": "string",
                "arrivalCity": "string",
                "hour": "date"
            }
        ]
    }
    ```

### ReservatioThey require authentication

-   **POST /api/reserves**
    ```json
    {
        "name": "string",
        "email": "string",
        "passenger_count": "integer",
        "adult_count": "integer",
        "child_count": "integer",
        "baby_count": "integer",
        "total_amount": "numeric",
        "currency": "string",
        "itineraries": [
            {
                "departure_city": "string",
                "arrival_city": "string",
                "departure_date": "date",
                "arrival_date": "date",
                "departure_time": "time",
                "arrival_time": "time",
                "flight_number": "string",
                "marketing_carrier": "string"
            }
        ]
    }
    ```

## Tests

### Execute tests

```bash
php artisan test
```

### TESTS COVERAGE

-   AuthController

    -   User registration
    -   User login
    -   Validation of credentials

-   ReserveController
    -   Creation of reservations
    -   Data validation
    -   Relationships with itineraries

## Project structure

## App relevant folders

### Http/Controllers

-   `AuthController`: Handles authentication and registration
-   `FlightController`: Manages airports and flight search
-   `ReserveController`: Handles flight reservations

### Models

-   `User`: User model
-   `Reserve`: Reserve model
-   `Itinerary`: Itineraries model

### Providers

-   `AppServiceProvider`: Application services
    -   `parameterValidator`: Parameter validation service

### Bootstrap

-   `app.php`: Application configuration
-   `providers.php`: Providers initialization

### Database

-   `migrations`: Database tables configurations ready to migrate

### Routes

-   `api.php`: API Routes Instance

### Middlewares

-   `auth:sanctum`: API authentication

### Tests

-   `Feature`: Test complete functionalities, in this case here are the test controller tests and the reservations controller tests

-   `Unit`: Tests of specific code units that do not interfere with other codes and desire to test independence, in this case there is nothing here

### .Env

-   Stores the necessary environment variables for the correct execution of the project, in this case it is only necessary

### Composer.json

-   Stores the necessary packages for the correct execution of the project, like sanctum for auth, phpunit for test and others
