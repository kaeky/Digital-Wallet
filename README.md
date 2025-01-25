## Description

This repository contains a starter setup for two microservices built using NestJS framework and laravel. The microservices are:

- **Soap Microservice**
- **Rest Microservice**

Each microservice is independent, scalable, and designed for server-side applications.

## Authentication
This project uses Auth0 for authentication. Auth0 simplifies and centralizes authentication management with features such as:

- **Pre-built User Interfaces**: Quick setup for login, registration, and password reset screens.
- **Scalability**: Handles high traffic with ease.
- **Security**: Implements industry-standard security protocols such as OAuth2 and OpenID Connect.
- **Customizable Rules**: Allows customization of user authentication flows.

## Opportunities for Improvement

- ### Redis for Microservices Communication
    - **Low Latency**: Redis operates in-memory, ensuring quick message processing.
    - **Pub/Sub Mechanism**: Allows for decoupled communication between microservices.
- ### Microservice Gateway
    -  **Unified Entry Point**: Simplifies client communication by routing all requests through the gateway.
    -  **Load Balancing**: Improves distribution of requests across microservices.
    -  **Security**: Centralized authentication and authorization.

## Database Setup

### Step 1: Create a PostgreSQL Database with Docker

Run the following command to set up a Mysql database instance using Docker:

```bash
docker run -d \
--name digitalWallet \
-p 3306:3306 \
-e MYSQL_ROOT_HOST="%" \
-e MYSQL_ROOT_PASSWORD=digitalWalletDb \
-e MYSQL_DATABASE=digitalWallet \
-e MYSQL_PASSWORD=digitalWalletDb \
mysql

```

### Step 2: Clone the Repository
Clone the repository to your local machine:
```bash
git clone https://github.com/kaeky/Digital-Wallet.git
```
#### The repository contains two microservices:
- **Soap Microservice**
    - This microservice is responsible for all database request.
- **REST Microservice**
    - This microservice is a bridge between soap microservice and the final client.

Navigate to each microservice directory to configure and run it.

### Step 4: Environment Variables
Each microservice requires its own .env file. In the root directory of each microservice, create a .env file and add the following environment variables like the .env.example file.
### Step 5: Running the Microservices
Navigate to the respective microservice folder and use the following commands:
```bash
#for soap microservice
#use php 8.4.1 -> copy php.ini to your php ini file or solve the errors if you have any
composer install
php artisan doctrine:migrations:migrate

php artisan serve

#for rest microservice
# Install dependencies
npm install

# Run in development mode
npm run start:dev

# Run in production mode
npm run start:prod
```
### Step 6: Documentation APIS POSTMAN
import the postman collection from the root directory of the project
```bash
https://www.postman.com/kaeky/workspace/digital-wallet
```