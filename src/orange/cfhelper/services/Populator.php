<?php
namespace orange\cfhelper\services;
abstract class  Populator
{

    function __construct()
    {
    }

    public abstract function getService($name);


}