<?php

require_once __DIR__ . '/../dao/RecipeIngredientDAO.php';
require_once 'BaseService.php';

class RecipeIngredientService extends BaseService {

    public function __construct() {
        parent::__construct(new RecipeIngredientDAO());
    }
}
