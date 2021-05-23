<?php


class GetDepartments extends BaseMethod
{
    public function __construct()
    {
        $this->checkParams($_GET["token"]);

        $response = $this->getDatabase()->get("SELECT `a_role` FROM `accounts` WHERE a_token = '{$_GET["token"]}'");

        if(!$response) $this->getResponseBuilder()
            ->AddErrorUserMessage("Ваша сессия устарела!")
            ->AddErrorDebugMessage("Access denied | Invalid token.")
            ->BuildErrorResponse();

        if($response["a_role"] < 1) $this->getResponseBuilder()
            ->AddErrorUserMessage("У вас недостаточно прав для этой операции")
            ->AddErrorDebugMessage("Access denied | a_role = {$response["a_role"]}")
            ->BuildErrorResponse();

        $departments = $this->getDatabase()->all("departments");

        if(!$departments) $this->getResponseBuilder()
            ->AddErrorUserMessage("На данный момент вы не создали ни одного отдела.")
            ->AddErrorDebugMessage("E | Departments is null")
            ->BuildErrorResponse();

        $count = 0;

        while ($department = $departments->fetch_assoc()) {
            $data[$count]["department_id"] = $department["d_id"];
            $data[$count]["department_name"] = $department["d_name"];
            $count++;
        }

        $this->getResponseBuilder()
            ->AddSuccessResponseBody($data)
            ->BuildSuccessResponse();
    }
}