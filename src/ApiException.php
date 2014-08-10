<?php

namespace Aveiv\OpenExchangeRatesApi;

use Exception;

class ApiException extends \Exception {
    /**
     * @var string
     */
    protected $messageCode;

    /**
     * @param string $message
     * @param string $messageCode
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($message = "", $messageCode = "", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->messageCode = $messageCode;
    }


    /**
     * @return string
     */
    public final function getMessageCode() {
        return $this->messageCode;
    }
}
