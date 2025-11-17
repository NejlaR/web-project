<?php

require_once __DIR__ . '/../dao/RoleDAO.php';
require_once 'BaseService.php';

class RoleService extends BaseService {

    public function __construct() {
        parent::__construct(new RoleDAO());
    }
}
