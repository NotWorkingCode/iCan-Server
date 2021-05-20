<?php


class AddAdministration extends BaseMethod
{
    public function __construct()
    {
        $this->checkParams($_GET["token"], $_GET["login"], $_GET["name"]);

        $response = $this->getDatabase()->get("SELECT `a_role` FROM `accounts` WHERE a_token = '{$_GET["token"]}'");

        if(!$response) $this->getResponseBuilder()
            ->AddErrorUserMessage("Ваша сессия устарела!")
            ->AddErrorDebugMessage("Access denied | Invalid token.")
            ->BuildErrorResponse();

        if($response["a_role"] != 2) $this->getResponseBuilder()
            ->AddErrorUserMessage("У вас недостаточно прав для этой операции")
            ->AddErrorDebugMessage("Access denied | a_role = {$response["a_role"]}")
            ->BuildErrorResponse();

        $this->CheckUserExist($_GET["login"]);

        $code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);

        $sql = "INSERT INTO `waiting_registration`(`wr_login`, `wr_name`, `wr_code`, `wr_role`, `wr_department`) 
                VALUES ('{$_GET["login"]}', '{$_GET["name"]}', '{$code}', 2, NULL)";

        $query = $this->getDatabase()->query($sql);

        if($query) $this->getResponseBuilder()->AddSuccessResponseBody(array("code"=>$code))->BuildSuccessResponse();
        else $this->getResponseBuilder()
            ->AddErrorUserMessage("Произошла ошибка при обращении к БД.")
            ->AddErrorDebugMessage("DBE | Error insert ({$sql})")
            ->BuildErrorResponse();
    }

    private function CheckUserExist($login) {
        if($this->getDatabase()->exist("accounts", "a_login", $login)) $this->getResponseBuilder()
            ->AddErrorUserMessage("Пользователь с таким логином уже зарегестрирован!")
            ->AddErrorDebugMessage("E | User exist")
            ->BuildErrorResponse();

        if($this->getDatabase()->exist("waiting_registration", "wr_login", $login)) $this->getResponseBuilder()
            ->AddErrorUserMessage("Пользователь с таким логином уже ожидает регистрацию!")
            ->AddErrorDebugMessage("E | User exist")
            ->BuildErrorResponse();
    }
}