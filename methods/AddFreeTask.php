<?php


class AddFreeTask extends BaseMethod
{
    public function __construct()
    {
        $this->checkParams($_GET["token"], $_GET["title"], $_GET["body"], $_GET["deadline"]);

        $response = $this->getDatabase()->get("SELECT `a_id`, `a_role`, `a_department` FROM `accounts` WHERE a_token = '{$_GET["token"]}'");

        if(!$response) $this->getResponseBuilder()
            ->AddErrorUserMessage("Ваша сессия устарела!")
            ->AddErrorDebugMessage("Access denied | Invalid token.")
            ->BuildErrorResponse();

        if($response["a_role"] < 1) $this->getResponseBuilder()
            ->AddErrorUserMessage("У вас недостаточно прав для этой операции")
            ->AddErrorDebugMessage("Access denied | a_role = {$response["a_role"]}")
            ->BuildErrorResponse();

        $d_create = date("Y-m-d G.i.s");
        
        if($response["a_role"] == 2) {
            $this->checkParams($_GET["department"]);
            $sql = "INSERT INTO `tasks`(`t_title`, `t_body`, `t_create`, `t_deadline`, `t_executor`, `t_creator`, `t_department`, `t_status`) 
                VALUES ('{$_GET["title"]}', '{$_GET["body"]}', '{$d_create}', '{$_GET["deadline"]}', NULL, '{$response["a_id"]}', '{$_GET["department"]}', 0)";
        } else {
            $sql = "INSERT INTO `tasks`(`t_title`, `t_body`, `t_create`, `t_deadline`, `t_executor`, `t_creator`, `t_department`, `t_status`) 
                VALUES ('{$_GET["title"]}', '{$_GET["body"]}', '{$d_create}', '{$_GET["deadline"]}', NULL, '{$response["a_id"]}', '{$d_response["a_department"]}', 0)";
        }

        if($this->getDatabase()->query($sql)) $this->getResponseBuilder()->BuildSuccessResponse();
        else $this->getResponseBuilder()
        ->AddErrorUserMessage("Произошла ошибка при обращении к БД.")
        ->AddErrorDebugMessage("DBE | Error insert ({$sql})")
        ->BuildErrorResponse();
    }
}