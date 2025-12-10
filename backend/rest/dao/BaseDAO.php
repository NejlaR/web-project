<?php
require_once __DIR__ . '/../Database.php';

class BaseDAO {
    protected $connection;
    protected $table_name;
    protected $id_column;

    public function __construct($table_name, $id_column = null) {
        $this->connection = Database::connect();
        $this->table_name = $table_name;

        // Ako ID kolona nije eksplicitno zadana → detektuj iz naziva tabele
        if ($id_column) {
            $this->id_column = $id_column;
        } else {
            // automatski ID: recipe_id, category_id, user_id...
            $this->id_column = rtrim($table_name, 's') . '_id';
        }
    }

    // Get all rows
    public function getAll() {
        return $this->query("SELECT * FROM {$this->table_name}");
    }

    // Get by ID
    public function getById($id) {
        return $this->query_unique(
            "SELECT * FROM {$this->table_name} WHERE {$this->id_column} = :id",
            ['id' => $id]
        );
    }

    // Insert
    public function add($entity) {
        $columns = implode(", ", array_keys($entity));
        $placeholders = ":" . implode(", :", array_keys($entity));
        $query = "INSERT INTO {$this->table_name} ($columns) VALUES ($placeholders)";
        
        $stmt = $this->connection->prepare($query);
        $stmt->execute($entity);

        return $this->connection->lastInsertId();
    }

    // Update
    public function update($entity, $id) {
        $set_clause = "";

        foreach ($entity as $key => $value) {
            $set_clause .= "$key = :$key, ";
        }

        $set_clause = rtrim($set_clause, ", ");

        $query = "UPDATE {$this->table_name} 
                  SET $set_clause 
                  WHERE {$this->id_column} = :id";

        $entity['id'] = $id;
        $stmt = $this->connection->prepare($query);

        return $stmt->execute($entity);
    }

    // Delete
    public function delete($id) {
        $stmt = $this->connection->prepare(
            "DELETE FROM {$this->table_name} WHERE {$this->id_column} = :id"
        );

        return $stmt->execute(['id' => $id]);
    }

    // Helper for multiple rows
    protected function query($query, $params = []) {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Helper for single row
    protected function query_unique($query, $params = []) {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
