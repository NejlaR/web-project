<?php
require_once __DIR__ . '/BaseDao.php';

class AuthDao extends BaseDao {

    public function __construct() {
        // Radimo sa tabelom "users"
        parent::__construct("users");
    }

    public function get_user_by_email($email) {
        return $this->query_unique(
            "SELECT * FROM users WHERE email = :email",
            ["email" => $email]
        );
    }
}
