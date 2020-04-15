cf-helper-php
=============

An helper for php application inside cloudfoundry to access application and services bindings information without parsing the json-formatted `VCAP_APPLICATION` or `VCAP_SERVICES` env vars. This is similar to the https://www.npmjs.org/package/cfenv node package.
You will never have to do this again:
```php
// Don't do this
$vcap_services = json_decode($_ENV['VCAP_SERVICES']);
```

This helper works with official [php buildpack](https://github.com/cloudfoundry/php-buildpack).

Usage
-----
This php application is published as a composer package. Fetch it by adding the following to your composer.json:
```json
"cloudfoundry-community/cf-helper-php": "^2.0"
```
And include it the page you want to load:
```php
require_once __DIR__ .'/vendor/autoload.php';
use CfCommunity\CfHelper\CfHelper;
$cfHelper = new CfHelper();
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
$serviceManager = $cfHelper->getServiceManager();
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
$applicationInfo = $cfHelper->getApplicationInfo();
$version = $applicationInfo->getVersion();
$name = $applicationInfo->getName();
$uris = $applicationInfo->getUris();

//for other information contains in VCAP_APPLICATION like limits get with that
$limits = $applicationInfo->limits;
```

### Get a connector

`cf-helper-php` provide some connectors by auto-detecting.

It give you the possibility to have a PDO object when database is provided in services, or  a Predis\Client object when you provide a redis (look at [Predis](https://github.com/nrk/predis) ) or a [MongoClient](http://php.net/manual/fr/class.mongodb.php) object when a mongodb is provided.

To get this access just follow this this:
```php
$pdo = $cfHelper->getDatabaseConnector()->getConnection();
$redis = $cfHelper->getRedisConnector()->getConnection();
$mongodb = $cfHelper->getMongoDbConnector()->getConnection();
```

You can directly get credentials by doing `$cfHelper->get<TypeConnector>Connector()->getCredentials()` it will give you an array with:

 - host
 - port
 - pass
 - user
 - url (if url is provided by the service)
 - sentencePdo (**only** for database connector)
 - database (**only** for database connector)

### Example usage of pdo connector

```php
require_once __DIR__ .'/vendor/autoload.php';
use CfCommunity\CfHelper\CfHelper;
$cfHelper = new CfHelper();

//if we are in cloud foundry we use the connection given by cf-helper-php otherwise we use our database in local
if ($cfHelper->isInCloudFoundry()) {
    $db = $cfHelper->getDatabaseConnector()->getConnection();
} else {
    $db = new PDO('mysql:host=localhost;dbname=mydbinlocal;charset=utf8', 'root', '');
}
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//...
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
You can simulate a CloudFoudry environment by using a `vcap.json`, your environment variable from manifest will be set in `$_ENV`. This JSON format will mimic what you would find in VCAP_APPLICATION and VCAP_SERVICES, with the addition of an 'ENV' parameter to add environment variables.

```json
{
  "VCAP_SERVICES": {
    "user-provided": [
      {
        "label": "user-provided",
        "name": "managed-ELK-logging",
        "tags": [],
        "instance_name": "managed-ELK-logging",
        "binding_name": null,
        "credentials": {},
        "syslog_drain_url": "syslog://tcplogs-epg.localdomain:5000",
        "volume_mounts": []
      }
    ],
    "p.redis": [
      {
        "label": "p.redis",
        "provider": null,
        "plan": "cache-large",
        "name": "redis-sessions",
        "tags": [
          "redis",
          "pivotal",
          "on-demand"
        ],
        "instance_name": "redis-sessions",
        "binding_name": null,
        "credentials": {
          "host": "q-s0.redis-instance.svc-dmd.service-instance-8e8fb07f-9c0b-4313-8748-0cf61f0ee989.bosh",
          "password": "redispassword",
          "port": 6379
        },
        "syslog_drain_url": null,
        "volume_mounts": []
      }
    ]
  },
  "VCAP_APPLICATION": {
    "cf_api": "https://api.sys.pcf.localdomain",
    "limits": {
      "fds": 16384
    },
    "application_name": "api-active",
    "application_uris": [
      "api-qa.apps.pcf.localdomain"
    ],
    "name": "api-active",
    "space_name": "epg02-qa",
    "space_id": "d3325332-9abb-4136-9232-f7e244f51817",
    "organization_id": "4fe703b7-199c-4a63-aaa9-6261de724641",
    "organization_name": "epg02-local-team",
    "uris": [
      "api-qa.apps.pcf.localdomain"
    ],
    "users": null,
    "application_id": "8bee55d9-5f5c-4d70-88a7-f1c159d9652e"
  },
  "ENV": {
    "APPLICATION_VERSION": "1.0.0"
  }
}

```

To run CloudFoundry simulation simply do:
```php
$cfHelper->simulateCloudFoundry(); //it use vcap.json which is in the same folder where this script is called
//to set another vcap.json:
$cfHelper->simulateCloudFoundry("your_vcap.json");
```



