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
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
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
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"recipe_id", "user_id", "rating"},
 *             @OA\Property(property="recipe_id", type="integer", example=3),
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="rating", type="integer", example=5, minimum=1, maximum=5),
 *             @OA\Property(property="comment", type="string", example="Odličan recept!")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Review created")
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
 *     summary="Update a review",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\JsonContent(
 *             @OA\Property(property="rating", type="integer", example=4, minimum=1, maximum=5),
 *             @OA\Property(property="comment", type="string", example="Ipak bih malo izmijenila recept.")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Review updated")
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
 *     summary="Delete review",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
 *     @OA\Response(response=200, description="Review deleted")
 * )
 */
Flight::route('DELETE /reviews/@id', function($id) {
    $result = Flight::reviewService()->delete($id);
    Flight::json($result, $result['success'] ? 200 : 400);
});


/* =======================================================
 *        EXTRA ROUTES — REVIEW UTILITIES
 * ======================================================= */

/**
 * @OA\Get(
 *     path="/reviews/recipe/{recipe_id}",
 *     tags={"Reviews"},
 *     summary="Get all reviews for a recipe",
 *     @OA\Parameter(name="recipe_id", in="path", required=true, @OA\Schema(type="integer", example=1)),
 *     @OA\Response(response=200, description="Reviews retrieved")
 * )
 */
Flight::route('GET /reviews/recipe/@recipe_id', function($recipe_id) {
    $result = Flight::reviewService()->get_by_recipe($recipe_id);
    Flight::json($result, 200);
});


/**
 * @OA\Get(
 *     path="/reviews/user/{user_id}",
 *     tags={"Reviews"},
 *     summary="Get all reviews by a user",
 *     @OA\Parameter(name="user_id", in="path", required=true, @OA\Schema(type="integer", example=1)),
 *     @OA\Response(response=200, description="Reviews retrieved")
 * )
 */
Flight::route('GET /reviews/user/@user_id', function($user_id) {
    $result = Flight::reviewService()->get_by_user($user_id);
    Flight::json($result, 200);
});


/**
 * @OA\Get(
 *     path="/reviews/average/{recipe_id}",
 *     tags={"Reviews"},
 *     summary="Get average rating for a recipe",
 *     @OA\Parameter(name="recipe_id", in="path", required=true, @OA\Schema(type="integer", example=1)),
 *     @OA\Response(response=200, description="Average rating retrieved")
 * )
 */
Flight::route('GET /reviews/average/@recipe_id', function($recipe_id) {
    $result = Flight::reviewService()->get_average_rating($recipe_id);
    Flight::json($result, 200);
});


/**
 * @OA\Get(
 *     path="/reviews/count/{recipe_id}",
 *     tags={"Reviews"},
 *     summary="Get total number of reviews for a recipe",
 *     @OA\Parameter(name="recipe_id", in="path", required=true, @OA\Schema(type="integer", example=1)),
 *     @OA\Response(response=200, description="Review count retrieved")
 * )
 */
Flight::route('GET /reviews/count/@recipe_id', function($recipe_id) {
    $result = Flight::reviewService()->get_review_count($recipe_id);
    Flight::json($result, 200);
});
