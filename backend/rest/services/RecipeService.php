<?php

require_once __DIR__ . '/../dao/RecipeDAO.php';
require_once 'BaseService.php';

class RecipeService extends BaseService {

    public function __construct() {
        parent::__construct(new RecipeDAO());
    }


    public function get_by_id_with_details($id) {
        try {
            $data = $this->dao->getByIdWithDetails($id);

            if ($data === null) {
                return [
                    "success" => false,
                    "data" => null,
                    "message" => "Recipe not found"
                ];
            }

            return [
                "success" => true,
                "data" => $data,
                "message" => "Recipe with details retrieved"
            ];

        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }


    public function get_all_with_details() {
        try {
            return [
                "success" => true,
                "data" => $this->dao->getAllWithDetails(),
                "message" => "All recipes with details retrieved"
            ];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }


    public function get_by_user($userId) {
        try {
            return [
                "success" => true,
                "data" => $this->dao->getByUser($userId),
                "message" => "User recipes retrieved"
            ];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }


    public function search($term) {
        try {
            return [
                "success" => true,
                "data" => $this->dao->search($term),
                "message" => "Search results retrieved"
            ];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}
