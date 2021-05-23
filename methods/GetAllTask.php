<?php


class GetAllTask extends BaseMethod
{
    public function __construct()
    {
        $this->checkParams($_GET["token"]);

        $response = $this->getDatabase()->get("SELECT `a_department` FROM `accounts` WHERE a_token = '{$_GET["token"]}'");

        if(!$response) $this->getResponseBuilder()
            ->AddErrorUserMessage("Ваша сессия устарела!")
            ->AddErrorDebugMessage("Access denied | Invalid token.")
            ->BuildErrorResponse();

        $tasks = $this->getDatabase()->all("tasks", $response["a_department"], "t_department");

        echo "SELECT `a_department` FROM `accounts` WHERE a_token = '{$_GET["token"]}'";

        if(!$tasks) $this->getResponseBuilder()
            ->AddErrorUserMessage("На данный момент нет доступных задач.")
            ->AddErrorDebugMessage("E | Tasks is null")
            ->BuildErrorResponse();

        $count = 0;

        while ($task = $tasks->fetch_assoc()) {
            $data[$count]["task_id"] = $task["t_id"];
            $data[$count]["task_title"] = $task["t_title"];
            $data[$count]["task_body"] = $task["t_body"];
            $data[$count]["task_create"] = $task["t_create"];
            $data[$count]["task_deadline"] = $task["t_deadline"];
            $data[$count]["task_executor"] = $task["t_executor"];
            $data[$count]["task_creator"] = $task["t_creator"];
            $data[$count]["task_department"] = $task["t_department"];
            $data[$count]["task_status"] = $task["t_status"];
            $count++;
        }

        $this->getResponseBuilder()
            ->AddSuccessResponseBody($data)
            ->BuildSuccessResponse();
    }
}