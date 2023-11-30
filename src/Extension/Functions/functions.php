<?php

/**
 * Dump data
 * @param ...$data
 * @return void
 */
function dumpData(...$data): void
{
    echo '<pre>';
    var_dump(...$data);
    echo '</pre>';
}

/**
 * Dump data with exit
 * @param ...$data
 * @return never
 */
function dumpeData(...$data): never
{
    ob_clean();
    echo '<pre>';
    var_dump(...$data);
    echo '</pre>';
    exit;
}