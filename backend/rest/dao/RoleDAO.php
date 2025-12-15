<?php

require_once 'BaseDAO.php';

/**
 * RoleDAO - Data Access Object for roles table
 * Handles CRUD operations for user roles
 */
class RoleDAO extends BaseDAO {
    
    public function __construct() {
        // OVDE JE BITNA PROMJENA → ID kolona se zove 'id'
        parent::__construct('roles', 'id');
    }
    
    /**
     * Create a new role
     * @param array $data
     * @return int|false - Returns role ID on success, false on failure
     */
    public function create($data) {
        return $this->add($data);
    }
    
    /**
     * Update an existing role
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateRole($id, $data) {
        return $this->update($data, $id);
    }
    
    /**
     * Get role by name
     * @param string $name
     * @return array|null
     */
    public function getByName($name) {
        return $this->query_unique(
            "SELECT * FROM {$this->table_name} WHERE name = :name",
            ['name' => $name]
        );
    }
    
    /**
     * Check if role exists by name
     * @param string $name
     * @return bool
     */
    public function exists($name) {
        return $this->getByName($name) !== false;
    }
    
    /**
     * Get all roles ordered by name
     * @return array
     */
    public function getAllOrdered() {
        return $this->query(
            "SELECT * FROM {$this->table_name} ORDER BY name",
            []
        );
    }
}

?>
