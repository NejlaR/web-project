<?php

/**
 * ============================
 *        REVIEW ROUTES
 * ============================
 */

/**
 * @OA\Get(
 *     path="/reviews",
 *     tags={"Reviews"},
 *     summary="Get all reviews",
 *     @OA\Response(response=200, description="List of all reviews")
 * )
 */
Flight::route('GET /reviews', function() {
    $result = Flight::reviewService()->get_all();
    Flight::json($result, $result['success'] ? 200 : 400);
});


/**
 * @OA\Get(
 *     path="/reviews/{id}",
 *     tags={"Reviews"},
 *     summary="Get review by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Review ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(response=200, description="Review found"),
 *     @OA\Response(response=404, description="Review not found")
 * )
 */
Flight::route('GET /reviews/@id', function($id) {
    $result = Flight::reviewService()->get_by_id($id);
    Flight::json($result, $result['success'] ? 200 : 404);
});


/**
 * @OA\Post(
 *     path="/reviews",
 *     tags={"Reviews"},
 *     summary="Create a new review",
 *     description="Add a rating and comment for a recipe.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"recipe_id","user_id","rating"},
 *             @OA\Property(property="recipe_id", type="integer", example=3),
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="rating", type="integer", example=5),
 *             @OA\Property(property="comment", type="string", example="Amazing recipe!")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Review created successfully"),
 *     @OA\Response(response=400, description="Invalid data")
 * )
 */
Flight::route('POST /reviews', function() {
    $data = Flight::request()->data->getData();
    $result = Flight::reviewService()->add($data);
    Flight::json($result, $result['success'] ? 201 : 400);
});


/**
 * @OA\Put(
 *     path="/reviews/{id}",
 *     tags={"Reviews"},
 *     summary="Update an existing review",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\JsonContent(
 *             @OA\Property(property="rating", type="integer", example=4),
 *             @OA\Property(property="comment", type="string", example="Updated comment text")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Review updated successfully")
 * )
 */
Flight::route('PUT /reviews/@id', function($id) {
    $data = Flight::request()->data->getData();
    $result = Flight::reviewService()->update($data, $id);
    Flight::json($result, $result['success'] ? 200 : 400);
});


/**
 * @OA\Delete(
 *     path="/reviews/{id}",
 *     tags={"Reviews"},
 *     summary="Delete review by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=2)
 *     ),
 *     @OA\Response(response=200, description="Review deleted successfully")
 * )
 */
Flight::route('DELETE /reviews/@id', function($id) {
    $result = Flight::reviewService()->delete($id);
    Flight::json($result, $result['success'] ? 200 : 400);
});
