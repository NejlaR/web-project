<?php

require_once __DIR__ . '/../dao/UserDAO.php';
require_once 'BaseService.php';

class UserService extends BaseService {

    public function __construct() {
        parent::__construct(new UserDAO());
    }
}
