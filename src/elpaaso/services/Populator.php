<?php
namespace elpaaso\services;
abstract class  Populator
{

    function __construct()
    {
    }

    public abstract function getService($name);


}