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
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Updated Category Name")
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
 *     summary="Delete a category",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Category deleted")
 * )
 */
Flight::route('DELETE /categories/@id', function($id) {
    $result = Flight::categoryService()->delete($id);
    Flight::json($result, $result['success'] ? 200 : 400);
});


/**
 * @OA\Get(
 *     path="/categories/with-count",
 *     tags={"Categories"},
 *     summary="Get categories with recipe count",
 *     @OA\Response(response=200, description="List with recipe counts")
 * )
 */
Flight::route('GET /categories/with-count', function() {
    $result = Flight::categoryService()->get_all_with_recipe_count();
    Flight::json($result, 200);
});


/**
 * @OA\Get(
 *     path="/categories/ordered",
 *     tags={"Categories"},
 *     summary="Get ordered list of categories",
 *     @OA\Response(response=200, description="Sorted categories")
 * )
 */
Flight::route('GET /categories/ordered', function() {
    $result = Flight::categoryService()->get_all_ordered();
    Flight::json($result, 200);
});


/**
 * @OA\Get(
 *     path="/categories/search/{term}",
 *     tags={"Categories"},
 *     summary="Search categories",
 *     @OA\Parameter(
 *         name="term",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response=200, description="Search results")
 * )
 */
Flight::route('GET /categories/search/@term', function($term) {
    $result = Flight::categoryService()->search($term);
    Flight::json($result, 200);
});


/**
 * @OA\Get(
 *     path="/categories/can-delete/{id}",
 *     tags={"Categories"},
 *     summary="Check if category can be deleted",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Delete availability")
 * )
 */
Flight::route('GET /categories/can-delete/@id', function($id) {
    $result = Flight::categoryService()->can_delete($id);
    Flight::json($result, 200);
});


/**
 * @OA\Get(
 *     path="/categories/name/{name}",
 *     tags={"Categories"},
 *     summary="Get category by name",
 *     @OA\Parameter(
 *         name="name",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response=200, description="Category found")
 * )
 */
Flight::route('GET /categories/name/@name', function($name) {
    $result = Flight::categoryService()->get_by_name($name);
    Flight::json($result, 200);
});


/**
 * @OA\Get(
 *     path="/categories/{id}",
 *     tags={"Categories"},
 *     summary="Get category by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Category found"),
 *     @OA\Response(response=404, description="Category not found")
 * )
 *
 */
Flight::route('GET /categories/@id', function($id) {
    $result = Flight::categoryService()->get_by_id($id);
    Flight::json($result, $result['success'] ? 200 : 404);
});

