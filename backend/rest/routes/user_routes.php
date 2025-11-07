<?php

/**
 * User Routes - Authentication and User Management
 */

// Initialize service
$userService = new UserService();

// =============================================================================
// AUTHENTICATION ROUTES
// =============================================================================

// User login
$app->route('POST /auth/login', function() use ($userService) {
    $data = getRequestBody();
    $result = $userService->login($data['email'] ?? '', $data['password'] ?? '');
    jsonResponse($result, $result['success'] ? 200 : 401);
});

// User registration
$app->route('POST /auth/register', function() use ($userService) {
    $data = getRequestBody();
    $result = $userService->register($data);
    jsonResponse($result, $result['success'] ? 201 : 400);
});

// Logout (for session-based auth)
$app->route('POST /auth/logout', function() {
    jsonResponse([
        'success' => true,
        'message' => 'Logged out successfully'
    ]);
});

// =============================================================================
// USER MANAGEMENT ROUTES
// =============================================================================

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

// Get user profile (current user)
$app->route('GET /users/profile', function() use ($userService) {
    // In a real app, you'd get user ID from JWT token or session
    $userId = $_GET['user_id'] ?? 1; 
    $result = $userService->getById($userId);
    jsonResponse($result, $result['success'] ? 200 : 404);
});

// Update user profile
$app->route('PUT /users/profile', function() use ($userService) {
    $data = getRequestBody();
    $userId = $data['user_id'] ?? 1; // In real app, get from token
    unset($data['user_id']); // Remove from update data
    
    $result = $userService->update($userId, $data);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Change password
$app->route('PUT /users/@id/password', function($id) use ($userService) {
    $data = getRequestBody();
    $result = $userService->changePassword($id, $data['old_password'] ?? '', $data['new_password'] ?? '');
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Search users
$app->route('GET /users/search', function() use ($userService) {
    $query = $_GET['q'] ?? '';
    $result = $userService->search($query);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get users by role
$app->route('GET /users/role/@roleId', function($roleId) use ($userService) {
    $result = $userService->getUsersByRole($roleId);
    jsonResponse($result, $result['success'] ? 200 : 400);
});