<?php

namespace Illuminated\Console\Mutex\Exceptions;

use Exception;
use Symfony\Component\Console\Exception\RuntimeException as SymfonyRuntimeException;

class RuntimeException extends SymfonyRuntimeException
{
    private $context;
    public function __construct($message = '', array $context = [], $code = 0, Exception $previous = null)
    {
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }
    public function getContext()
    {
        return $this->context;
    }
}
