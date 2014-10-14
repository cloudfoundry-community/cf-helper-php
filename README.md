cf-helper-php
=============

An helper for php application inside cloudfoundry to access application and services bindings information without parsing the json-formatted `VCAP_APPLICATION` or `VCAP_SERVICES` env vars. This is similar to the https://www.npmjs.org/package/cfenv node package.
You will never have to do this again:
```php
<?php
$vcap_services = json_decode($_ENV['VCAP_SERVICES']);
```

This helper was tested against [pivotal-cf-experimental/cf-buildpack-php](https://github.com/pivotal-cf-experimental/cf-buildpack-php) and [dmikusa-pivotal/cf-php-build-pack](https://github.com/dmikusa-pivotal/cf-php-build-pack) php buildpack.

Usage
-----
This php application is published as a composer package. Fetch it by adding the following to your composer.json:
```json
"orange-opensource/cf-helper-php": "1.2.*"
```
And include it the page you want to load:
```php
<?php
require_once __DIR__ .'/vendor/autoload.php';
use orange\cfhelper\CfHelper;
$cfHelper = CfHelper::getInstance();
```
You can access the service binding or application information through the service manager class


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
$dbService = $serviceManager->getService('database'); //or regular expression example: getService('.*database.*')
//and for example get the host credential
$host = $dbService->getValue('host');//or regular expression example: getValue('ho[A-Za-z]+')
//...
```

### Get Application's informations

Simply like this:
```php
<?php
$applicationInfo = $cfHelper->getApplicationInfo();
$version = $applicationInfo->getVersion();
$name = $applicationInfo->getName();
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
    "error_reporting": 24575, //equal to E_ALL & ~E_DEPRECATED
}
```

Set your php project in development mode
----------------------------------------
By default this two buildpacks hide error and it's not very good when you're in development phase. 
With `cf-helper-php` you can say that you are in development and app will do the rest and even show you error with [filp/whoops](https://github.com/filp/whoops) package, to do that add in your composer.json a `cfhelper` variable and put `type` variable in `developement`:
```json
"cfhelper":{
    "type": "development"
}
```

Simulate CloudFoundry environment
---------------------------------





