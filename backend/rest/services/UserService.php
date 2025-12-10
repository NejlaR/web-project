<?php

require_once __DIR__ . '/../dao/UserDAO.php';
require_once 'BaseService.php';

class UserService extends BaseService {

    public function __construct() {
        parent::__construct(new UserDAO());
    }

    public function get_all_with_roles() {
        try {
            return [
                "success" => true,
                "data" => $this->dao->getAllWithRoles()
            ];
        } catch(Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function get_by_email($email) {
        try {
            return [
                "success" => true,
                "data" => $this->dao->getByEmail($email)
            ];
        } catch(Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function get_by_id_with_role($id) {
        try {
            return [
                "success" => true,
                "data" => $this->dao->getByIdWithRole($id)
            ];
        } catch(Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function email_exists($email) {
        try {
            return [
                "success" => true,
                "data" => $this->dao->emailExists($email)
            ];
        } catch(Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function get_by_role($role_id) {
        try {
            return [
                "success" => true,
                "data" => $this->dao->getByRole($role_id)
            ];
        } catch(Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function search($term) {
        try {
            return [
                "success" => true,
                "data" => $this->dao->search($term)
            ];
        } catch(Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}
