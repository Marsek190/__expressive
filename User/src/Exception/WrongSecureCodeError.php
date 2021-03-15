<?php
declare(strict_types=1);

namespace User\Exception;


final class WrongSecureCodeError extends \Exception
{
    /**
     * @param string $message
     */
    public function __construct(string $message = "")
    {
        parent::__construct($message, 0, null);
    }
}