<?php
// USER CRUD ROUTES

Flight::route('GET /users', function() {
    $result = Flight::userService()->get_all();
    Flight::json($result, $result['success'] ? 200 : 400);
});

Flight::route('GET /users/@id', function($id) {
    $result = Flight::userService()->get_by_id($id);
    Flight::json($result, $result['success'] ? 200 : 404);
});

Flight::route('POST /users', function() {
    $data = Flight::request()->data->getData();
    $result = Flight::userService()->add($data);
    Flight::json($result, $result['success'] ? 201 : 400);
});

Flight::route('PUT /users/@id', function($id) {
    $data = Flight::request()->data->getData();
    $result = Flight::userService()->update($data, $id);
    Flight::json($result, $result['success'] ? 200 : 400);
});

Flight::route('DELETE /users/@id', function($id) {
    $result = Flight::userService()->delete($id);
    Flight::json($result, $result['success'] ? 200 : 400);
});
