<?php

require_once __DIR__ . "/../Config.php";

/**
 * DatabaseService - Service for database connection testing and utilities
 * Provides methods to test database connectivity and basic operations
 */
class DatabaseService {
    
    private $connection;
    
    public function __construct() {
        $this->connection = null;
    }
    
    /**
     * Test database connection
     * @return array - Connection test results
     */
    public function testConnection() {
        $result = [
            'success' => false,
            'message' => '',
            'details' => [],
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        try {
            // Attempt to create database connection
            $this->connection = new PDO(
                "mysql:host=" . Config::DB_HOST() . ";dbname=" . Config::DB_NAME() . 
                ";charset=utf8;port=" . Config::DB_PORT(), 
                Config::DB_USER(), 
                Config::DB_PASSWORD(), 
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
            
            // Test basic query
            $stmt = $this->connection->prepare("SELECT 1 as test");
            $stmt->execute();
            $testResult = $stmt->fetch();
            
            if ($testResult['test'] == 1) {
                $result['success'] = true;
                $result['message'] = 'Database connection successful';
                $result['details'] = [
                    'host' => Config::DB_HOST(),
                    'database' => Config::DB_NAME(),
                    'port' => Config::DB_PORT(),
                    'user' => Config::DB_USER(),
                    'connection_status' => 'Connected',
                    'test_query' => 'Passed'
                ];
            }
            
        } catch (PDOException $e) {
            $result['success'] = false;
            $result['message'] = 'Database connection failed: ' . $e->getMessage();
            $result['details'] = [
                'host' => Config::DB_HOST(),
                'database' => Config::DB_NAME(),
                'port' => Config::DB_PORT(),
                'user' => Config::DB_USER(),
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage()
            ];
        }
        
        return $result;
    }
    
    /**
     * Test if database exists
     * @return array - Database existence test results
     */
    public function testDatabaseExists() {
        $result = [
            'success' => false,
            'message' => '',
            'database_exists' => false
        ];
        
        try {
            // Connect without specifying database
            $connection = new PDO(
                "mysql:host=" . Config::DB_HOST() . ";port=" . Config::DB_PORT(), 
                Config::DB_USER(), 
                Config::DB_PASSWORD()
            );
            
            $stmt = $connection->prepare("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = :dbname");
            $stmt->bindParam(':dbname', Config::DB_NAME());
            $stmt->execute();
            
            if ($stmt->fetch()) {
                $result['success'] = true;
                $result['database_exists'] = true;
                $result['message'] = 'Database "' . Config::DB_NAME() . '" exists';
            } else {
                $result['success'] = false;
                $result['database_exists'] = false;
                $result['message'] = 'Database "' . Config::DB_NAME() . '" does not exist';
            }
            
        } catch (PDOException $e) {
            $result['success'] = false;
            $result['message'] = 'Error checking database existence: ' . $e->getMessage();
        }
        
        return $result;
    }
    
    /**
     * Test if required tables exist
     * @return array - Tables existence test results
     */
    public function testTablesExist() {
        $requiredTables = ['roles', 'users', 'categories', 'ingredients', 'recipes', 'recipe_ingredients', 'reviews'];
        
        $result = [
            'success' => false,
            'message' => '',
            'tables_status' => [],
            'missing_tables' => [],
            'existing_tables' => []
        ];
        
        try {
            if (!$this->connection) {
                $connectionTest = $this->testConnection();
                if (!$connectionTest['success']) {
                    return [
                        'success' => false,
                        'message' => 'Cannot test tables: Database connection failed'
                    ];
                }
            }
            
            foreach ($requiredTables as $table) {
                try {
                    $stmt = $this->connection->prepare("SELECT 1 FROM `$table` LIMIT 1");
                    $stmt->execute();
                    $result['tables_status'][$table] = 'exists';
                    $result['existing_tables'][] = $table;
                } catch (PDOException $e) {
                    $result['tables_status'][$table] = 'missing';
                    $result['missing_tables'][] = $table;
                }
            }
            
            if (empty($result['missing_tables'])) {
                $result['success'] = true;
                $result['message'] = 'All required tables exist';
            } else {
                $result['success'] = false;
                $result['message'] = 'Missing tables: ' . implode(', ', $result['missing_tables']);
            }
            
        } catch (Exception $e) {
            $result['success'] = false;
            $result['message'] = 'Error checking tables: ' . $e->getMessage();
        }
        
        return $result;
    }
    
    /**
     * Get database server information
     * @return array - Server information
     */
    public function getServerInfo() {
        $result = [
            'success' => false,
            'server_info' => []
        ];
        
        try {
            if (!$this->connection) {
                $connectionTest = $this->testConnection();
                if (!$connectionTest['success']) {
                    return $result;
                }
            }
            
            $result['server_info'] = [
                'mysql_version' => $this->connection->getAttribute(PDO::ATTR_SERVER_VERSION),
                'connection_status' => $this->connection->getAttribute(PDO::ATTR_CONNECTION_STATUS),
                'server_info' => $this->connection->getAttribute(PDO::ATTR_SERVER_INFO),
                'client_version' => $this->connection->getAttribute(PDO::ATTR_CLIENT_VERSION)
            ];
            
            $result['success'] = true;
            
        } catch (Exception $e) {
            $result['message'] = 'Error getting server info: ' . $e->getMessage();
        }
        
        return $result;
    }
    
    /**
     * Run comprehensive database test
     * @return array - Complete test results
     */
    public function runFullTest() {
        $results = [
            'overall_status' => 'unknown',
            'timestamp' => date('Y-m-d H:i:s'),
            'tests' => []
        ];
        
        // Test 1: Connection
        $results['tests']['connection'] = $this->testConnection();
        
        // Test 2: Database existence
        $results['tests']['database_exists'] = $this->testDatabaseExists();
        
        // Test 3: Tables existence
        $results['tests']['tables_exist'] = $this->testTablesExist();
        
        // Test 4: Server info
        $results['tests']['server_info'] = $this->getServerInfo();
        
        // Determine overall status
        $allPassed = true;
        foreach (['connection', 'database_exists', 'tables_exist'] as $testName) {
            if (!$results['tests'][$testName]['success']) {
                $allPassed = false;
                break;
            }
        }
        
        $results['overall_status'] = $allPassed ? 'PASSED' : 'FAILED';
        
        return $results;
    }
    
    /**
     * Close database connection
     */
    public function closeConnection() {
        $this->connection = null;
    }
}