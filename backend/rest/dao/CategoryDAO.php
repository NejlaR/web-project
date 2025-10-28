<?php

require_once 'BaseDAO.php';

/**
 * CategoryDAO - Data Access Object for categories table
 * Handles CRUD operations for recipe categories
 */
class CategoryDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct('categories');
    }
    
    /**
     * Create a new category
     * @param array $data
     * @return int|false - Returns category ID on success, false on failure
     */
    public function create($data) {
        return $this->add($data);
    }
    
    /**
     * Update an existing category
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateCategory($id, $data) {
        return $this->update($data, $id);
    }
    
    /**
     * Get category by name
     * @param string $name
     * @return array|null
     */
    public function getByName($name) {
        return $this->query_unique("SELECT * FROM {$this->table_name} WHERE name = :name", ['name' => $name]);
    }
    
    /**
     * Get categories with recipe count
     * @return array
     */
    public function getAllWithRecipeCount() {
        return $this->query("SELECT c.*, COUNT(r.id) as recipe_count 
                  FROM {$this->table_name} c 
                  LEFT JOIN recipes r ON c.id = r.category_id 
                  GROUP BY c.id 
                  ORDER BY c.name", []);
    }
    
    /**
     * Check if category exists by name
     * @param string $name
     * @param int|null $excludeId
     * @return bool
     */
    public function nameExists($name, $excludeId = null) {
        $query = "SELECT id FROM {$this->table_name} WHERE name = :name";
        $params = ['name' => $name];
        
        if ($excludeId) {
            $query .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        
        $result = $this->query($query, $params);
        return !empty($result);
    }
    
    /**
     * Get categories ordered by name
     * @return array
     */
    public function getAllOrdered() {
        return $this->query("SELECT * FROM {$this->table_name} ORDER BY name", []);
    }
    
    /**
     * Search categories by name or description
     * @param string $searchTerm
     * @return array
     */
    public function search($searchTerm) {
        $searchParam = '%' . $searchTerm . '%';
        return $this->query("SELECT * FROM {$this->table_name} 
                  WHERE name LIKE :search OR description LIKE :search 
                  ORDER BY name", ['search' => $searchParam]);
    }
    
    /**
     * Check if category can be deleted (no associated recipes)
     * @param int $id
     * @return bool
     */
    public function canDelete($id) {
        $result = $this->query_unique("SELECT COUNT(*) as count FROM recipes WHERE category_id = :id", ['id' => $id]);
        return $result['count'] == 0;
    }
}