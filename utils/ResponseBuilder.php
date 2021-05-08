<?php


namespace utils;


class ResponseBuilder
{
    private array $success_response_body = array();
    private string $error_response_user_message = "Произошла ошибка в работе сервера";
    private string $error_response_debug_message = "При обращании к серверу переданы не все аргументы";

    public function AddSuccessResponseBody(array $body): ResponseBuilder
    {
        $this->success_response_body = $body;
        return $this;
    }

    public function AddErrorUserMessage(string $message): ResponseBuilder
    {
        $this->error_response_user_message = $message;
        return $this;
    }

    public function AddErrorDebugMessage(string $message): ResponseBuilder
    {
        $this->error_response_debug_message = $message;
        return $this;
    }

    public function BuildSuccessResponse(int $response_code = RESPONSE_CODE_SUCCESS)
    {
        $response = json_encode(array(
            "response_code" => $response_code,
            "body" => $this->success_response_body
        ));
        $this->Reset();
        exit($response);
    }

    public function BuildErrorResponse(int $response_code = RESPONSE_CODE_ERROR)
    {
        $response = json_encode(array(
            "response_code" => $response_code,
            "user_message" => $this->error_response_user_message,
            "debug_message" => $this->error_response_debug_message
        ));
        $this->Reset();
        exit($response);
    }

    private function Reset()
    {
        $this->error_response_debug_message = "При обращании к серверу переданы не все аргументы";
        $this->error_response_user_message = "Произошла ошибка в работе сервера";
        $this->success_response_body = array();
    }
}