<?php

/**
 * ============================
 *          ROLE ROUTES
 * ============================
 */

/**
 * @OA\Get(
 *     path="/role",
 *     tags={"Role"},
 *     summary="Get all roles",
 *     @OA\Response(response=200, description="List of roles")
 * )
 */
Flight::route('GET /role', function() {
    $result = Flight::roleService()->get_all();
    Flight::json($result, $result['success'] ? 200 : 400);
});

/**
 * @OA\Get(
 *     path="/role/{id}",
 *     tags={"Role"},
 *     summary="Get role by ID",
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(response=200, description="Role found"),
 *     @OA\Response(response=404, description="Role not found")
 * )
 */
Flight::route('GET /role/@id', function($id) {
    $result = Flight::roleService()->get_by_id($id);
    Flight::json($result, $result['success'] ? 200 : 404);
});

/**
 * @OA\Post(
 *     path="/role",
 *     tags={"Role"},
 *     summary="Create a new role",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Admin")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Role created")
 * )
 */
Flight::route('POST /role', function() {
    $data = Flight::request()->data->getData();
    $result = Flight::roleService()->add($data);
    Flight::json($result, $result['success'] ? 201 : 400);
});

/**
 * @OA\Put(
 *     path="/role/{id}",
 *     tags={"Role"},
 *     summary="Update role",
 *     @OA\Parameter(
 *          name="id", 
 *          in="path", 
 *          required=true,
 *          @OA\Schema(type="integer", example=2)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Updated Role Name")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Role updated")
 * )
 */
Flight::route('PUT /role/@id', function($id) {
    $data = Flight::request()->data->getData();
    $result = Flight::roleService()->update($data, $id);
    Flight::json($result, $result['success'] ? 200 : 400);
});

/**
 * @OA\Delete(
 *     path="/role/{id}",
 *     tags={"Role"},
 *     summary="Delete role",
 *     @OA\Parameter(
 *          name="id", 
 *          in="path", 
 *          required=true,
 *          @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(response=200, description="Role deleted")
 * )
 */
Flight::route('DELETE /role/@id', function($id) {
    $result = Flight::roleService()->delete($id);
    Flight::json($result, $result['success'] ? 200 : 400);
});
