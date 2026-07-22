<?php

declare(strict_types=1);

$a = new DateTime();
$b = new DateTime('now');
$c = new DateTimeImmutable();
$d = new DateTimeImmutable('now');
$e = new DateTime('2023-01-01');
$f = new DateTimeImmutable('yesterday');
$g = new DateTimeImmutable('tomorrow');
$h = new DateTime('+1 day');
$i = new DateTimeImmutable('next Monday');
$j = new DateTimeImmutable('-2 weeks');
$k = new DateTimeImmutable('2 days ago');
$l = new DateTimeImmutable('last day of this month');
$m = new DateTimeImmutable('today');
$n = new DateTimeImmutable('friday');
$o = new DateTimeImmutable('in 3 weeks');
$p = new DateTimeImmutable('noon');
$q = new DateTimeImmutable('+0 seconds');
$r = new DateTimeImmutable('0 days');
$s = new DateTimeImmutable('2023-12-31 23:59:59');
$t = new DateTimeImmutable('2023-01-15T12:00:00+00:00');
$u = new DateTimeImmutable('12:00:00');
$dateString = '2023-01-01';
$someVar = '2024-06-15';
$args = ['2023-01-01'];
$v = new DateTimeImmutable($dateString);
$w = new DateTime($someVar);
$x = new DateTimeImmutable(...$args);
$y = new DateTimeImmutable('yesterday noon');
$z = new DateTimeImmutable('today midnight');
$aa = new DateTimeImmutable('tomorrow 12:00');
$bb = new DateTimeImmutable('monday 14:00:00');
$cc = new DateTimeImmutable('TOMORROW');
$dd = new \stdClass();
