<?php

namespace App\Validator;

use Symfony\Component\Validator\Exception\UnexpectedValueException;

class CustomConstraint
{

    public function validateString($value)
    {

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {

            throw new UnexpectedValueException($value, 'string');

        }

    }

    public function validateArray($array)
    {

        if (!is_array($array)) {

            throw new UnexpectedValueException($array, 'array');

        }

    }

    public function validateInteger($integer)
    {

        if (!is_int($integer)) {

            throw new UnexpectedValueException($integer, 'integer');

        }

    }

    public function validateFloat($float)
    {

        if (!is_float($float)) {

            throw new UnexpectedValueException($float, 'float');

        }

    }

    public function validateBoolean($bool)
    {

        if (!is_bool($bool)) {

            throw new UnexpectedValueException($bool, 'boolean');

        }

    }

    public function validateNumeric($numeric)
    {

        if (!is_numeric($numeric)) {

            throw new UnexpectedValueException($numeric, 'numeric');

        }

    }

    public function validateObject($object)
    {

        if (!is_object($object)) {

            throw new UnexpectedValueException($object, 'object');

        }

    }

}