cf-helper-php
=============

An helper for php application inside cloudfoundry to get services binded and application information.
This helper work was tested on [pivotal-cf-experimental/cf-buildpack-php](https://github.com/pivotal-cf-experimental/cf-buildpack-php) and [dmikusa-pivotal/cf-php-build-pack](https://github.com/dmikusa-pivotal/cf-php-build-pack) php buildpack.

Usage
-----
This php application is a composer package so use composer and put in your required package inside your composer.json:
```json
"orange-opensource/cf-helper-php": "1.1.*"
```
And include in page you want to load this helper like this:
```php
<?php
require_once __DIR__ .'/vendor/autoload.php';
use orange\cfhelper\CfHelper;
$cfHelper = CfHelper::getInstance();
```
You can access to service manager or application information.


### Get your service(s)

For example you have a service called `database` with this credentials:
```json
{
    "host": "localhost",
    "username": "jojo",
    "password": "toto",
    "port": "3306"
}
```
You can simply get your service like this:
```php
<?php
$dbService = $serviceManager->getService('database');
//and for example get the host credential
$host = $dbService->getValue('host');
//...
```

### Get Application's informations

Simply like this:
```php
<?php
$applicationInfo = $cfHelper->getApplicationInfo();
$version = $applicationInfo->getVersion();
$name = $applicationInfo->getName();
$host = $applicationInfo->getHost();
$uris = $applicationInfo->getUris();

//for other information contains in VCAP_APPLICATION like limits get with that
$limits = $applicationInfo->limits
```

Set php configuration
-------------------------
With [pivotal-cf-experimental/cf-buildpack-php](https://github.com/pivotal-cf-experimental/cf-buildpack-php) you can set a `.user.ini` file to set your php configuration but it's not very flexible, you can also use directly `ini_set()` but you will have to do all by your own.

So with `cf-helper-php` we help you to set your php configuration, add in your composer.json a `php-ini` variable and set your php configuration, example:
```json
"php-ini": {
    "display_errors": "On",
    "error_reporting": -1, //equal to E_ALL & ~E_DEPRECATED
}
```

Set your php project in development mode
----------------------------------------
By default this two buildpacks hide error and it's not very good when you're in development phase. 
With `cf-helper-php` you can say that you are in development and app will do the rest and even show you error with [filp/whoops](https://github.com/filp/whoops) package, to do that add in your composer.json a `cfhelper` variable and put `type`variable in `developement`:
```json
"cfhelper":{
    "type": "development"
}
```





