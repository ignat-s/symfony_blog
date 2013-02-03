<?php

namespace Acme\BlogBundle\Form\Extension\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class ArrayToStringTransformer implements DataTransformerInterface
{
    private $separator;
    private $filterUniqueValues;

    public function __construct($separator, $filterUniqueValues)
    {
        $this->separator = $separator;
        $this->filterUniqueValues = $filterUniqueValues;
    }

    public function transform($value)
    {
        if (null === $value) {
            return '';
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        return $this->arrayToString($value);
    }

    public function reverseTransform($value)
    {
        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if ('' === $value) {
            return array();
        }

        return $this->stringToArray($value);
    }

    private function arrayToString(array $arrayValue)
    {
        return implode($this->separator, $this->filterArrayValue($arrayValue));
    }

    private function stringToArray($stringValue)
    {
        if (trim($this->separator)) {
            $separator = trim($this->separator);
        } else {
            $separator = $this->separator;
        }
        $arrayValue = explode($separator, $stringValue);
        return $this->filterArrayValue($arrayValue);
    }

    private function filterArrayValue(array $arrayValue)
    {
        if ($this->filterUniqueValues) {
            $arrayValue = array_filter(array_unique($arrayValue));
        }
        $arrayValue = array_map('trim', $arrayValue);
        return $arrayValue;
    }
}
