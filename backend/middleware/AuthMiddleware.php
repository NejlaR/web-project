<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {


    public function verifyToken($token){

        if(!$token){
            Flight::halt(401, json_encode(["error" => "Missing authentication header"]));
        }

        try {
            $decoded = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));

            // snimi usera i token u globalni Flight
            Flight::set('user', (array)$decoded->user);
            Flight::set('jwt_token', $token);

            return true;

        } catch (Exception $e){
            Flight::halt(401, json_encode(["error" => "Invalid or expired token"]));
        }
    }


    public function authorizeRole($required){
        $user = Flight::get('user');

        if (!$user) {
            Flight::halt(401, json_encode(["error" => "Unauthorized"]));
        }

        if ($user['role'] !== $required){
            Flight::halt(403, json_encode(["error" => "Access denied"]));
        }
    }

    public function authorizeRoles($roles){
        $user = Flight::get('user');

        if (!$user) {
            Flight::halt(401, json_encode(["error" => "Unauthorized"]));
        }

        if (!in_array($user['role'], $roles)){
            Flight::halt(403, json_encode(["error" => "Forbidden"]));
        }
    }


    public function requireAdmin() {
        $this->authorizeRole('admin');
    }
}
