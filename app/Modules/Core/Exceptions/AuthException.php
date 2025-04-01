<?php

namespace App\Modules\Core\Exceptions;

use Exception;

class AuthException extends Exception
{
    /**
     * @param string $message
     * @param int $status
     */
    public function __construct(string $message = 'Auth logic exception', int $status = 401)
    {
        $this->message = $message;
        $this->status = $status;
    }
}
