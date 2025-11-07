<?php
/**
 * Recipe Manager REST API - Main Entry Point
 * Complete implementation with FlightPHP framework and organized routes
 * 
 * Milestone 3: Full CRUD Implementation & OpenAPI Documentation
 */

// Load dependencies and configuration
require_once __DIR__ . '/../../vendor/autoload.php';
require_once 'Config.php';

// Autoload our classes
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/dao/',
        __DIR__ . '/services/',
        __DIR__ . '/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

use flight\Engine;
use flight\net\Request;
use flight\net\Response;

// Initialize Flight framework
$app = new Engine();

// =============================================================================
// MIDDLEWARE AND CONFIGURATION
// =============================================================================

// CORS Headers - Enable cross-origin requests
$app->before('*', function() {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Content-Type: application/json; charset=utf-8');
    
    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
});

// Global error handler
$app->map('error', function(Exception $ex) {
    error_log("API Error: " . $ex->getMessage() . " in " . $ex->getFile() . ":" . $ex->getLine());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => true,
        'message' => 'Internal server error occurred',
        'debug' => [
            'error' => $ex->getMessage(),
            'file' => basename($ex->getFile()),
            'line' => $ex->getLine()
        ]
    ]);
});

// Request logging middleware
$app->before('*', function() {
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];
    $timestamp = date('Y-m-d H:i:s');
    error_log("[$timestamp] $method $uri");
});

// =============================================================================
// HELPER FUNCTIONS
// =============================================================================

/**
 * Send standardized JSON response
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    
    // Ensure consistent response format
    if (!isset($data['success'])) {
        $data = [
            'success' => $statusCode >= 200 && $statusCode < 300,
            'data' => $data,
            'message' => $statusCode >= 200 && $statusCode < 300 ? 'Success' : 'Error'
        ];
    }
    
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Get request body as associative array
 */
function getRequestBody() {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    return $data ?: [];
}

/**
 * Validate required fields in request data
 */
function validateRequiredFields($data, $requiredFields) {
    $missing = [];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            $missing[] = $field;
        }
    }
    return $missing;
}

// =============================================================================
// API INFORMATION ROUTES
// =============================================================================

// API root - Information and available endpoints
$app->route('GET /', function() {
    jsonResponse([
        'name' => 'Recipe Manager API',
        'version' => '1.0.0',
        'status' => 'running',
        'timestamp' => date('c'),
        'documentation' => [
            'interactive' => '/docs.html',
            'openapi_spec' => '/openapi.yaml',
            'test_suite' => '/test_api.php'
        ],
        'endpoints' => [
            'authentication' => [
                'POST /auth/login' => 'User login',
                'POST /auth/register' => 'User registration',
                'POST /auth/logout' => 'User logout'
            ],
            'entities' => [
                'GET /users' => 'User management',
                'GET /categories' => 'Recipe categories',
                'GET /ingredients' => 'Ingredient management',
                'GET /recipes' => 'Recipe operations',
                'GET /reviews' => 'Reviews and ratings',
                'GET /roles' => 'Role management',
                'GET /recipe-ingredients' => 'Recipe-ingredient relationships'
            ],
            'utilities' => [
                'GET /health' => 'API health check',
                'GET /stats' => 'API statistics'
            ]
        ]
    ]);
});

// API health check
$app->route('GET /health', function() {
    try {
        // Test database connection
        $testDAO = new BaseDAO('users');
        $dbStatus = 'connected';
    } catch (Exception $e) {
        $dbStatus = 'error: ' . $e->getMessage();
    }
    
    jsonResponse([
        'status' => 'healthy',
        'timestamp' => date('c'),
        'database' => $dbStatus,
        'php_version' => PHP_VERSION,
        'memory_usage' => memory_get_usage(true),
        'uptime' => $_SERVER['REQUEST_TIME'] - $_SERVER['REQUEST_TIME_FLOAT']
    ]);
});

// API statistics
$app->route('GET /stats', function() {
    jsonResponse([
        'total_endpoints' => 50,
        'entities_supported' => 7,
        'crud_operations' => ['CREATE', 'READ', 'UPDATE', 'DELETE'],
        'authentication_methods' => ['login', 'register'],
        'documentation_formats' => ['OpenAPI 3.0', 'Swagger UI'],
        'supported_methods' => ['GET', 'POST', 'PUT', 'DELETE']
    ]);
});

// API Documentation routes
$app->route('GET /docs', function() {
    header('Location: docs.html');
    exit();
});

$app->route('GET /api-spec', function() {
    header('Content-Type: application/x-yaml');
    readfile('openapi.yaml');
    exit();
});

// =============================================================================
// LOAD ALL ROUTE FILES
// =============================================================================

// Include all route definitions
require_once __DIR__ . '/routes/user_routes.php';
require_once __DIR__ . '/routes/recipe_routes.php';
require_once __DIR__ . '/routes/category_routes.php';
require_once __DIR__ . '/routes/ingredient_routes.php';
require_once __DIR__ . '/routes/review_routes.php';
require_once __DIR__ . '/routes/role_routes.php';
require_once __DIR__ . '/routes/recipe_ingredient_routes.php';

