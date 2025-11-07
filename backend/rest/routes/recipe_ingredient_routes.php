<?php

/**
 * Recipe Ingredient Routes - Recipe-Ingredient Relationship Management
 */

// Initialize service
$recipeIngredientService = new RecipeIngredientService();

// =============================================================================
// RECIPE INGREDIENT CRUD ROUTES
// =============================================================================

// Get all recipe ingredients
$app->route('GET /recipe-ingredients', function() use ($recipeIngredientService) {
    $result = $recipeIngredientService->getAll();
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get recipe ingredient by ID
$app->route('GET /recipe-ingredients/@id', function($id) use ($recipeIngredientService) {
    $result = $recipeIngredientService->getById($id);
    jsonResponse($result, $result['success'] ? 200 : 404);
});

// Add ingredient to recipe
$app->route('POST /recipe-ingredients', function() use ($recipeIngredientService) {
    $data = getRequestBody();
    $result = $recipeIngredientService->create($data);
    jsonResponse($result, $result['success'] ? 201 : 400);
});

// Update recipe ingredient
$app->route('PUT /recipe-ingredients/@id', function($id) use ($recipeIngredientService) {
    $data = getRequestBody();
    $result = $recipeIngredientService->update($id, $data);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Remove ingredient from recipe
$app->route('DELETE /recipe-ingredients/@id', function($id) use ($recipeIngredientService) {
    $result = $recipeIngredientService->delete($id);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// =============================================================================
// RECIPE-SPECIFIC INGREDIENT ROUTES
// =============================================================================

// Get ingredients for specific recipe
$app->route('GET /recipe-ingredients/recipe/@recipeId', function($recipeId) use ($recipeIngredientService) {
    $result = $recipeIngredientService->getByRecipeId($recipeId);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get recipes using specific ingredient
$app->route('GET /recipe-ingredients/ingredient/@ingredientId', function($ingredientId) use ($recipeIngredientService) {
    $result = $recipeIngredientService->getByIngredientId($ingredientId);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Add multiple ingredients to recipe
$app->route('POST /recipe-ingredients/recipe/@recipeId/bulk', function($recipeId) use ($recipeIngredientService) {
    $data = getRequestBody();
    $ingredients = $data['ingredients'] ?? [];
    $result = $recipeIngredientService->addIngredientsToRecipe($recipeId, $ingredients);
    jsonResponse($result, $result['success'] ? 201 : 400);
});

// Update recipe ingredient by recipe and ingredient ID
$app->route('PUT /recipe-ingredients/recipe/@recipeId/ingredient/@ingredientId', function($recipeId, $ingredientId) use ($recipeIngredientService) {
    $data = getRequestBody();
    $result = $recipeIngredientService->updateRecipeIngredient($recipeId, $ingredientId, $data);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Remove specific ingredient from recipe
$app->route('DELETE /recipe-ingredients/recipe/@recipeId/ingredient/@ingredientId', function($recipeId, $ingredientId) use ($recipeIngredientService) {
    $result = $recipeIngredientService->removeIngredientFromRecipe($recipeId, $ingredientId);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// =============================================================================
// RECIPE INGREDIENT UTILITY ROUTES
// =============================================================================

// Get recipe shopping list
$app->route('GET /recipe-ingredients/recipe/@recipeId/shopping-list', function($recipeId) use ($recipeIngredientService) {
    $result = $recipeIngredientService->getShoppingList($recipeId);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get recipe nutritional summary
$app->route('GET /recipe-ingredients/recipe/@recipeId/nutrition', function($recipeId) use ($recipeIngredientService) {
    $result = $recipeIngredientService->getNutritionalSummary($recipeId);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Scale recipe ingredients
$app->route('POST /recipe-ingredients/recipe/@recipeId/scale', function($recipeId) use ($recipeIngredientService) {
    $data = getRequestBody();
    $scaleFactor = $data['scale_factor'] ?? 1;
    $result = $recipeIngredientService->scaleRecipe($recipeId, $scaleFactor);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Find ingredient substitutes
$app->route('GET /recipe-ingredients/ingredient/@ingredientId/substitutes', function($ingredientId) use ($recipeIngredientService) {
    $result = $recipeIngredientService->getIngredientSubstitutes($ingredientId);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// =============================================================================
// RECIPE INGREDIENT ANALYTICS ROUTES
// =============================================================================

// Get ingredient usage analytics
$app->route('GET /recipe-ingredients/analytics/ingredient-usage', function() use ($recipeIngredientService) {
    $result = $recipeIngredientService->getIngredientUsageAnalytics();
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get recipe complexity (by ingredient count)
$app->route('GET /recipe-ingredients/analytics/complexity', function() use ($recipeIngredientService) {
    $result = $recipeIngredientService->getRecipeComplexityAnalytics();
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get common ingredient combinations
$app->route('GET /recipe-ingredients/analytics/combinations', function() use ($recipeIngredientService) {
    $result = $recipeIngredientService->getCommonIngredientCombinations();
    jsonResponse($result, $result['success'] ? 200 : 400);
});