# Recipe Manager - Milestone 1

This repository contains the frontend static Single Page Application (SPA) shell and an initial backend folder structure for the Recipe Manager project required for Milestone 1.

What is included
- frontend/index.html - SPA shell that loads views into the main container
- frontend/views/*.html - separate HTML files for each view (dashboard, recipes, login, register, etc.)
	- includes `dishes.html` — a catalog of dishes with photos and ratings
		- includes `ingredients.html` — searchable ingredients table with details modal
- frontend/css/main.css - shared styles
- frontend/js/main.js - SPA loader and simple initializers (DataTables, Highcharts)
- backend/routes, backend/services, backend/dao - initial folders for REST backend (FlightPHP + PDO to be added later)
- ERD.md - draft entity-relationship diagram for at least 5 entities

How to run (static preview)
You can preview the static frontend by serving the `frontend` folder with any static server. Examples using Python built-in server (from repository root):

```powershell
# serve on http://localhost:8000
python -m http.server 8000 -d frontend
```

Next steps (Milestone 2+)
- Implement FlightPHP REST API in the `backend` using PDO and JWT authentication
- Document API with OpenAPI
- Connect frontend via AJAX to backend endpoints

Deliverables for Milestone 1
- SPA frontend with per-view files
- Draft ERD showing at least 5 entities
- Project structure initialized
