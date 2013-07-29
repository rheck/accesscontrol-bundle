<?php

namespace Rheck\AccessControlBundle\Exception;

class StrategyNotFoundException extends \Exception
{
    public function __construct($id, \Exception $previous = null)
    {
        $msg  = 'You have request a non-service strategy. ';
        $msg .= sprintf('You have sure that you have registered your strategy "%s" as service?', $id);

        parent::__construct($msg, 0, $previous);
    }
}
