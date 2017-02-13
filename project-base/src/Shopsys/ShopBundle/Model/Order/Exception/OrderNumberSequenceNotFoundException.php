<?php

namespace Shopsys\ShopBundle\Model\Order\Exception;

use Exception;
use Shopsys\ShopBundle\Model\Order\Exception\OrderException;

class OrderNumberSequenceNotFoundException extends Exception implements OrderException
{
    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
