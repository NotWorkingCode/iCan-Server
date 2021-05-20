<?php
#   Подключение основных компонентов    #
require_once "utils/Const.php";
require_once "utils/ResponseBuilder.php";
require_once "utils/Database.php";
require_once "methods/BaseMethod.php";
use utils\ResponseBuilder;
/*
 * Получение необходимого метода из параметров
 * В случае, если параметр не был передан, загрузится стандарный метод ErrorNoMethod
 */
$method = $_GET['method'] ?: "ErrorNoMethod";
header('Content-Type: application/json'); // Говорим бреузеру, что возвразаем JSON
if(file_exists("methods\\" . $method . ".php")) // Провверяем, есть ли необходимый метод
{
    require_once("methods\\" . $method . ".php"); // Если да, загружаем его
    $api_module = new $method(); // Создаем экземпляр класса, тем самым вызываю его конструктор
} else // Если запрашиваемый метод не найден, возвращаем ошибку с помощью ResponseBuilder
{
    $response_builder = new ResponseBuilder();
    $response_builder->AddErrorDebugMessage("Не удалось найти метод [{$method}]")->BuildErrorResponse();
}



