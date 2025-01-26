# Ticketing API

This is a Ticketing System API designed for handling event tickets, user interactions, and ticket transfers. It features three user roles: Admin, Organizer, and User. Admins have full control, Organizers can create events and manage tickets, and Users can buy tickets, transfer tickets, and chat with other users.

## Repository

**GitHub Repository:**  
[https://github.com/KenPrz/ticketing-api](https://github.com/KenPrz/ticketing-api)

## Table of Contents

- [Project Overview](#project-overview)
- [Features](#features)
- [Setup Instructions](#setup-instructions)
- [API Documentation](#api-documentation)

## Project Overview

This API allows users to buy tickets, transfer tickets, chat with users, and more. Organizers can create and manage events, while Admins manage everything but chats.

### Key Features:

- **User Features:**
  - Register, login, and manage profiles.
  - Buy tickets for events.
  - Transfer tickets to other users.
  - Chat with users and organizers.

- **Organizer Features:**
  - Create and manage events.
  - Verify ticket purchases.
  - Chat with users.

- **Admin Features:**
  - Full access to user, event, and ticket management (excluding chat functionality).

---

## Setup Instructions

### Prerequisites

- [Docker](https://www.docker.com/get-started) and [Docker Compose](https://docs.docker.com/compose/) are required for local development.
- Laravel Sail for local development environment (built on Docker).
  
### 1. Clone the Repository

Clone the repository to your local machine:

```bash
git clone git@github.com:KenPrz/ticketing-api.git
cd ticketing-api
```

### 2. Install Dependencies

Run the following command to install Laravel's dependencies and set up the environment:

```bash
composer install
```

### 3. Set Up Docker Environment

This project uses Laravel Sail for local development, which is a Docker-based environment.

To start the environment, run:

```bash
./vendor/bin/sail up
```

This will start up the necessary Docker containers, including the web server and database.

### 4. Set Up Environment Variables

Copy the `.env.example` file to create your `.env` file:

```bash
cp .env.example .env
```

Then, edit the `.env` file to configure your environment variables, such as your database connection and app settings.

### 5. Generate Application Key

Run the following command to generate the application key:

```bash
./vendor/bin/sail artisan key:generate
```

### 6. Run Migrations

Migrate the database to set up the necessary tables:

```bash
./vendor/bin/sail artisan migrate
```

### 7. Seed Database (Optional)

If you want to seed the database with test data (e.g., sample users, events), run:

```bash
./vendor/bin/sail artisan db:seed
```

### 8. Access the API

Once your Sail environment is running, you can access the API at `http://localhost`.

---

## API Documentation

This API allows users, organizers, and admins to interact with the system. Below is a brief overview of the available endpoints:

- **User Routes:**
  - `POST /api/login`: User login.
  - `POST /api/register`: User registration.
  - `POST /api/tickets/{event_id}/buy`: Buy tickets.
  - `POST /api/tickets/{event_id}/{ticket_id}/transfer`: Transfer tickets to other users.

- **Organizer Routes:**
  - `POST /api/events`: Create an event.
  - `POST /api/tickets/{event_id}/{ticket_id}/verify`: Verify ticket.

- **Admin Routes:**
  - Admin routes are available only through web routes (not API).
  - Manage users, events, and tickets through the admin panel.
---