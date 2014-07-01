<?php
namespace Arhframe\Util;
/**
*
* 
*/
final class UtilException extends \Exception
{
    public function __construct($message = "",$code=0, $previous = NULL)
    {
        parent::__construct('Arhframe util exception: '. $message, $code, $previous);
    }
}
