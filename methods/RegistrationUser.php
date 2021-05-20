<?php


class RegistrationUser extends BaseMethod
{
    public function __construct()
    {
        $this->checkParams($_GET["login"], $_GET["pass"], $_GET["code"]);

        $response = $this->getDatabase()->get("SELECT * FROM `waiting_registration` WHERE wr_login = '{$_GET["login"]}'");

        if(!$response) $this->getResponseBuilder()
        ->AddErrorUserMessage("Пользователь с данным логином не ожидает регистрации!")
        ->AddErrorDebugMessage("E | User not found")
        ->BuildErrorResponse();

        if($response["wr_code"] != $_GET["code"]) $this->getResponseBuilder()
            ->AddErrorUserMessage("Вы ввели не верный код регистрации!")
            ->AddErrorDebugMessage("E | Invalid code passed.")
            ->BuildErrorResponse();

        $pass = md5($_GET["pass"]);
        $token = md5(microtime() . $_GET["login"] . time() . date( "d.m.y") . $_GET["pass"]);

        $sql = "INSERT INTO `accounts`(`a_login`, `a_name`, `a_pass`, `a_token`, `a_role`, `a_department`) 
                VALUES ('{$response["wr_login"]}', '{$response["wr_name"]}', '{$pass}','{$token}' , {$response["wr_role"]}, NULLIF('{$response["wr_department"]}', ''))";

        if($this->getDatabase()->query($sql)) {
            $this->getDatabase()->query("DELETE FROM `waiting_registration` WHERE wr_login = '{$_GET["login"]}'");
            $this->getResponseBuilder()->BuildSuccessResponse();
        }
        else $this->getResponseBuilder()
            ->AddErrorUserMessage("Произошла ошибка при обращении к БД")
            ->AddErrorDebugMessage("DBE | Error insert {$sql}")
            ->BuildErrorResponse();
    }
}