<?php

namespace Arhframe\IocArt;
/**
*
*/

/**
 * Class for manipulate bean from iocart and load the context in iocart
 * @author Arthur Halet <arthurh.halet@gmail.com>
 */
class BeanLoader
{
    private $ioc;
    private static $_instance = null;
    private function __construct()
    {
        
    }
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new BeanLoader();
        }

        return self::$_instance;
    }
    public function addBean($beanId, $beanValue)
    {
        $this->ioc->addBean($beanId, $beanValue);

        return $this;
    }
    public function removeBean($beanId)
    {
        $this->ioc->removeBean($beanId);

        return $this;
    }
    public function getBean($beanId)
    {
        return $this->ioc->getBean($beanId);
    }
    public function loadContext($contextFile)
    {
        $this->ioc = new IocArt($contextFile);
        $this->ioc->loadContext();

        return $this;
    }
}
