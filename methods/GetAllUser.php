<?php


class GetAllUser extends BaseMethod
{
    public function __construct()
    {
        $this->checkParams($_GET["token"]);

        $response = $this->getDatabase()->get("SELECT `a_role` FROM `accounts` WHERE a_token = '{$_GET["token"]}'");

        if(!$response) $this->getResponseBuilder()
            ->AddErrorUserMessage("Ваша сессия устарела!")
            ->AddErrorDebugMessage("Access denied | Invalid token.")
            ->BuildErrorResponse();

        if($response["a_role"] != 2) $this->getResponseBuilder()
            ->AddErrorUserMessage("У вас недостаточно прав для этой операции")
            ->AddErrorDebugMessage("Access denied | a_role = {$response["a_role"]}")
            ->BuildErrorResponse();

        $accounts = $this->getDatabase()->allOrder("accounts", "a_role");

        if (!$accounts) $this->getResponseBuilder()
            ->AddErrorUserMessage("Произошла ошибка при обращении к БД")
            ->AddErrorDebugMessage("E | Accounts is null")
            ->BuildErrorResponse();

        $data = array(array());

        $count = 0;

        while ($task = $accounts->fetch_assoc()) {
            $data[$count]["user_id"] = $task["a_id"];
            $data[$count]["user_login"] = $task["a_login"];
            $data[$count]["user_name"] = $task["a_name"];
            $data[$count]["user_role"] = $task["a_role"];
            $data[$count]["user_department"] = $task["a_department"];
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