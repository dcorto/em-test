<?php

namespace App\Interfaces;

interface ICache
{
    public function put($json);
    public function get($key);
}