<?php

namespace App\Helpers;

class OrganizeResponse
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var bool
     */
    protected $error;

    /**
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     */
    public function __construct(int $statusCode, string $message = '', $data = null)
    {
        $this->data = $data;
        $this->message = $message;
        $this->statusCode = $statusCode;
        $this->error = $this->isError($statusCode);
    }

    /**
     * @param int $statusCode
     * @return bool
     */
    protected function isError($statusCode): bool
    {
        return $statusCode >= 400;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return bool
     */
    public function getError(): bool
    {
        return $this->error;
    }
}
