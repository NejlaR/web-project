<?php

require_once __DIR__ . '/../dao/IngredientDAO.php';
require_once 'BaseService.php';

class IngredientService extends BaseService {

    public function __construct() {
        parent::__construct(new IngredientDAO());
    }

    // =====================================================
    // ADDITIONAL METHODS CONNECTED TO DAO
    // =====================================================

    public function get_all_with_usage_count() {
    try {
        $data = $this->dao->getAllWithUsageCount();

        return [
            "success" => true,
            "data" => $data,
            "message" => "Ingredients with usage count retrieved"
        ];

    } catch (Exception $e) {
        return [
            "success" => false,
            "data" => null,
            "message" => $e->getMessage()
        ];
    }
}


   public function search($term) {
    try {
        $data = $this->dao->search($term);

        return [
            "success" => true,
            "data" => $data,
            "message" => "Search results retrieved"
        ];

    } catch (Exception $e) {
        return [
            "success" => false,
            "data" => null,
            "message" => $e->getMessage()
        ];
    }
}


    public function get_by_name($name) {
        try {
            return [
                "success" => true,
                "data" => $this->dao->getByName($name),
                "message" => "Ingredient retrieved by name"
            ];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}
