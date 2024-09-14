<?php
function redirect(string $path = '/') {
    header('Location: ' . $path);
    exit;
}

function dd(mixed $data)
{
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
    die();
}

function pd(mixed $data)
{
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
}

function requestFilterSimple(array $keys, array $data) {
    return array_map(function($key) use ($data) {
        return array_key_exists($key, $data) ? $data[$key] : false;
    }, $keys);
}

function requestFilter(array $keys, array $data) {
    return array_filter($data, function($key) use ($keys) {
        return in_array($key, $keys);
    }, ARRAY_FILTER_USE_KEY);
}