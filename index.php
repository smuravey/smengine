<?php
// $start = microtime(true);

error_reporting(E_ALL);

set_include_path('application/classes'.PATH_SEPARATOR.'application/controllers'.PATH_SEPARATOR.'application/sql');

spl_autoload_register(function ($class) {
	@include  $class.'.php';
});

$f = new Front();
$f->route();

// $time = microtime(true) - $start;
// printf('Скрипт выполнялся %.5F сек.', $time);