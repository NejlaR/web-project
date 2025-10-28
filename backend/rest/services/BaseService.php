<?php

require_once __DIR__ . "/../dao/BaseDAO.php";

/**
 * BaseService - Base service class providing common business logic operations
 * All entity-specific service classes should extend this base class
 */
abstract class BaseService {
    
    protected $dao;
    
    public function __construct($dao) {
        $this->dao = $dao;
    }
    
    /**
     * Get all records
     * @return array
     */
    public function getAll() {
        try {
            return [
                'success' => true,
                'data' => $this->dao->get_all(),
                'message' => 'Records retrieved successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving records: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get record by ID
     * @param int $id
     * @return array
     */
    public function getById($id) {
        try {
            if (!$id || !is_numeric($id)) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Invalid ID provided'
                ];
            }
            
            $result = $this->dao->query_unique("SELECT * FROM {$this->dao->table_name} WHERE id = :id", ['id' => $id]);
            
            if ($result) {
                return [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Record found'
                ];
            } else {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Record not found'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving record: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Create new record
     * @param array $data
     * @return array
     */
    public function create($data) {
        try {
            // Validate required fields
            $validation = $this->validateCreate($data);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Validation failed: ' . implode(', ', $validation['errors'])
                ];
            }
            
            // Process data before creation
            $processedData = $this->processDataForCreate($data);
            
            $result = $this->dao->add($processedData);
            
            if ($result) {
                return [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Record created successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Failed to create record'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error creating record: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Update existing record
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update($id, $data) {
        try {
            if (!$id || !is_numeric($id)) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Invalid ID provided'
                ];
            }
            
            // Check if record exists
            $existing = $this->getById($id);
            if (!$existing['success']) {
                return $existing;
            }
            
            // Validate data
            $validation = $this->validateUpdate($data, $id);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Validation failed: ' . implode(', ', $validation['errors'])
                ];
            }
            
            // Process data before update
            $processedData = $this->processDataForUpdate($data, $id);
            
            $result = $this->dao->update($processedData, $id);
            
            if ($result) {
                // Get updated record
                $updatedRecord = $this->getById($id);
                return [
                    'success' => true,
                    'data' => $updatedRecord['data'],
                    'message' => 'Record updated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Failed to update record'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error updating record: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Delete record
     * @param int $id
     * @return array
     */
    public function delete($id) {
        try {
            if (!$id || !is_numeric($id)) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Invalid ID provided'
                ];
            }
            
            // Check if record exists
            $existing = $this->getById($id);
            if (!$existing['success']) {
                return $existing;
            }
            
            // Check if can delete
            $canDelete = $this->canDelete($id);
            if (!$canDelete['can_delete']) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => $canDelete['message']
                ];
            }
            
            $this->dao->delete($id);
            
            return [
                'success' => true,
                'data' => null,
                'message' => 'Record deleted successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error deleting record: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Search records
     * @param string $query
     * @return array
     */
    public function search($query) {
        try {
            if (empty($query)) {
                return $this->getAll();
            }
            
            $results = $this->performSearch($query);
            
            return [
                'success' => true,
                'data' => $results,
                'message' => 'Search completed successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Search error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get paginated records
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getPaginated($page = 1, $limit = 10) {
        try {
            $offset = ($page - 1) * $limit;
            
            $results = $this->dao->query(
                "SELECT * FROM {$this->dao->table_name} ORDER BY id LIMIT :limit OFFSET :offset",
                ['limit' => $limit, 'offset' => $offset]
            );
            
            $total = $this->dao->query_unique(
                "SELECT COUNT(*) as count FROM {$this->dao->table_name}",
                []
            );
            
            return [
                'success' => true,
                'data' => [
                    'records' => $results,
                    'pagination' => [
                        'current_page' => $page,
                        'per_page' => $limit,
                        'total' => (int) $total['count'],
                        'total_pages' => ceil($total['count'] / $limit)
                    ]
                ],
                'message' => 'Records retrieved successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving paginated records: ' . $e->getMessage()
            ];
        }
    }
    
    // Abstract methods that must be implemented by child classes
    abstract protected function validateCreate($data);
    abstract protected function validateUpdate($data, $id);
    abstract protected function processDataForCreate($data);
    abstract protected function processDataForUpdate($data, $id);
    abstract protected function performSearch($query);
    abstract protected function canDelete($id);
}