<?php

require_once __DIR__ . '/../dao/RecipeDAO.php';
require_once 'BaseService.php';

class RecipeService extends BaseService {

    public function __construct() {
        parent::__construct(new RecipeDAO());
    }
}
