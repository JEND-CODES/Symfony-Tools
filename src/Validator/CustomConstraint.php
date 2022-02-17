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

}