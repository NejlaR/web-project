<?php

/**
 * Recipe Routes - Recipe Management and Search
 */

// Initialize service
$recipeService = new RecipeService();

// =============================================================================
// RECIPE CRUD ROUTES
// =============================================================================

// Get all recipes
$app->route('GET /recipes', function() use ($recipeService) {
    $limit = $_GET['limit'] ?? 50;
    $offset = $_GET['offset'] ?? 0;
    
    $result = $recipeService->getAll($limit, $offset);
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

// =============================================================================
// RECIPE SEARCH AND FILTERING ROUTES
// =============================================================================

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

// Get recipes by difficulty
$app->route('GET /recipes/difficulty/@level', function($level) use ($recipeService) {
    $result = $recipeService->getRecipesByDifficulty($level);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get popular recipes
$app->route('GET /recipes/popular', function() use ($recipeService) {
    $limit = $_GET['limit'] ?? 10;
    $result = $recipeService->getPopularRecipes($limit);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get recent recipes
$app->route('GET /recipes/recent', function() use ($recipeService) {
    $limit = $_GET['limit'] ?? 10;
    $result = $recipeService->getRecentRecipes($limit);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get featured recipes
$app->route('GET /recipes/featured', function() use ($recipeService) {
    $limit = $_GET['limit'] ?? 5;
    $result = $recipeService->getFeaturedRecipes($limit);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// =============================================================================
// RECIPE STATISTICS ROUTES
// =============================================================================

// Get recipe statistics
$app->route('GET /recipes/@id/stats', function($id) use ($recipeService) {
    $result = $recipeService->getRecipeStats($id);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get cooking time statistics
$app->route('GET /recipes/stats/cooking-times', function() use ($recipeService) {
    $result = $recipeService->getCookingTimeStats();
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get recipe count by category
$app->route('GET /recipes/stats/by-category', function() use ($recipeService) {
    $result = $recipeService->getRecipeCountByCategory();
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// =============================================================================
// RECIPE INTERACTION ROUTES
// =============================================================================

// Like/Unlike recipe (toggle)
$app->route('POST /recipes/@id/like', function($id) use ($recipeService) {
    $data = getRequestBody();
    $userId = $data['user_id'] ?? 1; // In real app, get from token
    $result = $recipeService->toggleLike($id, $userId);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Favorite/Unfavorite recipe (toggle)
$app->route('POST /recipes/@id/favorite', function($id) use ($recipeService) {
    $data = getRequestBody();
    $userId = $data['user_id'] ?? 1; // In real app, get from token
    $result = $recipeService->toggleFavorite($id, $userId);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get user's favorite recipes
$app->route('GET /recipes/favorites', function() use ($recipeService) {
    $userId = $_GET['user_id'] ?? 1; // In real app, get from token
    $result = $recipeService->getUserFavorites($userId);
    jsonResponse($result, $result['success'] ? 200 : 400);
});