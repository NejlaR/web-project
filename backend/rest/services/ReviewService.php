<?php

require_once __DIR__ . '/../dao/ReviewDAO.php';
require_once 'BaseService.php';

class ReviewService extends BaseService {

    public function __construct() {
        parent::__construct(new ReviewDAO());
    }

    /* =============================
       GET REVIEW BY ID  ✔ FIXED
    ============================== */
    public function get_by_id($id) {
        try {
            $data = $this->dao->getById($id);

            if (!$data) {
                return [
                    "success" => false,
                    "data" => null,
                    "message" => "Review not found"
                ];
            }

            return [
                "success" => true,
                "data" => $data,
                "message" => "Review found"
            ];

        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    /* =======================================
       GET ALL REVIEWS FOR SPECIFIC RECIPE
    ======================================== */
    public function get_by_recipe($recipeId) {
        try {
            return [
                "success" => true,
                "data" => $this->dao->getByRecipe($recipeId)
            ];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    /* =======================================
       GET ALL REVIEWS BY USER
    ======================================== */
    public function get_by_user($userId) {
        try {
            return [
                "success" => true,
                "data" => $this->dao->getByUser($userId)
            ];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    /* =======================================
       GET AVERAGE RATING
    ======================================== */
    public function get_average_rating($recipeId) {
        try {
            return [
                "success" => true,
                "data" => $this->dao->getAverageRating($recipeId)
            ];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    /* =======================================
       GET TOTAL REVIEW COUNT
    ======================================== */
    public function get_review_count($recipeId) {
        try {
            return [
                "success" => true,
                "data" => $this->dao->getReviewCount($recipeId)
            ];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}

