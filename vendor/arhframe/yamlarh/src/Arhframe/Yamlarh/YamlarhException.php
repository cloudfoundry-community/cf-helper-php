<?php
namespace Arhframe\Yamlarh;
/**
*
*/
final class YamlarhException extends \Exception
{
    public function __construct($message = "",$code=0, $previous = NULL)
    {
        parent::__construct('Yamlarh exception: '. $message, $code, $previous);
    }
}
