<?php

/**
 * =======================================
 *       RECIPE - INGREDIENT ROUTES
 * =======================================
 */

/**
 * @OA\Get(
 *     path="/recipe-ingredients",
 *     tags={"RecipeIngredients"},
 *     summary="Get all recipe-ingredient relationships",
 *     @OA\Response(response=200, description="List of recipe-ingredient entries")
 * )
 */
Flight::route('GET /recipe-ingredients', function() {
    $result = Flight::recipeIngredientService()->get_all();
    Flight::json($result, $result['success'] ? 200 : 400);
});

/**
 * @OA\Get(
 *     path="/recipe-ingredients/{id}",
 *     tags={"RecipeIngredients"},
 *     summary="Get a recipe-ingredient entry by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(response=200, description="Entry found"),
 *     @OA\Response(response=404, description="Entry not found")
 * )
 */
Flight::route('GET /recipe-ingredients/@id', function($id) {
    $result = Flight::recipeIngredientService()->get_by_id($id);
    Flight::json($result, $result['success'] ? 200 : 404);
});

/**
 * @OA\Post(
 *     path="/recipe-ingredients",
 *     tags={"RecipeIngredients"},
 *     summary="Create a recipe-ingredient entry",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"recipe_id","ingredient_id"},
 *             @OA\Property(property="recipe_id", type="integer", example=1),
 *             @OA\Property(property="ingredient_id", type="integer", example=5)
 *         )
 *     ),
 *     @OA\Response(response=201, description="Entry created")
 * )
 */
Flight::route('POST /recipe-ingredients', function() {
    $data = Flight::request()->data->getData();
    $result = Flight::recipeIngredientService()->add($data);
    Flight::json($result, $result['success'] ? 201 : 400);
});

/**
 * @OA\Put(
 *     path="/recipe-ingredients/{id}",
 *     tags={"RecipeIngredients"},
 *     summary="Update a recipe-ingredient entry",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\JsonContent(
 *             @OA\Property(property="recipe_id", type="integer", example=2),
 *             @OA\Property(property="ingredient_id", type="integer", example=10)
 *         )
 *     ),
 *     @OA\Response(response=200, description="Entry updated")
 * )
 */
Flight::route('PUT /recipe-ingredients/@id', function($id) {
    $data = Flight::request()->data->getData();
    $result = Flight::recipeIngredientService()->update($data, $id);
    Flight::json($result, $result['success'] ? 200 : 400);
});

/**
 * @OA\Delete(
 *     path="/recipe-ingredients/{id}",
 *     tags={"RecipeIngredients"},
 *     summary="Delete a recipe-ingredient entry",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=8)
 *     ),
 *     @OA\Response(response=200, description="Entry deleted")
 * )
 */
Flight::route('DELETE /recipe-ingredients/@id', function($id) {
    $result = Flight::recipeIngredientService()->delete($id);
    Flight::json($result, $result['success'] ? 200 : 400);
});
