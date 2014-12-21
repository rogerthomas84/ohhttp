<?php
@ob_start();

if (!function_exists('header_remove')) {
    function header_remove($name) {
        return true;
    }
}

$path = realpath(dirname(__FILE__) . '/../');

spl_autoload_register(function ($name) use($path) {
    $name = implode(DIRECTORY_SEPARATOR, explode('\\', $name)) . '.php';
    $srcPath = $path . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $name;
    if (file_exists($srcPath)) {
        include $srcPath;
    }
    $testsPath = $path . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $name;
    if (file_exists($testsPath)) {
        include $testsPath;
    }
});
