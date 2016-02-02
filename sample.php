<?php
error_reporting(-1);
ini_set('display_errors', 'On');

require_once('Matryoshka.php');

$str = "sample.limit(100).page(1).in(examples.limit(10).page(2).orderby(popular,desc)),test.length(100)";
$mat = new Matryoshka();

$result = $mat->query($str, Matryoshka::RESULT_ARRAY);
print_r( $result );

$result = $mat->query($str, Matryoshka::RESULT_CLASS);
print_r( $result );
