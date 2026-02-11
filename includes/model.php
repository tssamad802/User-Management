<?php
class model
{
    public $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * @param string $table
     * @param array $conditions
     * @param bool $includeSoftDeleted
     * @return array
     */
    public function check_record($table, $conditions = [], $includeSoftDeleted = false)
    {
        $password = null;
        if (isset($conditions['pwd'])) {
            $password = $conditions['pwd'];
            unset($conditions['pwd']);
        }

        $where = [];
        foreach ($conditions as $column => $value) {
            $where[] = "$column = :$column";
        }
        if (!$includeSoftDeleted) {
            $where[] = "is_deleted IS NULL";
        }

        $where_clause = $where ? implode(' AND ', $where) : '1';
        $sql = "SELECT * FROM $table WHERE $where_clause";
        $stmt = $this->conn->prepare($sql);

        foreach ($conditions as $column => $value) {
            $stmt->bindValue(":$column", $value);
        }

        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($password !== null) {
            foreach ($results as $key => $row) {
                if (!password_verify($password, $row['pwd'])) {
                    unset($results[$key]);
                }
            }
        }
        return $results;
    }

    /**
     * @param string $table
     * @param array $data
     * @return bool
     */
    public function insert_record($table, $data = [])
    {
        if (isset($data['pwd'])) {
            $data['pwd'] = password_hash($data['pwd'], PASSWORD_DEFAULT);
        }
        if (!isset($data['is_deleted'])) {
            $data['is_deleted'] = null;
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        foreach ($data as $column => $value) {
            if ($value === null) {
                $stmt->bindValue(":$column", $value, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(":$column", $value);
            }
        }
        $result = $stmt->execute();
        return json_encode([
            'success' => $result,
            'data' => $data
        ]);
    }

    /**
     * @param string $table 
     * @param array $conditions 
     * @param bool $includeSoftDeleted 
     * @return array 
     */
    public function fetch_records($table, $conditions = [], $includeSoftDeleted = false)
    {
        $where = [];
        foreach ($conditions as $column => $value) {
            $where[] = "$column = :$column";
        }
        if (!$includeSoftDeleted) {
            $where[] = "is_deleted IS NULL";
        }

        $where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        if (!$where && !$includeSoftDeleted) {
            $where_clause = 'WHERE is_deleted IS NULL';
        }

        $sql = "SELECT * FROM $table $where_clause";
        $stmt = $this->conn->prepare($sql);

        foreach ($conditions as $column => $value) {
            $stmt->bindValue(":$column", $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $table
     * @param array $data 
     * @param int $id
     * @return bool
     */
    public function update($table, $id, $data = [])
    {
        if (isset($data['pwd']) && !empty($data['pwd'])) {
            $data['pwd'] = password_hash($data['pwd'], PASSWORD_DEFAULT);
        } elseif (isset($data['pwd']) && empty($data['pwd'])) {
            unset($data['pwd']);
        }

        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "`$key` = :$key";
        }
        $fields_sql = implode(', ', $fields);

        $sql = "UPDATE `$table` SET $fields_sql WHERE id = :id AND is_deleted IS NULL";
        $stmt = $this->conn->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':id', $id);

        // return $stmt->execute();
        $result = $stmt->execute();
        return json_encode([
            'success' => $result,
            'data' => $data
        ]);
    }

    /**
     * @param string $table
     * @param int $id
     * @return bool
     */
    public function soft_delete($table, $id)
    {
        $sql = "UPDATE `$table` SET is_deleted = NOW() WHERE id = :id AND is_deleted IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * @param string $table
     * @param int $id
     * @return bool
     */
    public function restore($table, $id)
    {
        $sql = "UPDATE `$table` SET is_deleted = NULL WHERE id = :id AND is_deleted IS NOT NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * @param string $table
     * @param int $id
     * @return bool
     */
    public function hard_delete($table, $id)
    {
        $sql = "DELETE FROM `$table` WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * @param string $table 
     * @param array $conditions
     * @return array
     */
    public function fetch_deleted_records($table, $conditions = [])
    {
        $where = [];
        foreach ($conditions as $column => $value) {
            $where[] = "$column = :$column";
        }
        $where[] = "is_deleted IS NOT NULL";

        $where_clause = 'WHERE ' . implode(' AND ', $where);

        $sql = "SELECT * FROM $table $where_clause";
        $stmt = $this->conn->prepare($sql);

        foreach ($conditions as $column => $value) {
            $stmt->bindValue(":$column", $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>