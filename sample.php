<?php
error_reporting(-1);
ini_set('display_errors', 'On');

require_once('Matryoshka.php');

$str = 'AAA.in(BBB.d(100).e(200).in(CCC.f())).b(10).c(290)';

$mat = new Matryoshka();

$result = $mat->query($str, Matryoshka::RESULT_ARRAY);
print_r( $result );

$result = $mat->query($str, Matryoshka::RESULT_CLASS);
print_r( $result );
