IocArt
=======

IocArt is an another IOC (inversion of Control) and he is close too Spring Ioc style.
The main point is that IocArt have his context file in yml.
Bean is "class" wich you can inject inside their properties:
  * Another bean
  * Property file 
  * A yaml file read by [yamlarh](https://github.com/arhframe/yamlarh)
  * A stream

You can also import other yaml context in a yaml context

Installation
=======

Through Composer, obviously:

```json
{
    "require": {
        "arhframe/iocart": "1.*"
    }
}
```

Usage
========

```php
use Arhframe\IocArt\BeanLoader;

$beanLoader = BeanLoader::getInstance();
$beanLoader->loadContext('your/yaml/file/for/context');
```

Examples
=========
