# README for Gunung Galunggung Tourism System

## Project Overview

This project is a backend API for managing the Gunung Galunggung tourism system. It is built using Laravel and is designed to facilitate various administrative tasks related to the management of the tourism system, including ticket management, climbing schedules, user management, incoming contacts, gallery management, and reporting.

## Features

- **Admin Dashboard**: A centralized interface for administrators to manage the system.
- **Ticket Management**: Tools for managing ticket sales and availability.
- **Climbing Schedule Management**: Features to manage and display climbing schedules.
- **User Management**: Admin functionalities to manage user accounts and permissions.
- **Incoming Contacts**: A system to handle inquiries and messages from users.
- **Gallery Management**: Tools to manage images and content for the tourism gallery.
- **Reporting**: Generate reports for various aspects of the system.

## Installation

1. Clone the repository:
   ```
   git clone <repository-url>
   ```

2. Navigate to the project directory:
   ```
   cd dbgalunggung-backend
   ```

3. Install dependencies:
   ```
   composer install
   ```

4. Set up your `.env` file:
   - Copy the `.env.example` to `.env`:
     ```
     cp .env.example .env
     ```
   - Update the database configuration to match your MySQL setup.

5. Generate the application key:
   ```
   php artisan key:generate
   ```

6. Run the migrations (if applicable):
   ```
   php artisan migrate
   ```

## Model Structure

The project includes an `Admin` model located at `app/Models/Admin.php`. This model represents the `tbl_admin` table in the database and is configured for authentication using Laravel Sanctum.

### Admin Model Overview

- **Namespace**: `App\Models`
- **Primary Key**: `id_admin`
- **Table Name**: `tbl_admin`
- **Fillable Fields**: `nama_admin`, `email`, `kata_sandi`, `created_at`, `updated_at`
- **Hidden Fields**: `kata_sandi` (to protect sensitive information)

### Usage

The `Admin` model is designed to be used for authentication and can be integrated with Laravel Sanctum for API token management. It is ready for the next steps in the development process, including creating an AuthController for handling login functionality.

## Contributing

Contributions are welcome! Please open an issue or submit a pull request for any enhancements or bug fixes.

## License

This project is licensed under the MIT License. See the LICENSE file for more details.