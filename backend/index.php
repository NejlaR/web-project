<?php
// =====================================
// FIX: Define DIR constant for BaseDAO
// =====================================
define('DIR', __DIR__);  // 🔥 OVO RIJEŠAVA SVE PROBLEME SA Database.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// ===============================
// VENDOR AUTOLOAD
// (vendor folder je u backend/vendor)
// ===============================
require_once __DIR__ . '/vendor/autoload.php';

// ===============================
// SERVICES
// ===============================
require_once __DIR__ . '/rest/services/CategoryService.php';
require_once __DIR__ . '/rest/services/UserService.php';
require_once __DIR__ . '/rest/services/RoleService.php';
require_once __DIR__ . '/rest/services/RecipeService.php';
require_once __DIR__ . '/rest/services/IngredientService.php';
require_once __DIR__ . '/rest/services/RecipeIngredientService.php';
require_once __DIR__ . '/rest/services/ReviewService.php';

// ===============================
// REGISTER SERVICES IN FLIGHT
// ===============================
Flight::register('categoryService', 'CategoryService');
Flight::register('userService', 'UserService');
Flight::register('roleService', 'RoleService');
Flight::register('recipeService', 'RecipeService');
Flight::register('ingredientService', 'IngredientService');
Flight::register('recipeIngredientService', 'RecipeIngredientService');
Flight::register('reviewService', 'ReviewService');

// ===============================
// ROUTES
// ===============================
require_once __DIR__ . '/rest/routes/category_routes.php';
require_once __DIR__ . '/rest/routes/user_routes.php';
require_once __DIR__ . '/rest/routes/role_routes.php';
require_once __DIR__ . '/rest/routes/recipe_routes.php';
require_once __DIR__ . '/rest/routes/ingredient_routes.php';
require_once __DIR__ . '/rest/routes/recipe_ingredient_routes.php';
require_once __DIR__ . '/rest/routes/review_routes.php';

// ===============================
// START FLIGHT
// ===============================
Flight::start();
