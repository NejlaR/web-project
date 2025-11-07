<?php

/**
 * Role Routes - Role Management
 */

// Initialize service
$roleService = new RoleService();

// =============================================================================
// ROLE CRUD ROUTES
// =============================================================================

// Get all roles
$app->route('GET /roles', function() use ($roleService) {
    $result = $roleService->getAll();
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Get role by ID
$app->route('GET /roles/@id', function($id) use ($roleService) {
    $result = $roleService->getById($id);
    jsonResponse($result, $result['success'] ? 200 : 404);
});

// Create new role
$app->route('POST /roles', function() use ($roleService) {
    $data = getRequestBody();
    $result = $roleService->create($data);
    jsonResponse($result, $result['success'] ? 201 : 400);
});

// Update role
$app->route('PUT /roles/@id', function($id) use ($roleService) {
    $data = getRequestBody();
    $result = $roleService->update($id, $data);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Delete role
$app->route('DELETE /roles/@id', function($id) use ($roleService) {
    $result = $roleService->delete($id);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// =============================================================================
// ROLE UTILITY ROUTES
// =============================================================================

// Get role by name
$app->route('GET /roles/name/@name', function($name) use ($roleService) {
    $result = $roleService->getByName(urldecode($name));
    jsonResponse($result, $result['success'] ? 200 : 404);
});

// Get users by role
$app->route('GET /roles/@id/users', function($id) use ($roleService) {
    $result = $roleService->getUsersByRole($id);
    jsonResponse($result, $result['success'] ? 200 : 400);
});

// Search roles
$app->route('GET /roles/search', function() use ($roleService) {
    $query = $_GET['q'] ?? '';
    $result = $roleService->search($query);
    jsonResponse($result, $result['success'] ? 200 : 400);
});