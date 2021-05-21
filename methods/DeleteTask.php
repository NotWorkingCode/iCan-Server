<?php


class DeleteTask extends BaseMethod
{
    public function __construct()
    {
        $this->checkParams($_GET["token"], $_GET["taskid"]);

        $response = $this->getDatabase()->get("SELECT `a_role`, `a_department` FROM `accounts` WHERE a_token = '{$_GET["token"]}'");
        if(!$response) $this->getResponseBuilder()
            ->AddErrorUserMessage("Ваша сессия устарела!")
            ->AddErrorDebugMessage("Access denied | Invalid token.")
            ->BuildErrorResponse();

        $task = $this->getDatabase()->get("SELECT * FROM `tasks` WHERE t_id = '{$_GET["taskid"]}'");

        if(!$task) $this->getResponseBuilder()
            ->AddErrorUserMessage("Задача не найдена.")
            ->AddErrorDebugMessage("Task not found")
            ->BuildErrorResponse();

        switch ($response["a_role"]) {
            case 0:
            {
                $this->getResponseBuilder()
                    ->AddErrorUserMessage("У вас недостаточно прав для этой операции")
                    ->AddErrorDebugMessage("Access denied | a_role = {$response["a_role"]}")
                    ->BuildErrorResponse();
            }
            case 1:
            {
                if($task["t_department"] != $response["a_department"])
                    $this->getResponseBuilder()
                        ->AddErrorUserMessage("Вы можете удалить только задачу своего отдела.")
                        ->AddErrorDebugMessage("Access denied")
                        ->BuildErrorResponse();

                if ($this->getDatabase()->query("DELETE FROM `tasks` WHERE t_id = '{$_GET["taskid"]}'"))
                    $this->getResponseBuilder()
                        ->BuildSuccessResponse();
                else $this->getResponseBuilder()
                    ->AddErrorUserMessage("Произошла ошибка при обращении к БД.")
                    ->AddErrorDebugMessage("DBE | Error insert")
                    ->BuildErrorResponse();
            }
            case 2:
            {
                if ($this->getDatabase()->query("DELETE FROM `tasks` WHERE t_id = '{$_GET["taskid"]}'"))
                    $this->getResponseBuilder()
                        ->BuildSuccessResponse();
                else $this->getResponseBuilder()
                    ->AddErrorUserMessage("Произошла ошибка при обращении к БД.")
                    ->AddErrorDebugMessage("DBE | Error insert")
                    ->BuildErrorResponse();
            }
        }
    }
}