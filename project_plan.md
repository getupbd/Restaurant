# Antigravity – Multi-Tenant Restaurant SaaS Full Plan

## Project Overview
Antigravity is a Multi-Tenant + Multi-Database Restaurant Management SaaS platform.

Each tenant gets:
- Own website
- Own admin panel
- Own database
- Own domain/subdomain

---

## Architecture
### Central System
- Tenant management
- Subscription & billing
- Domain mapping
- Feature control

### Tenant System
- Restaurant operations
- Website
- Orders & POS
- Inventory
- Reports

---

## Multi-Database Structure
### Central DB
- tenants, domains, packages, subscriptions, invoices, payments

### Tenant DB
- users, branches, tables, menu_items, orders, stock, customers

---

## User Roles
Central: Super Admin, SaaS Admin  
Tenant: Owner, Manager, Cashier, Waiter, Kitchen, Customer

---

## Modules

### SaaS Core
- Tenant Management
- Package Management
- Billing
- Domain Management

### Restaurant Operations
- Menu
- Table
- POS
- Orders
- Kitchen
- Inventory
- Purchase

### Website & Growth
- Website Builder
- CMS
- Online Ordering
- Reservation
- CRM

---

## Development Phases
1. SaaS Core
2. Tenant Setup
3. Restaurant Core
4. Inventory
5. Website
6. Finance
7. CRM

---

## MVP Scope
- Tenant onboarding
- Menu
- POS
- Orders
- Inventory basic
- Website basic
- Online ordering

---

## Tech Stack
- Laravel 11
- MySQL
- Redis
- REST API

---

## End of File
