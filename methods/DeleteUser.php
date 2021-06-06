<?php


class DeleteUser extends BaseMethod
{
    public function __construct()
    {
        $this->checkParams($_GET["token"], $_GET["userid"]);

        $response = $this->getDatabase()->get("SELECT `a_role`  FROM `accounts` WHERE a_token = '{$_GET["token"]}'");
        if(!$response) $this->getResponseBuilder()
            ->AddErrorUserMessage("Ваша сессия устарела!")
            ->AddErrorDebugMessage("Access denied | Invalid token.")
            ->BuildErrorResponse();
        if($response["a_role"] != 2) $this->getResponseBuilder()
            ->AddErrorUserMessage("У вас недостаточно прав для выполнения этой задачи!")
            ->AddErrorDebugMessage("AD")
            ->BuildErrorResponse();
        if ($this->getDatabase()->query("DELETE FROM `accounts` WHERE a_id = {$_GET["userid"]}"))
            $this->getResponseBuilder()
                ->BuildSuccessResponse();
        else $this->getResponseBuilder()
            ->AddErrorUserMessage("Произошла ошибка при обращении к БД.")
            ->AddErrorDebugMessage("DBE | Error insert")
            ->BuildErrorResponse();
    }
}