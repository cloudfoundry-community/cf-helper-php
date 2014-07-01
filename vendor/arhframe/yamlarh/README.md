Yamlarh
=======

Yml injector for arhframe in standalone.
You can inject into your yaml:
  * object
  * constant from scope 
  * Variable from global scope
  * Variable from yaml file

You can also import other yaml inside a yaml file for overriding

Installation
=======

Through Composer, obviously:

```json
{
    "require": {
        "arhframe/yamlarh": "1.*"
    }
}
```

Usage
========

```php
use Arhframe\Yamlarh\Yamlarh;

$yamlarh = new Yamlarh(__DIR__.'/path/to/yaml/file');
$array = $yamlarh->parse();
```

Exemple
=========

Variable injection
---------

Variable injection is hierarchical, it will find in this order:
  1. In the yaml file with import
  2. In your global scope
  3. In you constant

Yaml file:
```yml
arhframe:
  myvar1: test
  myvar2: %arhframe.myvar1%
  myvar3: %var3%
  myvar4: %VARCONSTANT%
```

Php file:
```php
use Arhframe\Yamlarh\Yamlarh;
$var3 = 'testvar';
define('VARCONSTANT', 'testconstant');
$yamlarh = new Yamlarh(__DIR__.'/test.yml');
$array = $yamlarh->parse();
echo print_r($array);
```

Output:
```
  Array
  (
      [arhframe] => Array
          (
              [myvar1] => test
              [myvar2] => test
              [myvar3] => testvar
              [myvar4] => testconstant
          )

  ) 
```

Object injection
---------
It use [snakeyml](https://code.google.com/p/snakeyaml/wiki/Documentation#Compact_Object_Notation) (yaml parser for java) style:
```yml
arhframe:
  file: !! Arhframe.Util.File(test.php) #will instanciate this: Arhframe\Util\File('test.php') in file var after parsing
```

Import
---------
Import are also hierarchical the last one imported will override the others.
Use @import in your file: 

file1.yml
```yml
arhframe:
  var1: var
test: arhframe

@import:
 - file2.yml #you can use a relative path to your yaml file or an absolute
```

file2.yml
```yml
arhframe:
  var1: varoverride
test2: var3
```

After parsing file1.yml, yml will look like:
```yml
arhframe:
  var1: varoverride
test: arhframe
test2: var3
```

Include
---------
You can include a yaml file into another:

file1.yml
```yml
arhframe:
  var1: var
test:
  @include:
    - file2.yml #you can use a relative path to your yaml file or an absolute
```

file2.yml
```yml
test2: var3
```

After parsing file1.yml, yml will look like:
```yml
arhframe:
  var1: var
test:
  test2: var3
```
