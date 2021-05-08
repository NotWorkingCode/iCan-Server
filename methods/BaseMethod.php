<?php

use utils\Database;
use utils\ResponseBuilder;

class BaseMethod
{
    protected function getResponseBuilder(): ResponseBuilder {
        return new ResponseBuilder();
    }
    protected function getDatabase(): Database {
        return Database::getInstance();
    }
    protected function checkParams(...$params) {
        foreach ($params as $param) {
            if(!isset($param)) $this->getResponseBuilder()->BuildErrorResponse();
        }
    }
}