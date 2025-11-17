<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ===============================
// CONFIG
// ===============================
require __DIR__ . '/Config.php';

// ===============================
// VENDOR AUTOLOAD
// (vendor folder je u backend/vendor)
// ===============================
require __DIR__ . '/vendor/autoload.php';

// ===============================
// DAO AUTOLOADER
// ===============================
require __DIR__ . '/DAOAutoloader.php';

// ===============================
// SERVICES
// ===============================
require __DIR__ . '/services/CategoryService.php';
require __DIR__ . '/services/UserService.php';
require __DIR__ . '/services/RoleService.php';
require __DIR__ . '/services/RecipeService.php';
require __DIR__ . '/services/IngredientService.php';
require __DIR__ . '/services/RecipeIngredientService.php';
require __DIR__ . '/services/ReviewService.php';

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
require __DIR__ . '/rest/routes/category_routes.php';
require __DIR__ . '/rest/routes/user_routes.php';
require __DIR__ . '/rest/routes/role_routes.php';
require __DIR__ . '/rest/routes/recipe_routes.php';
require __DIR__ . '/rest/routes/ingredient_routes.php';
require __DIR__ . '/rest/routes/recipe_ingredient_routes.php';
require __DIR__ . '/rest/routes/review_routes.php';

// ===============================
// TEST ROUTE
// ===============================
Flight::route('/test', function() {
    echo json_encode([
        'status' => 'OK',
        'message' => 'Flight backend up and running 🚀',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});

// ===============================
// START FLIGHT
// ===============================
Flight::start();