// =============================================================================
// BACKWARDS COMPATIBILITY ROUTES (for existing implementations)
// =============================================================================

// Legacy user routes
$app->route('POST /users/login', function() {
    $userService = new UserService();
    $data = getRequestBody();
    $result = $userService->login($data['email'] ?? '', $data['password'] ?? '');
    jsonResponse($result, $result['success'] ? 200 : 401);
});

$app->route('POST /users/register', function() {
    $userService = new UserService();
    $data = getRequestBody();
    $result = $userService->register($data);
    jsonResponse($result, $result['success'] ? 201 : 400);
});

// =============================================================================
// ERROR HANDLING FOR UNMATCHED ROUTES
// =============================================================================

// 404 handler for unmatched routes
$app->map('notFound', function() {
    jsonResponse([
        'success' => false,
        'message' => 'Endpoint not found',
        'available_endpoints' => [
            'GET /' => 'API information',
            'GET /docs.html' => 'Interactive documentation',
            'GET /openapi.yaml' => 'OpenAPI specification',
            'POST /auth/login' => 'User authentication',
            'GET /users' => 'User management',
            'GET /recipes' => 'Recipe operations',
            'GET /categories' => 'Category management',
            'GET /ingredients' => 'Ingredient operations',
            'GET /reviews' => 'Review system',
            'GET /roles' => 'Role management'
        ]
    ], 404);
});

// =============================================================================
// START THE APPLICATION
// =============================================================================

// Start Flight framework
try {
    $app->start();
} catch (Exception $e) {
    error_log("Fatal API Error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Application failed to start',
        'error' => $e->getMessage()
    ], 500);
}

// =============================================================================
// API INFO ENDPOINTS
// =============================================================================

// API Info
$app->route('GET /', function() {
    jsonResponse([
        'name' => 'Recipe Manager API',
        'version' => '1.0.0',
        'status' => 'running',
        'documentation' => 'http://localhost/web-project/backend/rest/docs.html',
        'openapi_spec' => 'http://localhost/web-project/backend/rest/openapi.yaml',
        'endpoints' => [
            'GET /docs.html' => 'API Documentation (Swagger UI)',
            'GET /openapi.yaml' => 'OpenAPI Specification',
            'GET /users' => 'Get all users',
            'GET /categories' => 'Get all categories',
            'GET /ingredients' => 'Get all ingredients',
            'GET /recipes' => 'Get all recipes',
            'GET /reviews' => 'Get all reviews'
        ]
    ]);
});

// API Documentation route
$app->route('GET /docs', function() {
    header('Location: docs.html');
    exit();
});

// OpenAPI specification route
$app->route('GET /api-spec', function() {
    header('Content-Type: application/x-yaml');
    readfile('openapi.yaml');
    exit();
});

// =============================================================================
// USER ENDPOINTS
// =============================================================================

$userService = new UserService();

