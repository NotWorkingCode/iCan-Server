<?php


class GetUsersByDepartment extends BaseMethod
{
    public function __construct()
    {
        $this->checkParams($_GET["token"], $_GET["department"]);

        $response = $this->getDatabase()->get("SELECT `a_role` FROM `accounts` WHERE a_token = '{$_GET["token"]}'");

        if(!$response) $this->getResponseBuilder()
            ->AddErrorUserMessage("Ваша сессия устарела!")
            ->AddErrorDebugMessage("Access denied | Invalid token.")
            ->BuildErrorResponse();

        if($response["a_role"] < 1) $this->getResponseBuilder()
            ->AddErrorUserMessage("У вас недостаточно прав для этой операции")
            ->AddErrorDebugMessage("Access denied | a_role = {$response["a_role"]}")
            ->BuildErrorResponse();

        $accounts = $this->getDatabase()->all("accounts", $_GET["department"], "a_department");

        if (!$accounts) $this->getResponseBuilder()
            ->AddErrorUserMessage("В данном отделе нет сотрудников.")
            ->AddErrorDebugMessage("E | Accounts is null")
            ->BuildErrorResponse();

        $data = array(array());

        $count = 0;

        while ($task = $accounts->fetch_assoc()) {
            $data[$count]["user_id"] = $task["a_id"];
            $data[$count]["user_name"] = $task["a_name"];
            $count++;
        }
        
        $value = array(
            "users" => $data    
        );

        $this->getResponseBuilder()
            ->AddSuccessResponseBody($value)
            ->BuildSuccessResponse();
    }
}