<?php

/**
 * =============================
 *      INGREDIENT ROUTES
 * =============================
 */

/**
 * @OA\Get(
 *     path="/ingredients",
 *     tags={"Ingredients"},
 *     summary="Get all ingredients",
 *     @OA\Response(response=200, description="List of ingredients")
 * )
 */
Flight::route('GET /ingredients', function() {
    $result = Flight::ingredientService()->get_all();
    Flight::json($result, $result['success'] ? 200 : 400);
});

/**
 * @OA\Get(
 *     path="/ingredients/{id}",
 *     tags={"Ingredients"},
 *     summary="Get ingredient by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(response=200, description="Ingredient found"),
 *     @OA\Response(response=404, description="Ingredient not found")
 * )
 */
Flight::route('GET /ingredients/@id', function($id) {
    $result = Flight::ingredientService()->get_by_id($id);
    Flight::json($result, $result['success'] ? 200 : 404);
});

/**
 * @OA\Post(
 *     path="/ingredients",
 *     tags={"Ingredients"},
 *     summary="Create a new ingredient",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"recipe_id","name"},
 *             @OA\Property(property="recipe_id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Sugar"),
 *             @OA\Property(property="quantity", type="string", example="100g")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Ingredient created")
 * )
 */
Flight::route('POST /ingredients', function() {
    $data = Flight::request()->data->getData();
    $result = Flight::ingredientService()->add($data);
    Flight::json($result, $result['success'] ? 201 : 400);
});

/**
 * @OA\Put(
 *     path="/ingredients/{id}",
 *     tags={"Ingredients"},
 *     summary="Update ingredient",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=4)
 *     ),
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\JsonContent(
 *             @OA\Property(property="recipe_id", type="integer", example=2),
 *             @OA\Property(property="name", type="string", example="Updated Ingredient"),
 *             @OA\Property(property="quantity", type="string", example="200g")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Ingredient updated")
 * )
 */
Flight::route('PUT /ingredients/@id', function($id) {
    $data = Flight::request()->data->getData();
    $result = Flight::ingredientService()->update($data, $id);
    Flight::json($result, $result['success'] ? 200 : 400);
});

/**
 * @OA\Delete(
 *     path="/ingredients/{id}",
 *     tags={"Ingredients"},
 *     summary="Delete ingredient",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(response=200, description="Ingredient deleted")
 * )
 */
Flight::route('DELETE /ingredients/@id', function($id) {
    $result = Flight::ingredientService()->delete($id);
    Flight::json($result, $result['success'] ? 200 : 400);
});
