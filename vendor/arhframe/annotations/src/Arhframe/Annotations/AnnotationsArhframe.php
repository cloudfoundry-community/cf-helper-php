<?php
namespace Arhframe\Annotations;
include_once __DIR__ . '/RequiredAnnotation.php';
/*
 * This file is part of the phpalchemy package.
 *
 * (c) Erik Amaru Ortiz <aortiz.erik@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class Annotations
 * @author Arthur Halet <arthurhalet@free.fr>
 */
class AnnotationsArhframe
{
    /**
     * Static array to store already parsed annotations
     * @var array
     */
    private static $annotationCache;
    /**
     * Stores the default namespace for Objects instance, usually used on methods like getMethodAnnotationsObjects()
     * @var string
     */
    public $defaultNamespace = 'Arhframe\\Annotations\\';
    /**
     * Indicates that annotations should has strict behavior, 'false' by default
     * @var boolean
     */
    private $strict = false;

    /**
     * Gets all anotations with pattern @SomeAnnotation() from a determinated property of a given class
     *
     * @param  string $className class name
     * @param  string $propertyName property name to get annotations
     * @return array  self::$annotationCache all annotated elements of a method given
     */
    public static function getPropertyAnnotations($className, $propertyName)
    {
        if (!isset(self::$annotationCache[$className . ':::' . $propertyName])) {
            try {
                $property = new \ReflectionProperty($className, $propertyName);
                $annotations = self::parseAnnotations($property->getDocComment());
            } catch (\ReflectionException $e) {
                $annotations = array();
            }

            self::$annotationCache[$className . ':::' . $propertyName] = $annotations;
        }

        return self::$annotationCache[$className . ':::' . $propertyName];
    }

    /**
     * Parse annotations
     *
     * @param  string $docblock
     * @return array  parsed annotations params
     */
    private static function parseAnnotations($docblock)
    {
        $annotations = array();
        // Strip away the docblock header and footer to ease parsing of one line annotations
        $docblock = substr($docblock, 3, -2);

        if (preg_match_all('/@(?<name>[A-Za-z_-]+)[\s\t]*(\((?<args>.*)\))?[\s\t]*\r?$/m', $docblock, $matches)) {
            $numMatches = count($matches[0]);
            for ($i = 0; $i < $numMatches; ++$i) {
                // annotations has arguments
                if (isset($matches['args'][$i])) {
                    $argsParts = trim($matches['args'][$i]);
                    $name = $matches['name'][$i];
                    $value = self::parseArgs($argsParts);
                } else {
                    $value = array();
                }

                $annotations[$name][] = $value;
            }
        }

        return $annotations;
    }

    /**
     * Parse individual annotation arguments
     *
     * @param  string $content arguments string
     * @return array  annotated arguments
     */
    private static function parseArgs($content)
    {
        $data = array();
        $len = strlen($content);
        $i = 0;
        $var = '';
        $val = '';
        $level = 1;

        $prevDelimiter = '';
        $nextDelimiter = '';
        $nextToken = '';
        $composing = false;
        $type = 'plain';
        $delimiter = null;
        $quoted = false;
        $tokens = array('"', '"', '{', '}', ',', '=');

        while ($i <= $len) {
            $c = substr($content, $i++, 1);

            if ($c === '\'' || $c === '"') {
                $delimiter = $c;
                //open delimiter
                if (!$composing && empty($prevDelimiter) && empty($nextDelimiter)) {
                    $prevDelimiter = $nextDelimiter = $delimiter;
                    $val = '';
                    $composing = true;
                    $quoted = true;
                } else {
                    // close delimiter
                    if ($c !== $nextDelimiter) {
                        throw new \InvalidArgumentException(sprintf(
                            "Parse Error: enclosing error -> expected: [%s], given: [%s]",
                            $nextDelimiter, $c
                        ));
                    }

                    // validating sintax
                    if ($i < $len) {
                        if (',' !== substr($content, $i, 1)) {
                            throw new \InvalidArgumentException(sprintf(
                                "Parse Error: missing comma separator near: ...%s<--",
                                substr($content, ($i - 10), $i)
                            ));
                        }
                    }

                    $prevDelimiter = $nextDelimiter = '';
                    $composing = false;
                    $delimiter = null;
                }
            } elseif (!$composing && in_array($c, $tokens)) {
                switch ($c) {
                    case '=':
                        $prevDelimiter = $nextDelimiter = '';
                        $level = 2;
                        $composing = false;
                        $type = 'assoc';
                        $quoted = false;
                        break;
                    case ',':
                        $level = 3;

                        // If composing flag is true yet,
                        // it means that the string was not enclosed, so it is parsing error.
                        if ($composing === true && !empty($prevDelimiter) && !empty($nextDelimiter)) {
                            throw new \InvalidArgumentException(sprintf(
                                "Parse Error: enclosing error -> expected: [%s], given: [%s]",
                                $nextDelimiter, $c
                            ));
                        }

                        $prevDelimiter = $nextDelimiter = '';
                        break;
                    case '{':
                        $subc = '';
                        $subComposing = true;

                        while ($i <= $len) {
                            $c = substr($content, $i++, 1);

                            if (isset($delimiter) && $c === $delimiter) {
                                throw new \InvalidArgumentException(sprintf(
                                    "Parse Error: Composite variable is not enclosed correctly."
                                ));
                            }

                            if ($c === '}') {
                                $subComposing = false;
                                break;
                            }
                            $subc .= $c;
                        }

                        // if the string is composing yet means that the structure of var. never was enclosed with '}'
                        if ($subComposing) {
                            throw new \InvalidArgumentException(sprintf(
                                "Parse Error: Composite variable is not enclosed correctly. near: ...%s'",
                                $subc
                            ));
                        }

                        $val = self::parseArgs($subc);
                        break;
                }
            } else {
                if ($level == 1) {
                    $var .= $c;
                } elseif ($level == 2) {
                    $val .= $c;
                }
            }

            if ($level === 3 || $i === $len) {
                if ($type == 'plain' && $i === $len) {
                    $data = self::castValue($var);
                } else {
                    $data[trim($var)] = self::castValue($val, !$quoted);
                }

                $level = 1;
                $var = $val = '';
                $composing = false;
                $quoted = false;
            }
        }

        return $data;
    }

