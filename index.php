<?php

require_once "utils/Const.php";
require_once "utils/ResponseBuilder.php";
require_once "utils/Database.php";

use utils\ResponseBuilder;

$method = $_GET['method'] ?: "ErrorNoMethod";

header('Content-Type: application/json');

if(file_exists("methods\\" . $method . ".php"))
{
    require_once("methods\\" . $method . ".php");
    $api_module = new $method();
} else
{
    $response_builder = new ResponseBuilder();
    $response_builder->AddErrorDebugMessage("Не удалось найти метод [{$method}]")->BuildErrorResponse();
}
