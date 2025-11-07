<?php

/**
 * Category Routes - Category Management
 */

// Initialize service
$categoryService = new CategoryService();

// =============================================================================
// CATEGORY CRUD ROUTES
// =============================================================================

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

// =============================================================================
// CATEGORY STATISTICS ROUTES
// =============================================================================

// Get categories with recipe count
$app->route('GET /categories/with-count', function() use ($categoryService) {
    $result = $categoryService->getCategoriesWithRecipeCount();
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get popular categories
$app->route('GET /categories/popular', function() use ($categoryService) {
    $limit = $_GET['limit'] ?? 10;
    $result = $categoryService->getPopularCategories($limit);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get category statistics
$app->route('GET /categories/@id/stats', function($id) use ($categoryService) {
    $result = $categoryService->getCategoryStats($id);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Search categories
$app->route('GET /categories/search', function() use ($categoryService) {
    $query = $_GET['q'] ?? '';
    $result = $categoryService->search($query);
    jsonResponse($result, $result['success'] ? 200 : 400);
});