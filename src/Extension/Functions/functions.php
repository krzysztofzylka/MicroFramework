<?php

function dumpData(...$data): void
{
    echo '<pre>';
    var_dump(...$data);
    echo '</pre>';
}

function dumpeData(...$data): never
{
    ob_clean();
    echo '<pre>';
    var_dump(...$data);
    echo '</pre>';
    exit;
}