// Get all users
$app->route('GET /users', function() use ($userService) {
    $result = $userService->getAll();
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get user by ID
$app->route('GET /users/@id', function($id) use ($userService) {
    $result = $userService->getById($id);
    jsonResponse($result, $result['success'] ? 200 : 404);
});

// Create new user
$app->route('POST /users', function() use ($userService) {
    $data = getRequestBody();
    $result = $userService->create($data);
    jsonResponse($result, $result['success'] ? 201 : 400);
});

// Update user
$app->route('PUT /users/@id', function($id) use ($userService) {
    $data = getRequestBody();
    $result = $userService->update($id, $data);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Delete user
$app->route('DELETE /users/@id', function($id) use ($userService) {
    $result = $userService->delete($id);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// User authentication
$app->route('POST /users/login', function() use ($userService) {
    $data = getRequestBody();
    $result = $userService->login($data['email'] ?? '', $data['password'] ?? '');
    jsonResponse($result, $result['success'] ? 200 : 401);
});

$app->route('POST /users/register', function() use ($userService) {
    $data = getRequestBody();
    $result = $userService->register($data);
    jsonResponse($result, $result['success'] ? 201 : 400);
});

// =============================================================================
// CATEGORY ENDPOINTS
// =============================================================================

$categoryService = new CategoryService();

// Get all categories
$app->route('GET /categories', function() use ($categoryService) {
    $result = $categoryService->getAll();
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get category by ID
$app->route('GET /categories/@id', function($id) use ($categoryService) {
    $result = $categoryService->getById($id);
    jsonResponse($result, $result['success'] ? 200 : 404);
});

// Create new category
$app->route('POST /categories', function() use ($categoryService) {
    $data = getRequestBody();
    $result = $categoryService->create($data);
    jsonResponse($result, $result['success'] ? 201 : 400);
});

// Update category
$app->route('PUT /categories/@id', function($id) use ($categoryService) {
    $data = getRequestBody();
    $result = $categoryService->update($id, $data);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Delete category
$app->route('DELETE /categories/@id', function($id) use ($categoryService) {
    $result = $categoryService->delete($id);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get categories with recipe count
$app->route('GET /categories/with-count', function() use ($categoryService) {
    $result = $categoryService->getCategoriesWithRecipeCount();
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// =============================================================================
// INGREDIENT ENDPOINTS
// =============================================================================

$ingredientService = new IngredientService();

// Get all ingredients
$app->route('GET /ingredients', function() use ($ingredientService) {
    $result = $ingredientService->getAll();
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get ingredient by ID
$app->route('GET /ingredients/@id', function($id) use ($ingredientService) {
    $result = $ingredientService->getById($id);
    jsonResponse($result, $result['success'] ? 200 : 404);
});

// Create new ingredient
$app->route('POST /ingredients', function() use ($ingredientService) {
    $data = getRequestBody();
    $result = $ingredientService->create($data);
    jsonResponse($result, $result['success'] ? 201 : 400);
});

// Update ingredient
$app->route('PUT /ingredients/@id', function($id) use ($ingredientService) {
    $data = getRequestBody();
    $result = $ingredientService->update($id, $data);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Delete ingredient
$app->route('DELETE /ingredients/@id', function($id) use ($ingredientService) {
    $result = $ingredientService->delete($id);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get ingredients with usage stats
$app->route('GET /ingredients/with-usage', function() use ($ingredientService) {
    $result = $ingredientService->getIngredientsWithUsage();
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get most used ingredients
$app->route('GET /ingredients/most-used', function() use ($ingredientService) {
    $limit = $_GET['limit'] ?? 10;
    $result = $ingredientService->getMostUsedIngredients($limit);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// =============================================================================
// RECIPE ENDPOINTS
// =============================================================================

$recipeService = new RecipeService();

// Get all recipes
$app->route('GET /recipes', function() use ($recipeService) {
    $result = $recipeService->getAll();
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get recipe by ID
$app->route('GET /recipes/@id', function($id) use ($recipeService) {
    $result = $recipeService->getById($id);
    jsonResponse($result, $result['success'] ? 200 : 404);
});

// Create new recipe
$app->route('POST /recipes', function() use ($recipeService) {
    $data = getRequestBody();
    $result = $recipeService->create($data);
    jsonResponse($result, $result['success'] ? 201 : 400);
});

// Update recipe
$app->route('PUT /recipes/@id', function($id) use ($recipeService) {
    $data = getRequestBody();
    $result = $recipeService->update($id, $data);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Delete recipe
$app->route('DELETE /recipes/@id', function($id) use ($recipeService) {
    $result = $recipeService->delete($id);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Search recipes
$app->route('GET /recipes/search', function() use ($recipeService) {
    $query = $_GET['q'] ?? '';
    $result = $recipeService->search($query);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get recipes by category
$app->route('GET /recipes/category/@categoryId', function($categoryId) use ($recipeService) {
    $result = $recipeService->getRecipesByCategory($categoryId);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get recipes by user
$app->route('GET /recipes/user/@userId', function($userId) use ($recipeService) {
    $result = $recipeService->getRecipesByUser($userId);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// =============================================================================
// REVIEW ENDPOINTS
// =============================================================================

$reviewService = new ReviewService();

// Get all reviews
$app->route('GET /reviews', function() use ($reviewService) {
    $result = $reviewService->getAll();
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get review by ID
$app->route('GET /reviews/@id', function($id) use ($reviewService) {
    $result = $reviewService->getById($id);
    jsonResponse($result, $result['success'] ? 200 : 404);
});

// Create new review
$app->route('POST /reviews', function() use ($reviewService) {
    $data = getRequestBody();
    $result = $reviewService->create($data);
    jsonResponse($result, $result['success'] ? 201 : 400);
});

// Update review
$app->route('PUT /reviews/@id', function($id) use ($reviewService) {
    $data = getRequestBody();
    $result = $reviewService->update($id, $data);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Delete review
$app->route('DELETE /reviews/@id', function($id) use ($reviewService) {
    $result = $reviewService->delete($id);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get reviews for recipe
$app->route('GET /reviews/recipe/@recipeId', function($recipeId) use ($reviewService) {
    $limit = $_GET['limit'] ?? 10;
    $offset = $_GET['offset'] ?? 0;
    $result = $reviewService->getReviewsForRecipe($recipeId, $limit, $offset);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get reviews by user
$app->route('GET /reviews/user/@userId', function($userId) use ($reviewService) {
    $limit = $_GET['limit'] ?? 10;
    $offset = $_GET['offset'] ?? 0;
    $result = $reviewService->getReviewsByUser($userId, $limit, $offset);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get recipe rating stats
$app->route('GET /reviews/recipe/@recipeId/rating', function($recipeId) use ($reviewService) {
    $result = $reviewService->getRecipeRating($recipeId);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Start the application
$app->start();