<?php
class Database extends PDO
{
    private string $driver = 'mysql';
    private string $host = 'Localhost';
    private string $dbName = 'texfashion';
    private string $charset = 'utf8';
    private string $user = 'root';
    private string $password = '';

    public function __construct()
    {
        try {
            parent::__construct("{$this->driver}:host={$this->host}; dbname={$this->dbName}; charset={$this->charset}", $this->user, $this->password);
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Conexión Fallida {$e->getMessage()}";
        }
    }

    public function select(string $strSql, array $arrayData = [], int $fetchMode = PDO::FETCH_OBJ): array
    {
        $query = $this->prepare($strSql);

        foreach ($arrayData as $key => $value) {
            $query->bindParam(":$key", $value);
        }

        if (!$query->execute()) {
            echo "Error, la Consulta no se Realizó";
            return [];
        }

        return $query->fetchAll($fetchMode);
    }

    public function insert(string $table, array $data): void
    {
        try {
            ksort($data);
            unset($data['controller'], $data['method']);

            $fieldNames = implode('`, `', array_keys($data));
            $fieldValues = ':' . implode(', :', array_keys($data));
            $strSql = $this->prepare("INSERT INTO $table (`$fieldNames`) VALUES ($fieldValues)");

            foreach ($data as $key => $value) {
                $strSql->bindValue(":$key", $value);
            }

            $strSql->execute();
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function update(string $table, array $data, string $where): void
    {
        try {
            ksort($data);
            $fieldDetails = '';

            foreach ($data as $key => $value) {
                $fieldDetails .= "`$key` = :$key,";
            }

            $fieldDetails = rtrim($fieldDetails, ',');
            $strSql = $this->prepare("UPDATE $table SET $fieldDetails WHERE $where");

            foreach ($data as $key => $value) {
                $strSql->bindValue(":$key", $value);
            }

            $strSql->execute();
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function delete(string $table, string $where): int
    {
        return $this->exec("DELETE FROM $table WHERE $where");
    }
}
