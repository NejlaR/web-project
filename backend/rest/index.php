<?php

/**
 * Recipe Manager REST API - Main Entry Point
 * 
 * This is the main entry point for the Recipe Manager REST API
 * It provides endpoints for managing recipes, ingredients, users, and reviews
 */

// Set content type to JSON
header('Content-Type: application/json');

// Enable CORS for frontend access
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include configuration and autoloader
require_once 'Config.php';
require_once 'DAOAutoloader.php';

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in production

/**
 * Simple routing function
 */
function route($path, $method) {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = str_replace('/web-project/backend/rest', '', $uri);
    
    if ($uri === $path && $_SERVER['REQUEST_METHOD'] === $method) {
        return true;
    }
    return false;
}

/**
 * Send JSON response
 */
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

/**
 * Get JSON input
 */
function getJsonInput() {
    return json_decode(file_get_contents('php://input'), true);
}

try {
    // Test route - database connection test
    if (route('/test-connection', 'GET')) {
        require_once 'services/DatabaseService.php';
        $dbService = new DatabaseService();
        $testResults = $dbService->runFullTest();
        $dbService->closeConnection();
        
        sendResponse([
            'success' => $testResults['overall_status'] === 'PASSED',
            'data' => $testResults,
            'message' => 'Database connection test completed'
        ]);
    }
    
    // Test route - get all categories
    if (route('/categories', 'GET')) {
        $categoryDAO = new CategoryDAO();
        $categories = $categoryDAO->getAllWithRecipeCount();
        sendResponse([
            'success' => true,
            'data' => $categories,
            'message' => 'Categories retrieved successfully'
        ]);
    }
    
    // Test route - get all ingredients
    if (route('/ingredients', 'GET')) {
        $ingredientDAO = new IngredientDAO();
        $ingredients = $ingredientDAO->getAllWithUsageCount();
        sendResponse([
            'success' => true,
            'data' => $ingredients,
            'message' => 'Ingredients retrieved successfully'
        ]);
    }
    
    // Test route - get all recipes
    if (route('/recipes', 'GET')) {
        $recipeDAO = new RecipeDAO();
        $recipes = $recipeDAO->getAllWithDetails();
        sendResponse([
            'success' => true,
            'data' => $recipes,
            'message' => 'Recipes retrieved successfully'
        ]);
    }
    
    // Test route - get recipe by ID
    if (preg_match('/^\/recipes\/(\d+)$/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), $matches) && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $recipeId = (int) $matches[1];
        $recipeDAO = new RecipeDAO();
        $recipe = $recipeDAO->getByIdWithDetails($recipeId);
        
        if ($recipe) {
            // Get recipe ingredients
            $recipeIngredientDAO = new RecipeIngredientDAO();
            $ingredients = $recipeIngredientDAO->getByRecipe($recipeId);
            $recipe['ingredients'] = $ingredients;
            
            sendResponse([
                'success' => true,
                'data' => $recipe,
                'message' => 'Recipe retrieved successfully'
            ]);
        } else {
            sendResponse([
                'success' => false,
                'message' => 'Recipe not found'
            ], 404);
        }
    }
    
    // Test route - API status
    if (route('/', 'GET') || route('/status', 'GET')) {
        sendResponse([
            'success' => true,
            'message' => 'Recipe Manager API is running',
            'version' => Config::APP_VERSION,
            'endpoints' => [
                'GET /categories' => 'Get all categories',
                'GET /ingredients' => 'Get all ingredients',
                'GET /recipes' => 'Get all recipes',
                'GET /recipes/{id}' => 'Get recipe by ID',
                'GET /status' => 'API status'
            ]
        ]);
    }
    
    // Default route - not found
    sendResponse([
        'success' => false,
        'message' => 'Endpoint not found'
    ], 404);
    
} catch (Exception $e) {
    sendResponse([
        'success' => false,
        'message' => 'Internal server error',
        'error' => $e->getMessage()
    ], 500);
}