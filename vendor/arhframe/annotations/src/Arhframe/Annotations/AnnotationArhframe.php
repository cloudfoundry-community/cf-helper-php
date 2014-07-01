<?php
namespace Arhframe\Annotations;


/**
 * Abstract class for arhframe annotation
 * @author arthur halet <arthurh.halet@gmail.com>
 */
abstract class AnnotationArhframe
{
    protected $data = array();


    /**
     * Contructor
     * @param array $args argument passed to annotation
     */
    public function __construct($args = array())
    {
        $this->data = $args;
    }


    /**
     * Set one of argument
     * @param string $key key argument
     * @param mixed $value value argument
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }


    /**
     * Get one of argument
     * @param string $key argument's key
     * @param mixed $default default value to return if not found
     * @return mixed your value
     */
    public function get($key, $default = null)
    {
        if (empty($this->data[$key])) {
            return $default;
        }

        return $this->data[$key];
    }


    /**
     * Verify if argument exist
     * @param string $key argument's key
     */
    public function exists($key)
    {
        return isset($this->data[$key]);
    }
}
