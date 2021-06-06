<?php


class GetTask extends BaseMethod
{
    public function __construct()
    {
        $this->checkParams($_GET["token"], $_GET["taskid"]);

        $response = $this->getDatabase()->get("SELECT `a_department`, `a_role` FROM `accounts` WHERE a_token = '{$_GET["token"]}'");
        if(!$response) $this->getResponseBuilder()
            ->AddErrorUserMessage("Ваша сессия устарела!")
            ->AddErrorDebugMessage("Access denied | Invalid token.")
            ->BuildErrorResponse();

        $task = $this->getDatabase()->get("SELECT * FROM `tasks` WHERE t_id = '{$_GET["taskid"]}'");

        if($task["t_department"] != $response["a_department"] && $response["a_role"] < 1) $this->getResponseBuilder()
            ->AddErrorUserMessage("Данная задача принадлежит не вам.")
            ->AddErrorDebugMessage("TE | Access denied")
            ->BuildErrorResponse();
            
        $today = strtotime(date("Y-m-d G.i.s"));
        $end = strtotime($task["t_deadline"]);
        $seconds = abs($today - $end);
        $days = floor($seconds / 86400);

        $body = array(
            "task_title" => $task["t_title"],
            "task_body" => $task["t_body"],
            "task_creator" => $this->getDatabase()->get("SELECT `a_name` FROM `accounts` WHERE a_id = '{$task["t_creator"]}'")['a_name'],
            "task_executor" => $this->getDatabase()->get("SELECT `a_name` FROM `accounts` WHERE a_id = '{$task["t_executor"]}'")['a_name'],
            "task_day_before_deadline" => $days,
            "task_status" => $task["t_status"]
        );

        $this->getResponseBuilder()->AddSuccessResponseBody($body)->BuildSuccessResponse();
    }
}