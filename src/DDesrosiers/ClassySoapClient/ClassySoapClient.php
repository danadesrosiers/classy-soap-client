<?php

namespace DDesrosiers\ClassySoapClient;

use SoapClient;

class ClassySoapClient extends SoapClient
{
    public function __construct($wsdl, $options=array())
    {
        // the logic depends on this, so it can't be overridden
        $options['features'] = SOAP_SINGLE_ELEMENT_ARRAYS;

        parent::SoapClient($wsdl, $options);
    }

    protected function executeSoapCall($name, $args)
    {
        $input = array($this->toArray($args));
        $response = parent::__call($name, $input);
        return $this->convertResponse($response, $name);
    }

    protected function convertResponse($response, $method_name)
    {
        $meta = new \ReflectionMethod($this, $method_name);
        $class_name = $this->getAnnotationValue($meta->getDocComment(), '@return');
        return $this->convertStdClassToObject($response, $class_name);
    }

    protected function convertStdClassToObject($object, $class_name)
    {
        if (is_array($object))
        {
            $class_name = str_replace('[]', '', $class_name);
            $new_object = array();
            foreach ($object as $obj)
            {
                $new_object[] = $this->convertStdClassToObject($obj, $class_name);
            }
        }
        else if (class_exists($class_name))
        {
            $meta = new \ReflectionClass($class_name);
            $new_object = new $class_name();
            foreach ($object as $name => $value)
            {
                if ($meta->hasProperty($name))
                {
                    $class_name = $this->getAnnotationValue($meta->getProperty($name)->getDocComment(), '@var');
                    $new_object->$name = $this->convertStdClassToObject($value, $class_name);
                }
            }
        }
        else
        {
            $new_object = $object;
        }

        return $new_object;
    }

    protected function toArray($input)
    {
        if (is_object($input))
        {
            $input = (array) $input;
        }

        $return_val = $input;
        if (is_array($input))
        {
            $return_val = array();
            foreach ($input as $key => $value)
            {
                if (!is_null($value))
                {
                    $return_val[$key] = $this->toArray($value);
                }
            }
        }

        return $return_val;
    }

    protected function getAnnotationValue($comment, $annotation)
    {
        $value = false;
        if (preg_match('/'.$annotation.'\s+\S+/', $comment, $matches))
        {
            $value = trim(str_replace($annotation, '', $matches[0]));
        }

        return $value;
    }
}
