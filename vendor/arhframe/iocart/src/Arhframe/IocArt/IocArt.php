<?php
namespace Arhframe\IocArt;

use Symfony\Component\Yaml\Yaml;
use Arhframe\Annotations\AnnotationsArhframe;
use Arhframe\Yamlarh\Yamlarh;


/**
 *
 */
class IocArt
{
    private $bean = array();
    private $object = null;
    private $pathinfoContext;
    private $annotations;
    private $required = null;
    private $fileLoaded = array();

    public function __construct($context)
    {
        $this->pathinfoContext = pathinfo($context);
        $this->annotations = new AnnotationsArhframe();
        $this->importYml($context);
        if (!defined("ROOT")) {
            define("ROOT", $this->pathinfoContext['dirname']);
        }
    }

    private function importYml($file, $loadedBy = null)
    {
        $fileOriginal = $file;
        $file = trim($file);
        if ($file[0] != '/' && $file[1] != ':') {
            $file = $this->pathinfoContext['dirname'] . '/' . $file;
        }
        if (!is_file($file)) {
            throw new IocArtException("import '" . $fileOriginal . "' can't be found.");
        }
        if (!empty($this->fileLoaded[$file])) {
            throw new IocArtException("Redundant import in context file '" . $file . "' imported by '" . $loadedBy . "'.");
        }

        $array = Yaml::parse($file);
        if (!is_array($array)) {
            return;
        }
        if (empty($loadedBy)) {
            $loadedBy = $file;
        }
        $this->fileLoaded[$file] = $loadedBy;
        $this->bean = array_merge($this->bean, $array);
        if (!empty($this->bean['@import'])) {
            $import = $this->bean['@import'];
            unset($this->bean['@import']);
            if (!is_array($import)) {
                $this->importYml($import, $file);
            } else {
                foreach ($import as $fileName) {
                    $this->importYml($fileName, $file);
                }
            }

        }

    }

    public function loadContext()
    {
        $this->importBean();
        $this->instanciateAll();
    }

    private function importBean()
    {
        if (empty($this->bean)) {
            return;
        }
        foreach ($this->bean as $id => $bean) {
            if (strtolower($bean['type']) == 'abstract' || empty($bean['class']) || !empty($this->object[$id])) {
                continue;
            }
            try {
                new $bean['class'];
            } catch (Exception $e) {
                throw new IocArtException($e->getMessage());

            }
        }

    }

    private function instanciateAll()
    {
        if (empty($this->bean)) {
            return;
        }
        foreach ($this->bean as $id => $bean) {
            $this->instanciator($bean, $id);
        }
        $this->verifyRequired();
    }

    private function instanciator($bean, $beanId)
    {
        if (empty($bean) || empty($beanId) || empty($bean['class']) || !empty($this->object[$beanId])) {
            return;
        }
        if (!empty($bean['extend'])) {
            $bean = $this->herited($beanId, $bean['extend']);
        }
        if (strtolower($bean['type']) == 'abstract') {
            return;
        }
        $className = $bean['class'];


        if (!empty($this->object[$className])) {
            return;
        }
        $object = new \ReflectionClass($className);
        $this->object[$beanId] = $object->newInstance();
        $this->addAnnotationRequired($beanId, $className);
        if (empty($bean['property'])) {
            return;
        }
        foreach ($bean['property'] as $key => $value) {
            $contentBean = $this->inject($value, $beanId);
            if (count($contentBean) == 1 && is_array($contentBean)) {
                $contentBean = current($contentBean);
            }
            if (!empty($contentBean)) {
                $makeSetter = "set" . ucfirst($key);
                $this->object[$beanId]->$makeSetter($contentBean);
                $this->toggleRequired($beanId, $makeSetter);
            }

        }

    }

    private function herited($beanId, $beanIdExtend)
    {
        if (empty($this->bean[$beanIdExtend])) {
            throw new IocArtException("Bean '" . $beanIdExtend . "' doesn't exist for extended bean '" . $beanId . "'");
        }
        unset($this->bean[$beanIdExtend]['type']);
        unset($this->bean[$beanId]['extend']);
        $this->bean[$beanId] = array_merge($this->bean[$beanId], $this->bean[$beanIdExtend]);

        return $this->bean[$beanId];
    }

