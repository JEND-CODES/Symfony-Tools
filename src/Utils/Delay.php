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

}