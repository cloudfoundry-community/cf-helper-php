<?php
namespace Arhframe\IocArt;
/**
*
*/
final class IocArtException extends \Exception
{
    public function __construct($message = "",$code=0, $previous = NULL)
    {
        parent::__construct('IocArt exception: '. $message, $code, $previous);
    }
}
