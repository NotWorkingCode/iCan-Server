<?php

use utils\Database;
use utils\ResponseBuilder;

class BaseMethod
{
    public function getResponseBuilder(): ResponseBuilder {
        return new ResponseBuilder();
    }
    public function getDatabase(): Database {
        return Database::getInstance();
    }
}