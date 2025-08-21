# POS System

A comprehensive Point of Sale (POS) system built with Laravel, featuring inventory management, sales processing, invoicing, and reporting capabilities.

## Features

- **User Authentication**: Secure login and registration system with role-based access control
- **Dashboard**: Visual overview of sales, revenue, and inventory statistics
- **Product Management**: Create, update, and manage products with stock tracking
- **Category Management**: Organize products into categories
- **Sales Processing**: User-friendly interface for creating sales transactions
- **Invoice Generation**: Generate and print professional invoices for sales
- **Reporting**: Comprehensive reports for sales, inventory, and revenue analysis
- **Stock Alerts**: Notifications for low stock items
- **Database Management**: SQLite database with automated maintenance

## Installation

1. Clone the repository:
```bash
git clone https://github.com/AkalankaJayasinghe/POS-system.git
cd POS-system
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Create environment file:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure the database in `.env`:
```
DB_CONNECTION=sqlite
```

5. Run migrations and seed the database:
```bash
php artisan migrate --seed
```

6. Compile assets:
```bash
npm run dev
```

7. Start the server:
```bash
php artisan serve
```

## Maintenance

Run the maintenance command to optimize the system:
```bash
php artisan system:maintenance
```

## License

This POS system is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
