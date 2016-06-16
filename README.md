cf-helper-php
=============

An helper for php application inside cloudfoundry to access application and services bindings information without parsing the json-formatted `VCAP_APPLICATION` or `VCAP_SERVICES` env vars. This is similar to the https://www.npmjs.org/package/cfenv node package.
You will never have to do this again:
```php
<?php
$vcap_services = json_decode($_ENV['VCAP_SERVICES']);
```

This helper works with official [php buildpack](https://github.com/cloudfoundry/php-buildpack).

Usage
-----
This php application is published as a composer package. Fetch it by adding the following to your composer.json:
```json
"cloudfoundry-community/cf-helper-php": "1.6.*"
```
And include it the page you want to load:
```php
<?php
require_once __DIR__ .'/vendor/autoload.php';
use CfCommunity\CfHelper\CfHelper;
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
$serviceManager = CfHelper::getInstance()->getServiceManager();
$dbService = $serviceManager->getService('database'); //or regular expression example: getService('.*database.*')
//and for example get the host credential
$host = $dbService->getValue('host');//or regular expression example: getValue('ho[A-Za-z]+')

//get all your services
$services = $serviceManager->getAllServices();

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

### Get a connector

`cf-helper-php` provide some connectors by auto-detecting.

It give you the possibility to have a PDO object when database is provided in services, or  a Predis\Client object when you provide a redis (look at [Predis](https://github.com/nrk/predis) ) or a [MongoClient](http://php.net/manual/fr/class.mongodb.php) object when a mongodb is provided.

To get this access just follow this this:
```php
<?php
$pdo = CfHelper::getInstance()->getDatabaseConnector()->getConnection();
$redis = CfHelper::getInstance()->getRedisConnector()->getConnection();
$mongodb = CfHelper::getInstance()->getMongoDbConnector()->getConnection();
```

You can directly get credentials by doing `CfHelper::getInstance()->get<TypeConnector>Connector()->getCredentials()` it will give you an array with:

 - host
 - port
 - pass
 - user
 - url (if url is provided by the service)
 - sentencePdo (**only** for database connector)
 - database (**only** for database connector)

### Example usage of pdo connector

```php
<?php
require_once __DIR__ .'/vendor/autoload.php';
use CfCommunity\CfHelper\CfHelper;
$cfHelper = CfHelper::getInstance();
try {
    //if we are in cloud foundry we use the connection given by cf-helper-php otherwise we use our database in local
    if ($cfHelper->isInCloudFoundry()) {
        $db = $cfHelper->getDatabaseConnector()->getConnection();
    } else {
        $db = new PDO('mysql:host=localhost;dbname=mydbinlocal;charset=utf8', 'root', '');
    }
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
//...
```

### Get the logger

You have also access to a logger set for Cloud Foundry environment, access to it like this:

```php
<?php
$logger = CfHelper::getInstance()->getLogger();
```

Set php configuration
-------------------------
With `cf-helper-php` we help you to set your php configuration, add in a new file in root project directory called `cfhelper.json` a `php-ini` variable and set your php configuration, example:
```json
"php-ini": {
    "display_errors": "On",
    "error_reporting": 24575, //equal to E_ALL & ~E_DEPRECATED
}
```

Set your php project in development mode
----------------------------------------
By default this two buildpacks hide error and it's not very good when you're in development phase. 
With `cf-helper-php` you can say that you are in development and app will do the rest and even show you error with [filp/whoops](https://github.com/filp/whoops) package, to do that add in a new file in root project directory called `cfhelper.json` a `cfhelper` variable and put `type` variable in `developement`:
```json
//in cfhelper.json in your root project directory
"cfhelper":{
    "type": "development"
}
```

Simulate CloudFoundry environment
---------------------------------
You can half simulate a CloudFoudry environment by using a `manifest.yml`, your environment variable from manifest will be set in `$_ENV`.
You can also add simulate service by adding a key called `serviceSimulate` in your `manifest.yml`, example:

```yml
#manifest.yml
---
#manifest
applications:
  - name: test
    memory: 1G
    env:
      MYAPP_APP_DIR: /home/vcap/app
      MYAPP_LOGS_DIR: /logs_dir
serviceSimulate:
  DATABASE: {"host": "localhost", "username": "jojo", "password": "toto", "port": "3306"} # a service database will be accessible, prefer writing with {'key": 'value'} to simplify your cups command
```

To run CloudFoundry simulation simply do:
```php
<?php
$cfHelper->simulateCloudFoundry(); //it use manifest.yml which is in the same folder where this script is called
//to set another manifest.yml:
$cfHelper->simulateCloudFoundry("your_manifest.yml);
```



