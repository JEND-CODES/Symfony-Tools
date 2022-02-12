<?php

namespace App\Utils;

class Dumps 
{

    // Dump de résultats, dans un tableau, selon un critère de recherche
    public function dumpByCriteriaInArray(array $itemsQueried, string $criteria) {

        $array = [];

        $methodName = 'get'.ucfirst($criteria); 

        foreach ($itemsQueried as $item) {

            array_push($array, $item->$methodName());

        }

        dd($array);

    }

    // Vérifie dans un tableau de résultats si les longueurs d'éléments textes sont supérieures à un nombre
    public function dumpGreaterThanInArray(array $itemsQueried, string $criteria, int $number) {

        $array = [];

        $methodName = 'get'.ucfirst($criteria); 

        foreach ($itemsQueried as $item) {

            array_push($array, $item->$methodName());

        }

        $array = array_map(function($val) use ($number) {

            if (strlen($val.'') > $number) {

                return $val.'';

            } 

        }, $array);

        // dd($array);

        // Dump sans les valeurs nulles
        dd(array_filter($array));

    }

    // Vérifie dans un tableau de résultats si les longueurs d'éléments textes sont inférieures à un nombre
    public function dumpLessThanInArray(array $itemsQueried, string $criteria, int $number) {

        $array = [];

        $methodName = 'get'.ucfirst($criteria); 

        foreach ($itemsQueried as $item) {

            array_push($array, $item->$methodName());

        }

        $array = array_map(function($val) use ($number) {

            if (strlen($val.'') < $number) {

                return $val.'';

            } 

        }, $array);

        // dd($array);

        // Dump sans les valeurs nulles
        dd(array_filter($array));

    }

    // Vérifie si la longueur d'un texte est inférieure à un nombre
    public function dumpLessThan($itemsQueried, string $criteria, int $number) {

        $methodName = 'get'.ucfirst($criteria);

        $string = $itemsQueried->$methodName();

        if (strlen($string) < $number) {

            dd($string);

        }

    }

    // Vérifie si la longueur d'un texte est supérieure à un nombre
    public function dumpGreaterThan($itemsQueried, string $criteria, int $number) {

        $methodName = 'get'.ucfirst($criteria);

        $string = $itemsQueried->$methodName();

        if (strlen($string) > $number) {

            dd($string);

        }

    }

    // Vérifie si un texte contient des caractères spéciaux
    public function dumpIfSpecialCharacters($itemsQueried, string $criteria) {

        $methodName = 'get'.ucfirst($criteria);

        $string = $itemsQueried->$methodName();

        if (preg_match('/[^a-zA-Z]+/', $string)) {

            dd($string);

        }

    }

    // Vérifie si un tableau de résultats contient des symboles ou des nombres
    public function dumpIfSpecialCharactersInArray(array $itemsQueried, string $criteria) {

        $array = [];

        $methodName = 'get'.ucfirst($criteria); 

        foreach ($itemsQueried as $item) {

            array_push($array, $item->$methodName());

        }

        $errors = [];

        foreach ($array as $string) {

            if (preg_match('/[^a-zA-Z]+/', $string)) {

                array_push($errors, $string);
    
            }

        }

        dd($errors);

    }

    // Dump d'informations d'entités reliées
    public function dumpInRelationMethod($itemsQueried, string $methodName, string $relatedMethod) {

        $method = 'get'.ucfirst($methodName);

        $related = 'get'.ucfirst($relatedMethod);

        $string = $itemsQueried->$method();

        $result = $string->$related();

        dd($result);

    }

    // Dump d'informations d'entités reliées depuis un tableau
    public function dumpInRelationMethodInArray(array $itemsQueried, string $methodName, string $relatedMethod) {

        $array = [];

        $method = 'get'.ucfirst($methodName);

        $related = 'get'.ucfirst($relatedMethod);

        foreach ($itemsQueried as $item) {

            array_push($array, $item->$method()->$related());

        }

        dd($array);

    }

}