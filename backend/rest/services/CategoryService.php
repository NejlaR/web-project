<?php

require_once __DIR__ . '/../dao/CategoryDAO.php';
require_once 'BaseService.php';

class CategoryService extends BaseService {

    public function __construct() {
        parent::__construct(new CategoryDAO());
    }

    // -------------------------------------------
    // ADDITIONAL METHODS USED BY YOUR ROUTES
    // -------------------------------------------

    public function getCategoriesWithRecipeCount() {
        try {
            $result = $this->dao->getAllWithRecipeCount();
            return [
                "success" => true,
                "data" => $result,
                "message" => "Categories with recipe count retrieved"
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "data" => null,
                "message" => $e->getMessage()
            ];
        }
    }

    public function getPopularCategories($limit = 10) {
        try {
            $result = $this->dao->getPopular($limit);
            return [
                "success" => true,
                "data" => $result,
                "message" => "Popular categories retrieved"
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "data" => null,
                "message" => $e->getMessage()
            ];
        }
    }

    public function getCategoryStats($id) {
        try {
            $result = $this->dao->getStats($id);
            return [
                "success" => true,
                "data" => $result,
                "message" => "Category statistics retrieved"
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "data" => null,
                "message" => $e->getMessage()
            ];
        }
    }

    public function search($query) {
        try {
            $result = $this->dao->search($query);
            return [
                "success" => true,
                "data" => $result,
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
}
