<?php

require_once __DIR__ . '/../dao/IngredientDAO.php';
require_once 'BaseService.php';

class IngredientService extends BaseService {

    public function __construct() {
        parent::__construct(new IngredientDAO());
    }

}
