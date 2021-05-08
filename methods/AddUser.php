<?php


class AddUser extends BaseMethod
{
    public function __construct()
    {
        $this->checkParams($_GET["token"], $_GET["c_login"], $_GET["c_name"], $_GET["c_type"]);

        $response = $this->getDatabase()->get("SELECT a_type, a_department FROM `users` WHERE `a_token` = '{$_GET["token"]}'");

        if(!$response) $this->getResponseBuilder()
            ->AddErrorUserMessage("Ваша сессия устарела!")
            ->AddErrorDebugMessage("Access denied | Invalid token.")
            ->BuildErrorResponse();

        switch ($response["a_type"]) {
            case 0: {
                $this->getResponseBuilder()
                    ->AddErrorUserMessage("У вас недостаточно прав для этой операции")
                    ->AddErrorDebugMessage("Access denied | a_type = {$response["a_type"]}")
                    ->BuildErrorResponse();
                break;
            }
            case 1: {

                $this->CheckUserExist($_GET["c_login"]);

                $code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);

                if ($this->getDatabase()->query("INSERT INTO `reg_user`(`r_login`, `r_name`, `r_code`, `r_type`, `r_department`) VALUES ('{$_GET['c_login']}', '{$_GET['c_name']}', '{$code}', '{$_GET['c_type']}', '{$response["a_department"]}')"))
                    $this->getResponseBuilder()->AddSuccessResponseBody(array("code" => $code))->BuildSuccessResponse();

                else $this->getResponseBuilder()
                    ->AddErrorUserMessage("Произошла ошибка при обращении к БД.")
                    ->AddErrorDebugMessage("DB | Error SQL")
                    ->BuildErrorResponse();
            }
            case 2: {

                $this->CheckUserExist($_GET["c_login"]);

                $code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);

                if(isset($_GET["c_department"])) {
                    $sql = "INSERT INTO `reg_user`(`r_login`, `r_name`, `r_code`, `r_type`, `r_department`) VALUES ('{$_GET['c_login']}', '{$_GET['c_name']}', '{$code}', '{$_GET['c_type']}', '{$_GET["c_department"]}')";
                } else {
                    $sql = "INSERT INTO `reg_user`(`r_login`, `r_name`, `r_code`, `r_type`) VALUES ('{$_GET['c_login']}', '{$_GET['c_name']}', '{$code}', '{$_GET['c_type']}')";
                }

                if ($this->getDatabase()->query($sql))
                    $this->getResponseBuilder()->AddSuccessResponseBody(array("code" => $code))->BuildSuccessResponse();

                else $this->getResponseBuilder()
                    ->AddErrorUserMessage("Произошла ошибка при обращении к БД.")
                    ->AddErrorDebugMessage("DB | Error SQL")
                    ->BuildErrorResponse();

                break;
            }
        }
    }

    private function CheckUserExist($login) {
        if($this->getDatabase()->exist("users", "a_login", $login)) $this->getResponseBuilder()
            ->AddErrorUserMessage("Пользователь с таким логином уже зарегестрирован!")
            ->AddErrorDebugMessage("E | User exist")
            ->BuildErrorResponse();

        if($this->getDatabase()->exist("reg_user", "r_login", $login)) $this->getResponseBuilder()
            ->AddErrorUserMessage("Пользователь с таким логином уже ожидает регистрацию!")
            ->AddErrorDebugMessage("E | User exist")
            ->BuildErrorResponse();
    }
}