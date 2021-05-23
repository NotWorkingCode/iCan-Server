<?php


class LoginUser extends BaseMethod
{
    public function __construct()
    {
        $this->checkParams($_GET["login"], $_GET["pass"]);

        $response = $this->getDatabase()->get("SELECT * FROM `accounts` WHERE `a_login` = '{$_GET["login"]}'");

        if(!$response) $this->getResponseBuilder()
            ->AddErrorUserMessage("Пользователь с данным логином еще не зарегестрирован!")
            ->AddErrorDebugMessage("User not found.")
            ->BuildErrorResponse();

        if($response["a_pass"] != md5($_GET["pass"])) $this->getResponseBuilder()
            ->AddErrorUserMessage("Вы ввели не верный пароль!")
            ->AddErrorDebugMessage("Access denied | Invalid password.")
            ->BuildErrorResponse();

        $token = md5(microtime() . $_GET["login"] . time() . date( "d.m.y") . $_GET["pass"]);

        $body = array(
            "ID" => $response["a_id"],
            "name" => $response["a_name"],
            "token" => $token,
            "role" => $response["a_role"],
            "department" => $response["a_department"]
        );

        $this->getDatabase()->query("UPDATE `accounts` SET `a_token`='{$token}' WHERE `a_login`='{$_GET["login"]}'");

        $this->getResponseBuilder()->AddSuccessResponseBody($body)->BuildSuccessResponse();
    }
}