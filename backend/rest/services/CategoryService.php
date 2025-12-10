<?php

require_once __DIR__ . '/../dao/CategoryDAO.php';
require_once 'BaseService.php';

class CategoryService extends BaseService {

    public function __construct() {
        parent::__construct(new CategoryDAO());
    }

    public function get_all_with_recipe_count() {
        try {
            return [
                "success" => true,
                "data" => $this->dao->getAllWithRecipeCount(),
                "message" => "Categories with recipe count retrieved"
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    public function get_all_ordered() {
        try {
            return [
                "success" => true,
                "data" => $this->dao->getAllOrdered(),
                "message" => "Categories ordered retrieved"
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    public function search($term) {
        try {
            return [
                "success" => true,
                "data" => $this->dao->search($term),
                "message" => "Search results"
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    public function can_delete($id) {
        try {
            return [
                "success" => true,
                "data" => $this->dao->canDelete($id),
                "message" => "Delete check complete"
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    public function get_by_name($name) {
        try {
            return [
                "success" => true,
                "data" => $this->dao->getByName($name),
                "message" => "Category by name retrieved"
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }
}
