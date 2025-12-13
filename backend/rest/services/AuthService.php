<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../dao/AuthDao.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class AuthService extends BaseService {

    private $auth_dao;

    public function __construct() {
        // KORISTIMO ISTI DAO – OVO JE BILO TVOJE GLAVNO USKO GRLO
        $this->auth_dao = new AuthDao();
        parent::__construct($this->auth_dao);
    }

    public function get_user_by_email($email) {
        return $this->auth_dao->get_user_by_email($email);
    }

    /**
     * REGISTER
     */
    public function register($entity) {

        if (empty($entity['email']) || empty($entity['password'])) {
            return ['success' => false, 'error' => 'Email and password are required.'];
        }

        if ($this->auth_dao->get_user_by_email($entity['email'])) {
            return ['success' => false, 'error' => 'Email already registered.'];
        }

        // Default role: user
        if (!isset($entity['role_id'])) {
            $entity['role_id'] = 2; // obični korisnik
        }

        // Hash password
        $entity['password'] = password_hash($entity['password'], PASSWORD_BCRYPT);

        // Ubacivanje korisnika u bazu
        parent::add($entity);

        // Ponovo dohvatiti korisnika iz baze
        $user = $this->auth_dao->get_user_by_email($entity['email']);
        unset($user['password']); // nikad ne šaljemo password

        return [
            "success" => true,
            "data" => $user
        ];
    }

    /**
     * LOGIN
     */
    public function login($entity) {

        if (empty($entity['email']) || empty($entity['password'])) {
            return ['success' => false, 'error' => 'Email and password are required.'];
        }

        $user = $this->auth_dao->get_user_by_email($entity['email']);

        if (!$user || !password_verify($entity['password'], $user['password'])) {
            return ['success' => false, 'error' => 'Invalid username or password.'];
        }

        // Ukloniti password
        unset($user['password']);

        // Dohvati ime role
        $role = Flight::roleService()->get_by_id($user['role_id']);
        $user['role'] = $role['data']['name'];
        unset($user['role_id']);

        // JWT generisanje
        $payload = [
            "user" => $user,
            "iat" => time(),
            "exp" => time() + 60 * 60 * 24 // token važi 24h
        ];

        $token = JWT::encode($payload, Config::JWT_SECRET(), 'HS256');
        $user["token"] = $token;

        return [
            "success" => true,
            "data" => $user
        ];
    }
}
?>