    /**
     * Try determinate the original type variable of a string
     *
     * @param  string $val string containing possibles variables that can be cast to bool or int
     * @param  boolean $trim indicate if the value passed should be trimmed after to try cast
     * @return mixed   returns the value converted to original type if was possible
     */
    private static function castValue($val, $trim = false)
    {
        if (is_array($val)) {
            foreach ($val as $key => $value) {
                $val[$key] = self::castValue($value);
            }
        } elseif (is_string($val)) {
            if ($trim) {
                $val = trim($val);
            }

            $tmp = strtolower($val);

            if ($tmp === 'false' || $tmp === 'true') {
                $val = $tmp === 'true';
            } elseif (is_numeric($val)) {
                return $val + 0;
            }

            unset($tmp);
        }

        return $val;
    }

    /**
     * Sets strict variable to true/false
     * @param bool $value boolean value to indicate that annotations to has strict behavior
     */
    public function setStrict($value)
    {
        $this->strict = (bool)$value;
    }

    /**
     * Sets default namespace to use in object instantiation
     * @param string $namespace default namespace
     */
    public function setDefaultNamespace($namespace)
    {
        $this->defaultNamespace = $namespace;
    }

    /**
     * Gets default namespace used in object instantiation
     * @return string $namespace default namespace
     */
    public function getDefaultAnnotationNamespace()
    {
        return $this->defaultNamespace;
    }

    /**
     * Gets all anotations with pattern @SomeAnnotation() from a determinated method of a given class
     * and instance its abcAnnotation class
     *
     * @param  string $className class name
     * @param  string $methodName method name to get annotations
     * @return array  self::$annotationCache all annotated objects of a method given
     */
    public function getAnnotationsObjects($className, $methodName = null, $property = false)
    {

        if (!empty($methodName) && !$property) {
            $annotations = $this->getMethodAnnotations($className, $methodName);
        } elseif (!empty($methodName) && $property) {
            getPropertyAnnotations($className, $methodName);
        } else {
            $annotations = $this->getClassAnnotations($className);
        }
        $objects = array();
        $i = 0;

        foreach ($annotations as $annotationClass => $listParams) {
            $annotationClass = ucfirst($annotationClass);
            $class = $this->defaultNamespace . $annotationClass . 'Annotation';

            // verify is the annotation class exists, depending if Annotations::strict is true
            // if not, just skip the annotation instance creation.
            if (!class_exists($class)) {
                if ($this->strict) {
                    throw new \RuntimeException(sprintf('Runtime Error: Annotation Class Not Found: %s', $class));
                } else {
                    // silent skip & continue
                    continue;
                }
            }

            if (empty($objects[$annotationClass])) {
                $objects[$annotationClass] = new $class();
            }

            foreach ($listParams as $params) {
                if (is_array($params)) {
                    foreach ($params as $key => $value) {
                        $objects[$annotationClass]->set($key, $value);
                    }
                } else {
                    $objects[$annotationClass]->set($i++, $params);
                }
            }
        }

        return $objects;
    }

    /**
     * Gets all anotations with pattern @SomeAnnotation() from a determinated method of a given class
     *
     * @param  string $className class name
     * @param  string $methodName method name to get annotations
     * @return array  self::$annotationCache all annotated elements of a method given
     */
    public static function getMethodAnnotations($className, $methodName)
    {
        if (!isset(self::$annotationCache[$className . '::' . $methodName])) {
            try {
                $method = new \ReflectionMethod($className, $methodName);
                $annotations = self::parseAnnotations($method->getDocComment());
            } catch (\ReflectionException $e) {
                $annotations = array();
            }

            self::$annotationCache[$className . '::' . $methodName] = $annotations;
        }

        return self::$annotationCache[$className . '::' . $methodName];
    }

    /**
     * Gets all anotations with pattern @SomeAnnotation() from a given class
     *
     * @param  string $className class name to get annotations
     * @return array  self::$annotationCache all annotated elements
     */
    public static function getClassAnnotations($className)
    {
        if (!isset(self::$annotationCache[$className])) {
            $class = new \ReflectionClass($className);
            self::$annotationCache[$className] = self::parseAnnotations($class->getDocComment());
        }

        return self::$annotationCache[$className];
    }
}
