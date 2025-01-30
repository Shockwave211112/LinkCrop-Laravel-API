<?php

namespace App\Modules\Core\Exceptions;

use Exception;

class EmailException extends Exception
{
    /**
     * @param string $message
     * @param int $status
     */
    public function __construct(string $message = 'Email logic exception', int $status = 403)
    {
        $this->message = $message;
        $this->status = $status;
    }
}
