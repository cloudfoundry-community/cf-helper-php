<?php
/**
 * Copyright (C) 2014 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT' license which can be
 * found in the file 'LICENSE' in this package distribution or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 01-07-2014
 */
namespace CfCommunity\CfHelper\Services;

/**
 * Class Service
 * @package CfCommunity\CfHelper\Services
 */
class Service
{

    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $label;
    /**
     * @var array(key => value)
     */
    private $values;

    /**
     * @var array(key => value)
     */
    private $tags;

    /**
     * @param $name
     * @param array $values
     * @param null $label
     * @param array $tags
     */
    function __construct($name, array $values, $label = null, $tags = array())
    {
        $this->name = $name;
        $this->values = $values;
        $this->label = $label;
        $this->tags = $tags;
    }

    /**
     * @param array $datas
     */
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

    /**
     * @param $key
     * @return mixed|null
     */
    public function getValue($key)
    {
        foreach ($this->values as $keyObject => $value) {
            if (preg_match('#^' . $key . '$#i', $keyObject)) {
                return $this->values[$keyObject];
            }
        }
        return null;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @param array $tags
     * @return bool
     */
    public function haveOneOfTags($tags)
    {
        if (!is_array($tags)) {
            $tags = array($tags);
        }
        foreach ($tags as $tag) {
            if ($this->haveTag($tag)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $tagToFind
     * @return bool
     */
    public function haveTag($tagToFind)
    {
        foreach ($this->tags as $tag) {
            if (preg_match('#^' . $tagToFind . '$#i', $tag)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setValue($key, $value)
    {
        $this->values[$key] = $value;
        return $this;
    }

}
