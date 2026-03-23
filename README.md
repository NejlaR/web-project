# 🍽️ Recipe Manager – Full-Stack Web Application

A full-stack web application for managing recipes, ingredients, categories, and user interactions.
This project was developed as part of a multi-phase implementation covering frontend, backend, and database architecture.

---

## 🚀 Features

* Responsive Single Page Application (SPA)
* Full CRUD operations for all entities
* User authentication (login/register)
* Recipe search and filtering
* Ingredient and category management
* Reviews and rating system
* Interactive UI components (tables, charts, modals)

---

## 🛠️ Technologies Used

### Frontend

* HTML5, CSS3, JavaScript
* jQuery, Bootstrap
* SPA architecture (dynamic view loading)

### Backend

* PHP (FlightPHP framework)
* RESTful API design
* Service layer architecture
* OpenAPI (Swagger documentation)

### Database

* MySQL
* Relational schema with 7 entities
* DAO pattern implementation

---

## 📂 Project Structure

```
web-project/
├── frontend/         # SPA frontend
├── backend/rest/     # REST API backend
├── database.sql      # Database schema
├── composer.json     # Dependencies
```

---

## ⚙️ How to Run

### 1. Setup Database

```bash
mysql -u root -p < database.sql
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Start Backend Server

```bash
cd backend/rest
php -S localhost:8080
```

### 4. Open Application

Frontend:
http://localhost/web-project/frontend/

API Docs (Swagger):
http://localhost:8080/docs.html

---

## 🔥 Key Highlights

* Implemented **full-stack architecture** (frontend + backend + database)
* Built **35+ REST API endpoints** with proper HTTP methods 
* Designed **relational database with 7 entities** 
* Applied **clean architecture principles (DAO + Service Layer)**
* Created **interactive and responsive UI**

---

## 👩‍💻 Author

**Nejla Račić**
GitHub: https://github.com/NejlaR