    private function addAnnotationRequired($beanId, $className)
    {

        if (!class_exists($className) || !empty($this->required[$beanId])) {
            return;
        }

        $class = new \ReflectionClass($className);
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if (substr($method->name, 0, 3) == 'set') {
                $result = $this->annotations->getAnnotationsObjects($className, $method->name);
                if (!empty($result['Required'])) {
                    $this->required[$beanId][$method->name] = false;
                }
            }
        }
    }

    private function inject($value, $beanId)
    {
        $contentBean = null;
        if (!empty($value['ref'])) {
            $contentBean = $this->injectBean($value['ref']);
        }
        if (!empty($value['value'])) {
            $contentBean = $value['value'];
        }
        if (!empty($value['stream']['resource'])) {
            $contentBean = $this->injectStream($value['stream'], $beanId);
        }
        if (!empty($value['yml']) || !empty($value['yaml'])) {
            $contentBean = $this->injectYaml($value, $beanId);
        }
        if (!empty($value['propertyfile'])) {
            $contentBean = $this->injectPropertyFile($value['propertyfile'], $beanId);
        }
        return $contentBean;
    }

    private function injectBean($refs)
    {
        $array = array();
        if (!is_array($refs)) {
            $refs = array($refs);
        }
        foreach ($refs as $key => $ref) {
            $this->instanciator($this->bean[$ref], $ref);
            $array[$key] = $this->object[$ref];
        }
        return $array;
    }

    private function injectStream($streams, $beanId)
    {
        $array = array();
        if (!is_array($streams) || !empty($streams['resource'])) {
            $streams = array($streams);
        }
        foreach ($streams as $key => $stream) {
            $resource = $stream['resource'];
            if (empty($resource)) {
                throw new IocArtException("Resource for stream in bean '" . $beanId . "' cannot be null");
            }
            $context = $stream['context'];
            if (!empty($context)) {
                $contentBean = file_get_contents($resource, 0, stream_context_create($context));
            } else {
                $contentBean = file_get_contents($resource);
            }
            $array[$key] = $contentBean;
        }
        return $array;
    }

    private function injectYaml($yamls, $beanId)
    {
        $array = array();
        if (!is_array($yamls)) {
            $yamls = array($yamls);
        }
        foreach ($yamls as $key => $yaml) {
            if ($yaml[0] != '/' && $yaml[1] != ':') {
                $yaml = $this->pathinfoContext['dirname'] . '/' . $yaml;
            } else {
                $yaml = ROOT . '/' . $yaml;
            }
            try {
                $yamlArh = new Yamlarh($yaml);
                $array[$key] = $yamlArh->parse();
            } catch (Exception $e) {
                throw new IocArtException("Error in bean '" . $beanId . "': " . $e->getMessage());
            }
        }
        return $array;
    }

    private function injectPropertyFile($fileNames, $beanId)
    {
        $array = array();
        if (!is_array($fileNames)) {
            $fileNames = array($fileNames);
        }
        foreach ($fileNames as $key => $fileName) {
            if ($fileName[0] != '/' && $fileName[1] != ':') {
                $fileName = $this->pathinfoContext['dirname'] . '/' . $fileName;
            } else {
                $fileName = ROOT . '/' . $fileName;
            }
            $file = $fileName;
            $this->isFile($file, $beanId, $fileName);
            $array[$key] = parse_ini_file($file);
        }
        return $array;
    }

    private function isFile($file, $beanId, $fileName)
    {
        if (!is_file($file)) {
            throw new IocArtException("Property file '" . $fileName . "' in bean '" . $beanId . "' doesn't exist.");
        }
    }

    private function toggleRequired($beanId, $methodName)
    {
        if (!isset($this->required[$beanId][$methodName])) {
            return;
        }
        $this->required[$beanId][$methodName] = true;
    }

    private function verifyRequired()
    {
        if (empty($this->required)) {
            return;
        }
        foreach ($this->required as $beanId => $methods) {
            foreach ($methods as $methodName => $boolean) {
                if (!$boolean) {
                    $methodName = strtolower(substr($methodName, 3));
                    throw new IocArtException("Method '" . $methodName . "' is required for bean '" . $beanId . "'");
                }
            }
        }
    }

    public function addBean($beanId, $beanConfig)
    {
        $this->bean[$beanId] = $beanConfig;
    }

    public function removeBean($beanId)
    {
        unset($this->bean[$beanId]);
        unset($this->object[$beanId]);
        unset($this->required[$beanId]);
    }

    public function getBean($beanId)
    {
        return $this->object[$beanId];
    }

    public function getFileLoaded()
    {
        return $this->fileLoaded;
    }


}
