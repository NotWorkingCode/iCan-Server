<?php

require_once "BaseMethod.php";

class ErrorNoMethod extends BaseMethod {
    public function __construct()
    {
        $this->getResponseBuilder()->BuildErrorResponse();
    }
}