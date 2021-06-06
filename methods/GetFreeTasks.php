<?php

class GetFreeTasks extends BaseMethod
{
    public function __construct()
    {
        $this->checkParams($_GET["token"]);

        $response = $this->getDatabase()->get("SELECT `a_department` FROM `accounts` WHERE a_token = '{$_GET["token"]}'");

        if(!$response) $this->getResponseBuilder()
            ->AddErrorUserMessage("Ваша сессия устарела!")
            ->AddErrorDebugMessage("Access denied | Invalid token.")
            ->BuildErrorResponse();

        $tasks = $this->getDatabase()->customAll("SELECT * FROM `tasks` WHERE t_executor IS NULL AND t_department = '{$response["a_department"]}'");

        if(!$tasks) $this->getResponseBuilder()
            ->AddErrorUserMessage("На данный момент нет доступных задач.")
            ->AddErrorDebugMessage("E | Tasks is null")
            ->BuildErrorResponse();
            
        $today = strtotime(date("Y-m-d G.i.s"));

        $count = 0;

        while ($task = $tasks->fetch_assoc()) {
            $end = strtotime($task["t_deadline"]);
            $seconds = abs($today - $end);
            $days = floor($seconds / 86400);
            $data[$count]["task_id"] = $task["t_id"];
            $data[$count]["task_title"] = $task["t_title"];
            $data[$count]["task_body"] = $task["t_body"];
            $data[$count]["task_create"] = $task["t_create"];
            $data[$count]["task_deadline"] = $task["t_deadline"];
            $data[$count]["task_day_before_deadline"] = $days;
            $data[$count]["task_executor_num"] = $task["t_executor"];
            $data[$count]["task_executor"] = $this->getDatabase()->get("SELECT `a_name` FROM `accounts` WHERE a_id = '{$task["t_executor"]}'")['a_name'];
            $data[$count]["task_creator"] = $task["t_creator"];
            $data[$count]["task_department"] = $task["t_department"];
            $data[$count]["task_status"] = $task["t_status"];
            $count++;
        }
        
        $value = array(
            "tasks" => $data    
        );

        $this->getResponseBuilder()
            ->AddSuccessResponseBody($value)
            ->BuildSuccessResponse();
    }
}