<?php

/**
 * ============================
 *          USER ROUTES
 * ============================
 */

/**
 * @OA\Get(
 *     path="/users",
 *     tags={"Users"},
 *     summary="Get all users",
 *     @OA\Response(response=200, description="List of users")
 * )
 */
Flight::route('GET /users', function() {
    $result = Flight::userService()->get_all();
    Flight::json($result, $result['success'] ? 200 : 400);
});


/**
 * @OA\Get(
 *     path="/users/{id}",
 *     tags={"Users"},
 *     summary="Get user by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="User found"),
 *     @OA\Response(response=404, description="User not found")
 * )
 */
Flight::route('GET /users/@id', function($id) {
    $result = Flight::userService()->get_by_id($id);
    Flight::json($result, $result['success'] ? 200 : 404);
});


/**
 * @OA\Post(
 *     path="/users",
 *     tags={"Users"},
 *     summary="Create a new user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", example="john@gmail.com"),
 *             @OA\Property(property="password", type="string", example="123456"),
 *             @OA\Property(property="role_id", type="integer", example=2)
 *         )
 *     ),
 *     @OA\Response(response=201, description="User created")
 * )
 */
Flight::route('POST /users', function() {
    $data = Flight::request()->data->getData();
    $result = Flight::userService()->add($data);
    Flight::json($result, $result['success'] ? 201 : 400);
});


/**
 * @OA\Put(
 *     path="/users/{id}",
 *     tags={"Users"},
 *     summary="Update a user",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string"),
 *             @OA\Property(property="role_id", type="integer")
 *         )
 *     ),
 *     @OA\Response(response=200, description="User updated")
 * )
 */
Flight::route('PUT /users/@id', function($id) {
    $data = Flight::request()->data->getData();
    $result = Flight::userService()->update($data, $id);
    Flight::json($result, $result['success'] ? 200 : 400);
});


/**
 * @OA\Delete(
 *     path="/users/{id}",
 *     tags={"Users"},
 *     summary="Delete a user",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="User deleted")
 * )
 */
Flight::route('DELETE /users/@id', function($id) {
    $result = Flight::userService()->delete($id);
    Flight::json($result, $result['success'] ? 200 : 400);
});


/**
 * @OA\Get(
 *     path="/users/email/{email}",
 *     tags={"Users"},
 *     summary="Get user by email",
 *     @OA\Parameter(
 *         name="email",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response=200, description="User found")
 * )
 */
Flight::route('GET /users/email/@email', function($email) {
    $result = Flight::userService()->get_by_email($email);
    Flight::json($result, 200);
});


/**
 * @OA\Get(
 *     path="/users/{id}/role",
 *     tags={"Users"},
 *     summary="Get user with role data",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="User and role info")
 * )
 */
Flight::route('GET /users/@id/role', function($id) {
    $result = Flight::userService()->get_by_id_with_role($id);
    Flight::json($result, 200);
});


/**
 * @OA\Get(
 *     path="/users-with-roles",
 *     tags={"Users"},
 *     summary="Get all users with roles",
 *     @OA\Response(response=200, description="Users with roles")
 * )
 */
Flight::route('GET /users-with-roles', function() {
    $result = Flight::userService()->get_all_with_roles();
    Flight::json($result, 200);
});


/**
 * @OA\Get(
 *     path="/users/email-exists/{email}",
 *     tags={"Users"},
 *     summary="Check if email exists",
 *     @OA\Parameter(
 *         name="email",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response=200, description="Email existence result")
 * )
 */
Flight::route('GET /users/email-exists/@email', function($email) {
    $result = Flight::userService()->email_exists($email);
    Flight::json($result, 200);
});


/**
 * @OA\Get(
 *     path="/users/role/{role_id}",
 *     tags={"Users"},
 *     summary="Get users by role",
 *     @OA\Parameter(
 *         name="role_id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Users by role")
 * )
 */
Flight::route('GET /users/role/@role_id', function($role_id) {
    $result = Flight::userService()->get_by_role($role_id);
    Flight::json($result, 200);
});


/**
 * @OA\Get(
 *     path="/users/search/{term}",
 *     tags={"Users"},
 *     summary="Search users by name or email",
 *     @OA\Parameter(
 *         name="term",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response=200, description="Search results")
 * )
 */
Flight::route('GET /users/search/@term', function($term) {
    $result = Flight::userService()->search($term);
    Flight::json($result, 200);
});

?>
