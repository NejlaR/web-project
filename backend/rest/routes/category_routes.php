<?php

/**
 * ============================
 *       CATEGORY ROUTES
 * ============================
 */

/**
 * @OA\Get(
 *     path="/categories",
 *     tags={"Categories"},
 *     summary="Get all categories",
 *     @OA\Response(response=200, description="List of categories")
 * )
 */
Flight::route('GET /categories', function() {
    $result = Flight::categoryService()->get_all();
    Flight::json($result, $result['success'] ? 200 : 400);
});

/**
 * @OA\Get(
 *     path="/categories/{id}",
 *     tags={"Categories"},
 *     summary="Get category by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Category found"),
 *     @OA\Response(response=404, description="Category not found")
 * )
 */
Flight::route('GET /categories/@id', function($id) {
    $result = Flight::categoryService()->get_by_id($id);
    Flight::json($result, $result['success'] ? 200 : 404);
});

/**
 * @OA\Post(
 *     path="/categories",
 *     tags={"Categories"},
 *     summary="Create a new category",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Desserts")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Category created")
 * )
 */
Flight::route('POST /categories', function() {
    $data = Flight::request()->data->getData();
    $result = Flight::categoryService()->add($data);
    Flight::json($result, $result['success'] ? 201 : 400);
});

/**
 * @OA\Put(
 *     path="/categories/{id}",
 *     tags={"Categories"},
 *     summary="Update category",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Updated category name")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Category updated")
 * )
 */
Flight::route('PUT /categories/@id', function($id) {
    $data = Flight::request()->data->getData();
    $result = Flight::categoryService()->update($data, $id);
    Flight::json($result, $result['success'] ? 200 : 400);
});

/**
 * @OA\Delete(
 *     path="/categories/{id}",
 *     tags={"Categories"},
 *     summary="Delete category",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Category deleted")
 * )
 */
Flight::route('DELETE /categories/@id', function($id) {
    $result = Flight::categoryService()->delete($id);
    Flight::json($result, $result['success'] ? 200 : 400);
});
