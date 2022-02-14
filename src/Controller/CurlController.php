<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CurlController extends AbstractController
{
    /**
     * @Route("/curlapitest", name="curl_api_test")
     * @return Response
     */
    public function curlApiTest(): Response
    {
        // $apiKey = 'API_KEY';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://randomuser.me/api/?gender=male&results=10');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // curl_setopt($ch, CURLOPT_HEADER, array('X-API-Key: ' . $apiKey));
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // $json = json_decode(curl_exec($ch));
        
        $json_array = json_decode(curl_exec($ch))->results;

        foreach ($json_array as $result) {

            echo '
                <p>NAME : '. $result->name->first .'</p>
                <p>ADDRESS : '. $result->location->street->name .'</p>
                <p>EMAIL : '. $result->email .'</p>
                <hr>
            ';
        }

        return new Response();

        // dd(
        //     $json,
        //     // $json->results,
        //     $json_array[0]
        // );

    }

    /**
     * @Route("/curlapikeyfilters", name="curl_api_key_filters")
     * @return Response
     */
    public function curlApiKeyFilters(): Response
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://randomuser.me/api/?gender=male&results=10');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        
        $json_array = json_decode(curl_exec($ch))->results;

        $json_filtered = array_filter($json_array, function($key) {

            return $key == '0' || $key == '1';

        }, ARRAY_FILTER_USE_KEY );

        foreach ($json_filtered as $data) {

            echo '
                <p>NAME : '. $data->name->first .'</p>
                <p>ADDRESS : '. $data->location->street->name .'</p>
                <p>EMAIL : '. $data->email .'</p>
                <hr>
            ';
        }

        return new Response();

        // dd(
        //     $json_filtered
        // );

    }

    /**
     * @Route("/curlapidisallow", name="curl_api_disallow")
     * @return Response
     */
    public function curlApiDisallow(): Response
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://randomuser.me/api/?gender=male&results=10');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        
        $json_array = json_decode(curl_exec($ch))->results;

        $disallowed = [];

        for ($i = 0; $i <= 4; $i++) { 

            array_push($disallowed, $i);
            // $disallowed = [0, 1, 2, 3, 4];

        }

        foreach($disallowed as $key){

            unset($json_array[$key]);

        }
        
        foreach($json_array as $data){

            echo '
                <p>NAME : '. $data->name->first .'</p>
                <p>ADDRESS : '. $data->location->street->name .'</p>
                <p>EMAIL : '. $data->email .'</p>
                <hr>
            ';
   
        }

        return new Response();

        // dd(
        //     sizeof($json_array),
        //     $json_array
        // );

    }

    /**
     * @Route("/curlapifilterby", name="curl_api_filterby")
     * @return Response
     */
    public function curlApiFilterBy(): Response
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://randomuser.me/api/?results=10');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        
        $json_array = json_decode(curl_exec($ch))->results;

        $filterBy = 'male'; // or female

        $json_filtered = array_filter($json_array, function ($item) use ($filterBy) {

            return ($item->gender == $filterBy);

        });
        
        foreach($json_filtered as $data){

            echo '
                <p>NAME : '. $data->name->first .'</p>
                <p>ADDRESS : '. $data->location->street->name .'</p>
                <p>EMAIL : '. $data->email .'</p>
                <hr>
            ';
   
        }

        return new Response();

        // dd(
        //     sizeof($json_array),
        //     $json_filtered
        // );

    }

    public function duplicate($val) {

        return $val. " + " .$val;

    }

    public function hashValue($val) {

        return sha1($val);

    }

    /**
     * @Route("/curlapimap", name="curl_api_map")
     * @return Response
     */
    public function curlApiMap(): Response
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://randomuser.me/api/?results=10');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        
        // $json_array = json_decode(curl_exec($ch))->results[0];
        $json_array = json_decode(curl_exec($ch))->results;

        $json_filtered = [];

        // array_push($json_filtered, $json_array->name->last);

        foreach($json_array as $json_item){

            array_push($json_filtered, $json_item->name->last);
   
        }

        // $duplicate = function($a) {
        //     return $a." + ".$a;
        // };

        // $json_transformed = array_map($duplicate, $json_filtered);

        // POUR APPELER UNE FONCTION DÉFINIE DANS LE CONTROLLER :
        // $json_transformed = array_map(array($this, 'duplicate'), $json_filtered);

        $json_transformed = array_map(array($this, 'hashValue'), $json_filtered);
        
        foreach($json_transformed as $data){

            echo '
                <p>LAST NAME HASHED : '. $data .'</p>
                <hr>
            ';
   
        }

        // Il suffirait ensuite de vérifier si l'utilisateur a le droit ou non de voir les données décryptées... if isGranted or not !

        return new Response();

        // dd(
        //     // sizeof($json_array),
        //     // $json_filtered,
        //     $json_transformed
        // );

    }

}
