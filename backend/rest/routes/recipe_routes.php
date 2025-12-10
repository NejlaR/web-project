<?php

/**
 * =====================
 *       RECIPES
 * =====================
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
    Flight::json($result, 200);
});


/**
 * @OA\Get(
 *     path="/recipes/{id}",
 *     tags={"Recipes"},
 *     summary="Get recipe by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Recipe found"),
 *     @OA\Response(response=404, description="Recipe not found")
 * )
 */
Flight::route('GET /recipes/@id', function($id) {
    $result = Flight::recipeService()->get_by_id($id);
    Flight::json($result, $result["success"] ? 200 : 404);
});


/**
 * @OA\Post(
 *     path="/recipes",
 *     tags={"Recipes"},
 *     summary="Create a new recipe",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"category_id", "title"},
 *             @OA\Property(property="category_id", type="integer", example=1),
 *             @OA\Property(property="title", type="string", example="Pita od jabuka"),
 *             @OA\Property(property="description", type="string", example="Ukusna domaća pita")
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
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="category_id", type="integer"),
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="description", type="string")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Recipe updated")
 * )
 */
Flight::route('PUT /recipes/@id', function($id) {
    $data = Flight::request()->data->getData();
    $result = Flight::recipeService()->update($data, $id);
    Flight::json($result, $result["success"] ? 200 : 400);
});


/**
 * @OA\Delete(
 *     path="/recipes/{id}",
 *     tags={"Recipes"},
 *     summary="Delete recipe",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Recipe deleted")
 * )
 */
Flight::route('DELETE /recipes/@id', function($id) {
    $result = Flight::recipeService()->delete($id);
    Flight::json($result, $result["success"] ? 200 : 400);
});


/**
 * ================================
 *   RECIPE DETAILS ROUTES
 * ================================
 */

/**
 * @OA\Get(
 *     path="/recipes/details",
 *     tags={"Recipes"},
 *     summary="Get all recipes with details",
 *     @OA\Response(response=200, description="Detailed recipe list")
 * )
 */
Flight::route('GET /recipes/details', function() {
    $result = Flight::recipeService()->get_all_with_details();
    Flight::json($result, 200);
});


/**
 * @OA\Get(
 *     path="/recipes/{id}/details",
 *     tags={"Recipes"},
 *     summary="Get recipe with details",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Recipe details"),
 *     @OA\Response(response=404, description="Recipe not found")
 * )
 */
Flight::route('GET /recipes/@id/details', function($id) {
    $result = Flight::recipeService()->get_by_id_with_details($id);

    if ($result["data"] === null) {
        Flight::json([
            "success" => false,
            "data" => null,
            "message" => "Recipe not found"
        ], 404);
        return;
    }

    Flight::json($result, 200);
});


/**
 * ================================
 *   USER RECIPES
 * ================================
 */

/**
 * @OA\Get(
 *     path="/recipes/user/{user_id}",
 *     tags={"Recipes"},
 *     summary="Get recipes by user ID",
 *     @OA\Parameter(name="user_id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="User recipes")
 * )
 */
Flight::route('GET /recipes/user/@user_id', function($user_id) {
    $result = Flight::recipeService()->get_by_user($user_id);
    Flight::json($result, 200);
});


/**
 * ================================
 *   SEARCH RECIPES
 * ================================
 */

/**
 * @OA\Get(
 *     path="/recipes/search/{term}",
 *     tags={"Recipes"},
 *     summary="Search recipes",
 *     @OA\Parameter(name="term", in="path", required=true, @OA\Schema(type="string")),
 *     @OA\Response(response=200, description="Search results")
 * )
 */
Flight::route('GET /recipes/search/@term', function($term) {
    $result = Flight::recipeService()->search($term);
    Flight::json($result, 200);
});
