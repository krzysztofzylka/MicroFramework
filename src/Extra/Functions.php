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

/**
 * Better var_dump with exit
 * @param $data
 * @return void
 */
function dumpe(...$data): void
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    exit;
}