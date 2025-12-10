<?php

require_once 'BaseDAO.php';

class RecipeDAO extends BaseDAO {

    public function __construct() {
        parent::__construct('recipes', 'recipe_id');
    }

    public function create($data) {
        return $this->add($data);
    }

    public function updateRecipe($id, $data) {
        return $this->update($data, $id);
    }

    /* ========================
       GET RECIPE WITH DETAILS
       ======================== */
    public function getByIdWithDetails($id) {

        $query = "
            SELECT 
                r.recipe_id,
                r.category_id,
                r.title,
                r.description,
                r.created_at,
                c.name AS category_name,
                i.ingredient_id,
                i.name AS ingredient_name,
                i.quantity
            FROM recipes r
            LEFT JOIN categories c ON r.category_id = c.category_id
            LEFT JOIN ingredients i ON r.recipe_id = i.recipe_id
            WHERE r.recipe_id = :id
        ";

        $rows = $this->query($query, ['id' => $id]);

        if (empty($rows)) {
            return null;
        }

        $recipe = [
            "recipe_id"     => $rows[0]["recipe_id"],
            "category_id"   => $rows[0]["category_id"],
            "category_name" => $rows[0]["category_name"],
            "title"         => $rows[0]["title"],
            "description"   => $rows[0]["description"],
            "created_at"    => $rows[0]["created_at"],
            "ingredients"   => []
        ];

        foreach ($rows as $row) {
            if (!empty($row["ingredient_id"])) {
                $recipe["ingredients"][] = [
                    "ingredient_id" => $row["ingredient_id"],
                    "name"          => $row["ingredient_name"],
                    "quantity"      => $row["quantity"]
                ];
            }
        }

        return $recipe;
    }

    /* ============================
       GET ALL RECIPES WITH DETAILS
       ============================ */
    public function getAllWithDetails() {

        $query = "
            SELECT 
                r.recipe_id,
                r.category_id,
                r.title,
                r.description,
                r.created_at,
                c.name AS category_name,
                i.ingredient_id,
                i.name AS ingredient_name,
                i.quantity
            FROM recipes r
            LEFT JOIN categories c ON r.category_id = c.category_id
            LEFT JOIN ingredients i ON r.recipe_id = i.recipe_id
            ORDER BY r.recipe_id
        ";

        $rows = $this->query($query);

        $result = [];

        foreach ($rows as $row) {

            $id = $row['recipe_id'];

            if (!isset($result[$id])) {
                $result[$id] = [
                    "recipe_id"     => $row["recipe_id"],
                    "category_id"   => $row["category_id"],
                    "category_name" => $row["category_name"],
                    "title"         => $row["title"],
                    "description"   => $row["description"],
                    "created_at"    => $row["created_at"],
                    "ingredients"   => []
                ];
            }

            if (!empty($row["ingredient_id"])) {
                $result[$id]["ingredients"][] = [
                    "ingredient_id" => $row["ingredient_id"],
                    "name"          => $row["ingredient_name"],
                    "quantity"      => $row["quantity"]
                ];
            }
        }

        return array_values($result);
    }

    /* ===========================
       GET BY USER — YOU HAVE NO USER ID
       RETURN EMPTY ARRAY
       =========================== */
    public function getByUser($userId) {
        return []; // jer tabela nema user_id
    }

    /* ======================
          SEARCH RECIPES
       ====================== */
    public function search($searchTerm) {
        $searchParam = '%' . $searchTerm . '%';

        return $this->query("
            SELECT 
                r.*, 
                c.name AS category_name
            FROM recipes r
            LEFT JOIN categories c ON r.category_id = c.category_id
            WHERE r.title LIKE :search 
               OR r.description LIKE :search
            ORDER BY r.title
        ", ['search' => $searchParam]);
    }
}

