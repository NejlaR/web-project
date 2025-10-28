<?php

/**
 * Simple autoloader for DAO classes
 * Automatically includes DAO class files when they are instantiated
 */
class DAOAutoloader {
    
    private static $instance = null;
    private $basePath;
    
    private function __construct() {
        $this->basePath = __DIR__ . '/dao/';
    }
    
    /**
     * Get singleton instance
     * @return DAOAutoloader
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Register the autoloader
     */
    public function register() {
        spl_autoload_register([$this, 'autoload']);
    }
    
    /**
     * Autoload function
     * @param string $className
     */
    public function autoload($className) {
        // Check if the class is a DAO class
        if (strpos($className, 'DAO') !== false) {
            $fileName = $this->basePath . $className . '.php';
            
            if (file_exists($fileName)) {
                require_once $fileName;
            }
        }
    }
    
    /**
     * Load all DAO classes manually (alternative to autoloading)
     */
    public static function loadAllDAOs() {
        $daoPath = __DIR__ . '/dao/';
        $daoFiles = [
            'BaseDAO.php',
            'RoleDAO.php',
            'UserDAO.php',
            'CategoryDAO.php',
            'IngredientDAO.php',
            'RecipeDAO.php',
            'RecipeIngredientDAO.php',
            'ReviewDAO.php'
        ];
        
        foreach ($daoFiles as $file) {
            $filePath = $daoPath . $file;
            if (file_exists($filePath)) {
                require_once $filePath;
            }
        }
    }
}

// Register the autoloader
DAOAutoloader::getInstance()->register();