<?php


class AddTask extends BaseMethod
{
    public function __construct()
    {
        $this->checkParams($_GET["token"], $_GET["title"], $_GET["body"], $_GET["deadline"], $_GET["executor"]);

        $response = $this->getDatabase()->get("SELECT `a_id`, `a_role` FROM `accounts` WHERE a_token = '{$_GET["token"]}'");

        if(!$response) $this->getResponseBuilder()
            ->AddErrorUserMessage("Ваша сессия устарела!")
            ->AddErrorDebugMessage("Access denied | Invalid token.")
            ->BuildErrorResponse();

        if($response["a_role"] < 1) $this->getResponseBuilder()
            ->AddErrorUserMessage("У вас недостаточно прав для этой операции")
            ->AddErrorDebugMessage("Access denied | a_role = {$response["a_role"]}")
            ->BuildErrorResponse();

        $d_response = $this->getDatabase()->get("SELECT `a_department` FROM `accounts` WHERE a_id = '{$_GET["executor"]}'");

        if(!$d_response) $this->getResponseBuilder()
            ->AddErrorUserMessage("Данный пользователь не найден в БД.")
            ->AddErrorDebugMessage("E | User not found")
            ->BuildErrorResponse();


        $d_create = date("Y-m-d G.i.s");

        $sql = "INSERT INTO `tasks`(`t_title`, `t_body`, `t_create`, `t_deadline`, `t_executor`, `t_creator`, `t_department`, `t_status`) 
                VALUES ('{$_GET["title"]}', '{$_GET["body"]}', '{$d_create}', '{$_GET["deadline"]}', {$_GET["executor"]}, '{$response["a_id"]}', '{$d_response["a_department"]}', 0)";

        if($this->getDatabase()->query($sql)) $this->getResponseBuilder()->BuildSuccessResponse();
        else $this->getResponseBuilder()
        ->AddErrorUserMessage("Произошла ошибка при обращении к БД.")
        ->AddErrorDebugMessage("DBE | Error insert ({$sql})")
        ->BuildErrorResponse();
    }
}