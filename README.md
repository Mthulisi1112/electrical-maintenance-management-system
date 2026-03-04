# ⚡ Electrical Maintenance Management System (EMMS)

A comprehensive web application for managing electrical assets, preventive maintenance, fault reporting, and compliance records in industrial environments.

![Laravel](https://img.shields.io/badge/Laravel-12-red)
![PHP](https://img.shields.io/badge/PHP-8.3-purple)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3-blue)
![Alpine.js](https://img.shields.io/badge/Alpine.js-3-green)
![SQLite](https://img.shields.io/badge/SQLite-3-lightblue)

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Screenshots](#screenshots)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage Guide](#usage-guide)
- [User Roles](#user-roles)
- [API Documentation](#api-documentation)
- [Testing](#testing)
- [Deployment](#deployment)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

## 🎯 Overview

EMMS is a full-stack web application designed for industrial environments such as manufacturing plants, mines, utilities, and large facilities. It streamlines the management of electrical assets, schedules preventive maintenance, tracks work orders, and logs electrical faults.

This project bridges electrical engineering domain knowledge with modern web development, making it ideal for professionals transitioning from electrical engineering into software development.

## ✨ Features

### 🔧 Asset Management

- Register and manage electrical equipment (motors, transformers, MCCs, VFDs, etc.)
- Track asset specifications, location, and status
- QR code generation for each asset
- Asset health monitoring

### 📋 Work Order Management

- Create and assign work orders
- Track work order lifecycle (pending → in progress → completed → verified)
- Technician assignment and remarks
- Time tracking and parts usage logging

### ⚡ Fault Reporting

- Report electrical faults with severity levels
- Track fault resolution
- Upload images and symptoms
- Assign technicians to faults

### 📅 Maintenance Scheduling

- Create preventive maintenance schedules
- Automatic work order generation
- Frequency-based scheduling (daily, weekly, monthly, etc.)
- Overdue maintenance alerts

### 👥 Role-Based Access Control

- **Admin**: Full system access
- **Maintenance Supervisor**: Create work orders, assign technicians
- **Technician**: View assigned tasks, complete work orders
- **Auditor**: Read-only access to reports

### 📊 Reporting & Analytics

- Asset status dashboard
- Work order statistics
- Fault trends analysis
- Exportable reports

### 🔐 Security Features

- Email verification
- Password reset
- Role-based permissions
- Audit logging

## 🛠 Tech Stack

### Backend

- **Laravel 12** - PHP framework
- **Eloquent ORM** - Database management
- **Laravel Policies** - Authorization
- **Laravel Scheduler** - Automated tasks
- **Mailtrap** - Email testing

### Frontend

- **Blade Templates** - Server-side rendering
- **Alpine.js** - Interactive components
- **Tailwind CSS** - Styling
- **Chart.js** - Data visualization

### Database

- **SQLite** - Lightweight database (development)
- **MySQL/PostgreSQL** - Production ready

### Development Tools

- **Laravel Breeze** - Authentication scaffolding
- **Laravel Tinker** - Interactive debugging
- **TablePlus** - Database management

## 📸 Screenshots
