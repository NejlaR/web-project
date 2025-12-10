<?php

require_once 'BaseDAO.php';

/**
 * Handles CRUD operations for the categories table.
 */
class CategoryDAO extends BaseDAO {

    public function __construct() {
        parent::__construct('categories');
    }

    /** Create a new category */
    public function create($data) {
        return $this->add($data);
    }

    /** Update category by ID */
    public function updateCategory($id, $data) {
        return $this->update($data, $id);
    }

    /** Get category by name */
    public function getByName($name) {
        return $this->query_unique(
            "SELECT * FROM {$this->table_name} WHERE name = :name",
            ['name' => $name]
        );
    }

    /** Get all categories with count of related recipes */
    public function getAllWithRecipeCount() {
        return $this->query(
            "SELECT c.*, COUNT(r.recipe_id) AS recipe_count
             FROM {$this->table_name} c
             LEFT JOIN recipes r ON c.category_id = r.category_id
             GROUP BY c.category_id
             ORDER BY c.name",
            []
        );
    }

    /** Check if category name already exists */
    public function nameExists($name, $excludeId = null) {

        $params = ['name' => $name];

        $query = "SELECT category_id FROM {$this->table_name} WHERE name = :name";

        if ($excludeId !== null) {
            $query .= " AND category_id != :exclude";
            $params['exclude'] = $excludeId;
        }

        return !empty($this->query($query, $params));
    }

    /** Return all categories sorted alphabetically */
    public function getAllOrdered() {
        return $this->query(
            "SELECT * FROM {$this->table_name} ORDER BY name",
            []
        );
    }

    /** Search categories by name or description */
    public function search($term) {
    return $this->query(
        "SELECT * FROM {$this->table_name}
         WHERE name LIKE :s
         ORDER BY name",
        ['s' => "%$term%"]
    );
}


    /** Check if category has no recipes → can be deleted */
    public function canDelete($id) {
        $result = $this->query_unique(
            "SELECT COUNT(*) AS total
             FROM recipes
             WHERE category_id = :id",
            ['id' => $id]
        );

        return $result['total'] == 0;
    }
}
