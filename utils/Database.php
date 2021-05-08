<?php


namespace utils;


use mysqli;

class Database
{
    private $connect;

    private $DB_NAME = "ican";
    private $DB_HOST = "127.0.0.1";
    private $DB_USER = "mysql";
    private $DB_PASS = "mysql";

    private function __construct()
    {
        @$this->connect = new mysqli($this->DB_HOST, $this->DB_USER, $this->DB_PASS, $this->DB_NAME);
        @$this->connect->query("SET NAMES utf8");
        if(mysqli_connect_errno())
        {
            $response_builder = new ResponseBuilder();
            $response_builder->AddErrorUserMessage("Возникла ошибка при обращении к БД.")->AddErrorDebugMessage(mysqli_connect_error())->BuildErrorResponse();
        }
    }

    private function __clone(){}
    private function __wakeup(){}

    private static $instance;

    public static function getInstance()
    {
        if(is_null(self::$instance))
        {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function query($sql): bool
    {
        $this->connect->query($sql);
        return $this->connect->errno == 0;
    }

    public function get($sql)
    {
        $db_response = $this->connect->query($sql);
        if ($db_response->num_rows > 0)
        {
            return $db_response->fetch_array();
        }
        else {
            return false;
        }
    }

    public function exist($table, $field, $value): bool
    {
        $sql = "SELECT `{$field}` FROM `{$table}` WHERE {$field}='$value'";
        $result = $this->connect->query($sql);
        return $result->num_rows > 0;
    }

    public function all($table, $where = 0, $field = 0)
    {
        $sql = "SELECT * FROM `{$table}` ";
        if ($where != 0) $sql .= "WHERE {$field} = '{$where}'";
        if($result = $this->connect->query($sql))
        {
            if($result->num_rows > 0) return $result;
            else return false;
        } else return false;
    }
}