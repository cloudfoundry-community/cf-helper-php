<?php
/**
 * Copyright (C) 2014 Orange
 *
 * This software is distributed under the terms and conditions of the 'MIT' license which can be
 * found in the file 'LICENSE' in this package distribution or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 01-07-2014
 */
namespace orange\cfhelper\services;

class Service
{
    private $name;
    private $label;
    private $values;

    function __construct($name, array $values, $label = null)
    {
        $this->name = $name;
        $this->values = $values;
        $this->label = $label;
    }

    public function addDatas(array $datas)
    {
        foreach ($datas as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param null $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param array $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }

    public function getValue($key)
    {
        return $this->values[$key];
    }

    public function setValue($key, $value)
    {
        $this->values[$key] = $value;
        return $this;
    }

}
