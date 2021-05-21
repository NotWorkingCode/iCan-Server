<?php


class CheckUser extends BaseMethod
{
    public function __construct()
    {
        $this->checkParams($_GET["token"]);

        $response = $this->getDatabase()->get("SELECT * FROM `accounts` WHERE a_token = '{$_GET["token"]}'");

        if(!$response) $this->getResponseBuilder()
            ->AddErrorUserMessage("Ваша сессия устарела!")
            ->AddErrorDebugMessage("Access denied | Invalid token.")
            ->BuildErrorResponse();

        $body = array(
            "uID" => $response["a_id"],
            "name" => $response["a_name"],
            "role" => $response["a_role"],
            "department" => $response["a_department"]
        );

        $this->getResponseBuilder()->AddSuccessResponseBody($body)->BuildSuccessResponse();
    }
}