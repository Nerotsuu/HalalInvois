# HalalInvois

**HalalInvois** is a web-based Point of Sale (POS) and compliance management system built for F&B businesses in Malaysia. It integrates daily cashier checkout operations with **Halal ingredient tracking**, **simulated LHDN e-Invoicing**, **SST & sales reporting**, and **Role-Based Access Control (RBAC)**.

---

## Core Modules & Features

1. **Cashier POS Terminal (`pos.php`)**
   - Interactive product catalog with live stock validation and dynamic cart calculation.
   - Supports multiple payment methods (**Cash**, **Card**, and **DuitNow QR**).
   - Automatic Sales and Service Tax (SST) calculation and real-time stock deduction upon checkout.
   - Live session timer and operator status bar.

2. **Halal & Inventory Tracking**
   - Tracks raw ingredients, suppliers, stock quantities, and item expiry dates.
   - Monitors JAKIM Halal certificate numbers and certificate expiry statuses (`Valid` vs. `Expired`).

3. **LHDN e-Invoicing Simulation**
   - Automatically generates a unique simulated **LHDN UUID** for every completed transaction.
   - Logs transaction timestamps, payment methods, tax breakdowns, and cashier accountability.

4. **Tax & Sales Reporting**
   - Summarizes gross sales, total SST collected, net sales, and revenue breakdown by payment channel.

5. **Role-Based Access Control (RBAC)**
   - Three-tier role authentication backed by `password_hash()` (bcrypt) and PHP sessions:
     - **`owner`**: Full access to the multi-module management dashboard (`owner_dashboard.php`) and staff account creation.
     - **`cashier`**: Access to the POS checkout terminal (`pos.php`).
     - **`auditor`**: Read-only access to compliance and sales/tax reports (`auditor_reports.php`).

---

## Tech Stack

- **Backend:** PHP 8.2+ (PDO)
- **Database:** MySQL / MariaDB (XAMPP)
- **Frontend:** HTML5, Bootstrap 5, Bootstrap Icons, Vanilla JavaScript

---

## Project Folder Structure

```text
HalalInvois/
├── assets/               # Static images (product thumbnails, QR placeholders)
├── config/
│   └── db.php            # PDO database connection configuration
├── database/             # SQL schema and seed dump files
├── docs/                 # Project documentation and diagrams
├── includes/
│   └── functions.php     # Reusable database query and helper functions
├── views/                # HTML/Bootstrap presentation templates
├── login.php             # Authentication & RBAC role router
├── owner_dashboard.php   # Owner management dashboard controller
└── pos.php               # Cashier POS checkout controller
