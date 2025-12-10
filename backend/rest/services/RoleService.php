<?php

require_once __DIR__ . '/../dao/RoleDAO.php';
require_once 'BaseService.php';

class RoleService extends BaseService {

    public function __construct() {
        parent::__construct(new RoleDAO());
    }

    // Get role by name
    public function get_by_name($name) {
        try {
            return [
                "success" => true,
                "data" => $this->dao->getByName($name),
                "message" => "Role retrieved by name"
            ];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    // Check if a role exists
    public function exists($name) {
        try {
            return [
                "success" => true,
                "data" => $this->dao->exists($name),
                "message" => "Role existence checked"
            ];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    // Get all roles ordered alphabetically
    public function get_all_ordered() {
        try {
            return [
                "success" => true,
                "data" => $this->dao->getAllOrdered(),
                "message" => "Roles ordered by name"
            ];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}
