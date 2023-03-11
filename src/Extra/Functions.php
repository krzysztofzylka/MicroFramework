<?php

/**
 * Better var_dump
 * @param $data
 * @return void
 */
function dump(...$data): void
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}