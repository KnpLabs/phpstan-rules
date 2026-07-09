<?php

declare(strict_types=1);

$a = time();
$b = \time();
$c = date('Y-m-d');
$d = \date('Y-m-d');
$e = date('Y-m-d', 'now');
$f = \date('Y-m-d', 'now');
$g = date('Y-m-d', 1234567890);
$h = date('Y-m-d', strtotime('2023-01-01'));
