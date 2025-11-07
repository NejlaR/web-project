<?php

/**
 * Review Routes - Review and Rating Management
 */

// Initialize service
$reviewService = new ReviewService();

// =============================================================================
// REVIEW CRUD ROUTES
// =============================================================================

// Get all reviews
$app->route('GET /reviews', function() use ($reviewService) {
    $limit = $_GET['limit'] ?? 20;
    $offset = $_GET['offset'] ?? 0;
    
    $result = $reviewService->getAll($limit, $offset);
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

// =============================================================================
// REVIEW FILTERING ROUTES
// =============================================================================

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

// Get reviews by rating
$app->route('GET /reviews/rating/@rating', function($rating) use ($reviewService) {
    $result = $reviewService->getReviewsByRating($rating);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get recent reviews
$app->route('GET /reviews/recent', function() use ($reviewService) {
    $limit = $_GET['limit'] ?? 10;
    $result = $reviewService->getRecentReviews($limit);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// =============================================================================
// RATING STATISTICS ROUTES
// =============================================================================

// Get recipe rating statistics
$app->route('GET /reviews/recipe/@recipeId/rating', function($recipeId) use ($reviewService) {
    $result = $reviewService->getRecipeRating($recipeId);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get top rated recipes
$app->route('GET /reviews/top-rated', function() use ($reviewService) {
    $limit = $_GET['limit'] ?? 10;
    $result = $reviewService->getTopRatedRecipes($limit);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Check if user reviewed recipe
$app->route('GET /reviews/check/@userId/@recipeId', function($userId, $recipeId) use ($reviewService) {
    $result = $reviewService->hasUserReviewedRecipe($userId, $recipeId);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// =============================================================================
// REVIEW MODERATION ROUTES
// =============================================================================

// Flag review for moderation
$app->route('POST /reviews/@id/flag', function($id) use ($reviewService) {
    $data = getRequestBody();
    $result = $reviewService->flagReview($id, $data['reason'] ?? '');
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get flagged reviews (admin only)
$app->route('GET /reviews/flagged', function() use ($reviewService) {
    $result = $reviewService->getFlaggedReviews();
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Approve review (admin only)
$app->route('POST /reviews/@id/approve', function($id) use ($reviewService) {
    $result = $reviewService->approveReview($id);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// =============================================================================
// REVIEW ANALYTICS ROUTES
// =============================================================================

// Get review analytics
$app->route('GET /reviews/analytics', function() use ($reviewService) {
    $result = $reviewService->getReviewAnalytics();
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get rating distribution
$app->route('GET /reviews/rating-distribution', function() use ($reviewService) {
    $result = $reviewService->getRatingDistribution();
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get user review statistics
$app->route('GET /reviews/user/@userId/stats', function($userId) use ($reviewService) {
    $result = $reviewService->getUserReviewStats($userId);
    jsonResponse($result, $result['success'] ? 200 : 400);
});