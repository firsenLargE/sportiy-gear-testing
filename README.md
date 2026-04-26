# 🏅 SportifyGear

Welcome to SportifyGear! This guide will help you get the project running on your computer in just a few minutes.

---

### 🚀 Quick Start Guide

#### 1. Go to the project folder
Open your terminal and type:
```bash
cd SportifyGear/SportifyGear
```

#### 2. Download the tools
Run these two commands to install everything the website needs:
```bash
composer install --ignore-platform-reqs
npm install
```

#### 3. Setup your Database
Follow the steps for the database you have installed:

**Option A: If you have MySQL or MariaDB**
1. Create the database:  
   `sudo mariadb -e "CREATE DATABASE IF NOT EXISTS ecommercewebsite;"`
2. Create the user:  
   `sudo mariadb -e "CREATE USER IF NOT EXISTS 'sportify'@'localhost' IDENTIFIED BY 'sportify_secret'; GRANT ALL PRIVILEGES ON ecommercewebsite.* TO 'sportify'@'localhost'; FLUSH PRIVILEGES;"`
3. Check your `.env` file: Make sure `DB_CONNECTION=mysql` is set.

**Option B: If you have PostgreSQL**
1. Create the database:  
   `sudo -u postgres psql -c "CREATE DATABASE ecommercewebsite;"`
2. Create the user:  
   `sudo -u postgres psql -c "CREATE USER sportify WITH PASSWORD 'sportify_secret'; GRANT ALL PRIVILEGES ON DATABASE ecommercewebsite TO sportify;"`
3. Check your `.env` file: Change `DB_CONNECTION=pgsql` and `DB_PORT=5432`.

#### 4. Prepare the Data
Run this command to build the tables and add starting data:
```bash
php artisan migrate:fresh --seed
```

#### 5. Start the Website!
You need **two** terminal windows open:
*   **Window 1**: `php artisan serve`
*   **Window 2**: `npm run dev`

---

### 🌍 Open in your Browser
Main Website: [http://127.0.0.1:8000](http://127.0.0.1:8000)  
Admin Panel: [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)

admin credentials 
Email: admin@example.com
Password: password123

### ❓ Troubleshooting
- **Database error?** Double-check your `.env` file credentials match the steps in Step 3.
- **Vite not loading?** Make sure `npm run dev` is still running in the second terminal.

Happy Coding! 🚀
