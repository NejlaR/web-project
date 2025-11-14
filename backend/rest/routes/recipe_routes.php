<?php

/**
 * ============================
 *        RECIPE ROUTES
 * ============================
 */

/**
 * @OA\Get(
 *     path="/recipes",
 *     tags={"Recipes"},
 *     summary="Get all recipes",
 *     @OA\Response(response=200, description="List of recipes")
 * )
 */
Flight::route('GET /recipes', function() {
    $result = Flight::recipeService()->get_all();
    Flight::json($result, $result['success'] ? 200 : 400);
});

/**
 * @OA\Get(
 *     path="/recipes/{id}",
 *     tags={"Recipes"},
 *     summary="Get recipe by ID",
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(response=200, description="Recipe found"),
 *     @OA\Response(response=404, description="Recipe not found")
 * )
 */
Flight::route('GET /recipes/@id', function($id) {
    $result = Flight::recipeService()->get_by_id($id);
    Flight::json($result, $result['success'] ? 200 : 404);
});

/**
 * @OA\Post(
 *     path="/recipes",
 *     tags={"Recipes"},
 *     summary="Create a new recipe",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"category_id","title"},
 *             @OA\Property(property="category_id", type="integer", example=1),
 *             @OA\Property(property="title", type="string", example="Chocolate Cake"),
 *             @OA\Property(property="description", type="string", example="Delicious chocolate dessert")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Recipe created")
 * )
 */
Flight::route('POST /recipes', function() {
    $data = Flight::request()->data->getData();
    $result = Flight::recipeService()->add($data);
    Flight::json($result, $result['success'] ? 201 : 400);
});

/**
 * @OA\Put(
 *     path="/recipes/{id}",
 *     tags={"Recipes"},
 *     summary="Update recipe",
 *     @OA\Parameter(
 *          name="id", 
 *          in="path", 
 *          required=true,
 *          @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\JsonContent(
 *             @OA\Property(property="category_id", type="integer", example=2),
 *             @OA\Property(property="title", type="string", example="Updated Cake Title"),
 *             @OA\Property(property="description", type="string", example="Updated description")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Recipe updated")
 * )
 */
Flight::route('PUT /recipes/@id', function($id) {
    $data = Flight::request()->data->getData();
    $result = Flight::recipeService()->update($data, $id);
    Flight::json($result, $result['success'] ? 200 : 400);
});

/**
 * @OA\Delete(
 *     path="/recipes/{id}",
 *     tags={"Recipes"},
 *     summary="Delete recipe",
 *     @OA\Parameter(
 *          name="id", 
 *          in="path", 
 *          required=true,
 *          @OA\Schema(type="integer", example=8)
 *     ),
 *     @OA\Response(response=200, description="Recipe deleted")
 * )
 */
Flight::route('DELETE /recipes/@id', function($id) {
    $result = Flight::recipeService()->delete($id);
    Flight::json($result, $result['success'] ? 200 : 400);
});
