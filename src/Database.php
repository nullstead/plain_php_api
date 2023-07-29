<?php

class Database {

    public function __construct(private string $hostname,
                                private string $database,
                                private string $username,
                                private string $password)
    {}

    public function getConnection(): PDO {
        $dsn = "mysql:host={$this->hostname};dbname={$this->database};charset=utf8";
        return new PDO($dsn, $this->username, $this->password, [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false
        ]);
    }
}