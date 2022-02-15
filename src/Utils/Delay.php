<?php

namespace App\Utils;

class Delay 
{

    public function executionDelay(int $seconds)
    {
        $start = microtime(true);

        for ($i = 1; $i <= $seconds; $i ++) {

            time_sleep_until($start + $i);

        }

    }

    public function audit(float $reqTime, float $resTime): void
    {
        // CALCULATION TIME REQUEST
    
        $reqMilliSecond = (int) ($reqTime * 1000);
        $resMilliSecond = (int) ($resTime * 1000);
        $reqMicroSecond = (int) ($reqTime * 1000000);
        $resMicroSecond = (int) ($resTime * 1000000);
 
        $audit = [
            'milliseconds' => [
                'req' => $reqMilliSecond,
                'res' => $resMilliSecond,
                'elapsed' => $resMilliSecond - $reqMilliSecond,
            ],
            'microseconds' => [
                'req' => $reqMicroSecond,
                'res' => $resMicroSecond,
                'elapsed' => $resMicroSecond - $reqMicroSecond,
            ]
        ];
 
        // print_r($audit);

        echo '<pre>';
        var_dump($audit);
        echo '</pre>';

    }

}