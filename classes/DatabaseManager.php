<?php

abstract class DatabaseManager {

    protected $connection;

    public function __construct(
        string $host = 'localhost',
        string $dbName = 'database',
        string $dbUser = 'localuser',
        string $dbPass = 'localpass',
    ) {
        $this->connection = new PDO("mysql:host=$host;dbname=$dbName", $dbUser, $dbPass);
    }

    protected abstract function create(array $columnsValues);

    protected abstract function read(array $columns = ['*'], array $conditions = []);

    protected abstract function update(array $columnsValues, array $conditions);

    protected abstract function delete(array $conditions);
}
