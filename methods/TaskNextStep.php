<?php


class TaskNextStep extends BaseMethod
{
    public function __construct()
    {
        $this->checkParams($_GET["token"], $_GET["taskid"]);

        $response = $this->getDatabase()->get("SELECT `a_id`, `a_role` FROM `accounts` WHERE a_token = '{$_GET["token"]}'");
        if(!$response) $this->getResponseBuilder()
            ->AddErrorUserMessage("Ваша сессия устарела!")
            ->AddErrorDebugMessage("Access denied | Invalid token.")
            ->BuildErrorResponse();

        $task = $this->getDatabase()->get("SELECT * FROM `tasks` WHERE t_id = '{$_GET["taskid"]}'");
        
        if($task["t_status"] == 0) {
            if ($this->getDatabase()->query("UPDATE `tasks` SET `t_status`= 1, `t_executor`= '{$response["a_id"]}' WHERE t_id = '{$_GET["taskid"]}'"))
            $this->getResponseBuilder()
                ->AddSuccessResponseBody(array("task_status"=>1))
                ->BuildSuccessResponse();
            else $this->getResponseBuilder()
                ->AddErrorUserMessage("Произошла ошибка при обращении к БД.")
                ->AddErrorDebugMessage("DBE | Error insert")
                ->BuildErrorResponse();
        } else {
            if(($task["t_executor"] != $response["a_id"]) && ($response["a_role"] == 0)) $this->getResponseBuilder()
            ->AddErrorUserMessage("Данная задача принадлежит не вам.")
            ->AddErrorDebugMessage("TE | Access denied")
            ->BuildErrorResponse();

        $task_status = $task["t_status"] + 1;

        if ($this->getDatabase()->query("UPDATE `tasks` SET `t_status`= '{$task_status}' WHERE t_id = '{$_GET["taskid"]}'"))
            $this->getResponseBuilder()
                ->AddSuccessResponseBody(array("task_status"=>$task_status))
                ->BuildSuccessResponse();
        else $this->getResponseBuilder()
            ->AddErrorUserMessage("Произошла ошибка при обращении к БД.")
            ->AddErrorDebugMessage("DBE | Error insert")
            ->BuildErrorResponse();
        }
    }
}