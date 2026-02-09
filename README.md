# 🏛️ Altar Servers Management System (ASMS)

![ASMS Logo](public/images/logo.png)

A comprehensive, robust, and secure management platform specifically designed for Parish Altar Server Ministries. Built with a custom PHP MVC architecture, this system streamlines server registration, mass scheduling, and attendance tracking with a modern, high-performance user interface.

## 🚀 Key Features

- **🔐 Advanced Authentication:** Multi-role access control (User, Admin, Superadmin) with secure password hashing and CSRF protection.
- **📊 Dynamic Dashboard:** Live analytics and real-time stats including active server counts, upcoming masses, and overall attendance rates.
- **📅 Mass Scheduling:** Effortless creation and management of liturgical schedules with automated date/time formatting.
- **📋 Attendance Tracking:** One-click attendance marking (Present, Late, Absent) with a clean, responsive UI.
- **👥 Server Directory:** Complete management of server profiles, including ranks (Senior, Junior, Aspirant) and team assignments.
- **📢 Announcement Module:** Integrated communication channel for posting ministry updates and reminders.
- **🕵️ Audit Logs:** Full transparency with automated tracking of user actions (logins, creations, deletions).
- **📈 Reporting & Analytics:** Visualized data via Chart.js for long-term performance monitoring.

## 🛠️ Tech Stack

- **Backend:** PHP 8.x (Custom OOP MVC Framework)
- **Frontend:** Tailwind CSS v4 (Modern Utility-first CSS)
- **Database:** MySQL / MariaDB (Singleton PDO Connection)
- **Icons:** Phosphor Icons / HeroIcons
- **Charts:** Chart.js
- **Environment:** XAMPP / Apache

## 📦 Project Structure

```text
app/
├── config/         # App constants & DB credentials
├── core/           # Framework core (Router, Database, Controller)
├── helpers/        # Security & Utility functions (CSRF, XSS, Flash)
├── controllers/    # Business logic
├── models/         # Data entities
├── repositories/   # Data access layer (Repository Pattern)
└── views/          # UI templates
public/             # Web root (Index, CSS, JS, Assets)
```

## ⚙️ Quick Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/your-username/asms.git
   ```
2. **Setup Database:**
   - Create a database named `asms_db` in PHPMyAdmin.
   - Run the setup script: `http://localhost/[your-folder]/public/setup_database.php`
3. **Configure:**
   - Edit `.env` or `app/config/config.php` if you use different DB credentials.
   - The system automatically detects its root folder, so you can host it in a subfolder or directly in the document root.
4. **Build Styles:**
   ```bash
   npm install
   npm run build
   ```

## 🛡️ Security Features

- **XSS Mitigation:** Automated output escaping via helper functions.
- **CSRF Protection:** Integrated token validation for every POST request.
- **SQL Injection Prevention:** Strict use of PDO Prepared Statements.
- **Session Security:** Strict session management and login requirements for protected routes.

---
*Developed with ❤️ for the Altar Server Ministry.*
