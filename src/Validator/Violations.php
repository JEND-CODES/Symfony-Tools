<?php

namespace App\Validator;

class Violations
{

    public function checkIfSymbols($string)
    {
         // Vérifie qu'une chaîne de caractères ne contient pas de symboles spéciaux
         if (!preg_match('/^[a-zA-Z0-9]+$/', $string, $matches)) {

            throw new \Exception('Prohibited symbols !');

        }

    }

    public function checkTypeLower($string)
    {
         // Si un texte ne contient que des caractères en minuscules
         if (ctype_lower($string)) {

            // Erreur !
            throw new \Exception('Lowercase error !');

        }

    }

}