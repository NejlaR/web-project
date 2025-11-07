<?php

/**
 * Ingredient Routes - Ingredient Management
 */

// Initialize service
$ingredientService = new IngredientService();

// =============================================================================
// INGREDIENT CRUD ROUTES
// =============================================================================

// Get all ingredients
$app->route('GET /ingredients', function() use ($ingredientService) {
    $limit = $_GET['limit'] ?? 100;
    $offset = $_GET['offset'] ?? 0;
    
    $result = $ingredientService->getAll($limit, $offset);
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

// =============================================================================
// INGREDIENT SEARCH AND FILTERING ROUTES
// =============================================================================

// Search ingredients
$app->route('GET /ingredients/search', function() use ($ingredientService) {
    $query = $_GET['q'] ?? '';
    $result = $ingredientService->search($query);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get ingredient by name
$app->route('GET /ingredients/name/@name', function($name) use ($ingredientService) {
    $result = $ingredientService->getByName(urldecode($name));
    jsonResponse($result, $result['success'] ? 200 : 404);
});

// =============================================================================
// INGREDIENT STATISTICS ROUTES
// =============================================================================

// Get ingredients with usage statistics
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

// Get recipes using ingredient
$app->route('GET /ingredients/@id/recipes', function($id) use ($ingredientService) {
    $result = $ingredientService->getRecipesUsingIngredient($id);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get ingredient statistics
$app->route('GET /ingredients/@id/stats', function($id) use ($ingredientService) {
    $result = $ingredientService->getIngredientStats($id);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// =============================================================================
// INGREDIENT NUTRITIONAL ROUTES
// =============================================================================

// Get ingredients by nutritional criteria
$app->route('GET /ingredients/nutrition/high-protein', function() use ($ingredientService) {
    $result = $ingredientService->getHighProteinIngredients();
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get low calorie ingredients
$app->route('GET /ingredients/nutrition/low-calorie', function() use ($ingredientService) {
    $result = $ingredientService->getLowCalorieIngredients();
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get ingredients by allergen
$app->route('GET /ingredients/allergen/@type', function($type) use ($ingredientService) {
    $result = $ingredientService->getIngredientsByAllergen($type);
    jsonResponse($result, $result['success'] ? 200 : 400);
});