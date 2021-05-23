<?php


class AddDepartment extends BaseMethod
{
    public function __construct()
    {
        $this->checkParams($_GET["token"], $_GET["name"]);

        $response = $this->getDatabase()->get("SELECT `a_role` FROM `accounts` WHERE a_token = '{$_GET["token"]}'");

        if(!$response) $this->getResponseBuilder()
            ->AddErrorUserMessage("Ваша сессия устарела!")
            ->AddErrorDebugMessage("Access denied | Invalid token.")
            ->BuildErrorResponse();

        if($response["a_role"] != 2) $this->getResponseBuilder()
            ->AddErrorUserMessage("У вас недостаточно прав для этой операции")
            ->AddErrorDebugMessage("Access denied | a_role = {$response["a_role"]}")
            ->BuildErrorResponse();

        if($this->getDatabase()->query("INSERT INTO `departments`(`d_name`) VALUES ('{$_GET["name"]}')"))
            $this->getResponseBuilder()->BuildSuccessResponse();
        else $this->getResponseBuilder()
            ->AddErrorUserMessage("Произошла ошибка при обращении к БД.")
            ->AddErrorDebugMessage("DBE | Error insert")
            ->BuildErrorResponse();
    }
}