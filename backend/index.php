<?php

// ===============================
// CORS FIX — REQUIRED FOR LIVE SERVER
// ===============================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, Authentication");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
header("Access-Control-Expose-Headers: Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// =====================================
// FIX: Define DIR constant for BaseDAO
// =====================================
define('DIR', __DIR__);

ini_set('display_errors', 1);
error_reporting(E_ALL);

// ===============================
// VENDOR AUTOLOAD (Composer)
// ===============================
require_once __DIR__ . '/vendor/autoload.php';

// ===============================
// LOAD SERVICES
// ===============================
require_once __DIR__ . '/rest/services/AuthService.php';
require_once __DIR__ . '/rest/services/CategoryService.php';
require_once __DIR__ . '/rest/services/UserService.php';
require_once __DIR__ . '/rest/services/RoleService.php';
require_once __DIR__ . '/rest/services/RecipeService.php';
require_once __DIR__ . '/rest/services/IngredientService.php';
require_once __DIR__ . '/rest/services/RecipeIngredientService.php';
require_once __DIR__ . '/rest/services/ReviewService.php';

// ===============================
// REGISTER SERVICES
// ===============================
Flight::register('auth_service', 'AuthService');
Flight::register('categoryService', 'CategoryService');
Flight::register('userService', 'UserService');
Flight::register('roleService', 'RoleService');
Flight::register('recipeService', 'RecipeService');
Flight::register('ingredientService', 'IngredientService');
Flight::register('recipeIngredientService', 'RecipeIngredientService');
Flight::register('reviewService', 'ReviewService');

// ===============================
// AUTH MIDDLEWARE + ROLES
// ===============================
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/data/roles.php';

Flight::register('auth_middleware', 'AuthMiddleware');

// ===============================
// GLOBAL AUTH PROTECTION (JWT)
// ===============================
Flight::before('start', function () {

    $url = Flight::request()->url;
    $method = Flight::request()->method;

    // 1️⃣ Allow ALL GET routes (Swagger must work without JWT)
    if ($method === "GET") {
        return;
    }

    // 2️⃣ Allow login & register (POST without token)
    if (
        ($method === "POST" && strpos($url, "/auth/login") === 0) ||
        ($method === "POST" && strpos($url, "/auth/register") === 0)
    ) {
        return;
    }

    // 3️⃣ Everything else REQUIRES JWT token
    $headers = getallheaders();

    if (!isset($headers["Authorization"])) {
        Flight::halt(401, json_encode(["error" => "Missing Authorization header"]));
    }

    $authHeader = trim($headers["Authorization"]);

    if (!str_starts_with($authHeader, "Bearer ")) {
        Flight::halt(401, json_encode(["error" => "Authorization must be: Bearer <token>"]));
    }

    $token = substr($authHeader, 7);

    try {
        Flight::auth_middleware()->verifyToken($token);
    } catch (Exception $e) {
        Flight::halt(401, json_encode(["error" => "Invalid or expired token"]));
    }
});

// ===============================
// ROUTES (ALL API ENDPOINTS)
// ===============================
require_once __DIR__ . '/rest/routes/auth_routes.php';
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
