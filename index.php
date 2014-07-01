<?php
require_once __DIR__ . '/vendor/autoload.php';
var_dump($_ENV);
$beanLoader = \Arhframe\IocArt\BeanLoader::getInstance();
$beanLoader->loadContext(__DIR__ . '/context/main.yml');
$populator = $beanLoader->getBean('elpaaso.populator');

$serviceManager = $beanLoader->getBean('elpaaso.serviceManager');
$service = $serviceManager->getService('database-cftest');
$phpIniConfigurator = $beanLoader->getBean('elpaaso.phpIniConfigurator');
$phpIniConfigurator->loadIniConfig();
echo $service->getValue('host');
echo '<br/>';
echo $service->getValue('port');


