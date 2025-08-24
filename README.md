<b>How to setup after git clone</b>

1. git clone repo
2. copy env example rename to .env, change dbconnection to mysql and adjust if needed.
3. composer install --ignore-platform-reqs
4. php artisan key:generate
5. php artisan migrate
6. npm install
7. npm run dev
8. php artisan serve (Only run this if you are not using Laravel Herd.)

7 and 8 are needed to run everytime reopen vscode.



<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development/)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

# Kawal Selia Audit System

## Overview

**Kawal Selia** is an audit and monitoring system developed for the Royal Malaysia Police (PDRM).  
It is designed to manage, track, and report on various types of investigation papers and case records, ensuring compliance, transparency, and data integrity across multiple police departments.

## Features

- **Multi-Module Support:** Handles different case types (e.g., Narkotik, Jenayah, Trafik, Orang Hilang, Laporan Mati Mengejut).
- **Data Import:** Supports Excel file uploads for bulk data entry.
- **Role-Based Access:** Secure access for different user roles.
- **Audit Trail:** Tracks changes and user actions for accountability.
- **DataTables Integration:** Fast, filterable, and exportable data tables.
- **Localization:** Supports Malay languages.

## Technical Stack

- **Backend:** Laravel (PHP)
- **Frontend:** Blade, Alpine.js, DataTables
- **Database:** MySQL, Hostinger localhost in use
- **Other:** Redis, Queue, Mail (SMTP), AWS (optional)

## Environment Configuration

The system uses a `.env` file for environment-specific settings.  
Key configuration options include:

- **APP_URL:** Application base URL
- **DB_CONNECTION, DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD:** Database connection settings
- **SESSION_DRIVER:** Session storage (database recommended)
- **MAIL_MAILER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD:** Email sending configuration
- **CACHE_STORE, QUEUE_CONNECTION:** Caching and queue drivers

Example:
```env
APP_NAME="Kawal Selia"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=https://jips-kawalselia.com/kawalselia/
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=kawalselia
DB_USERNAME=root
DB_PASSWORD=root
...
```

## Security

- Sensitive credentials (DB, mail, etc.) are stored in `.env` and **should never be committed to version control**.
- Use strong, unique passwords for all production services.

## Getting Started

1. Clone the repository.
2. Copy `.env.example` to `.env` and update with your environment values.
3. Run `composer install` and `npm install`.
4. Run database migrations: `php artisan migrate`.
5. Start the server: `php artisan serve`.

## License

This project is proprietary and developed for the Royal Malaysia Police (PDRM).  
All rights
