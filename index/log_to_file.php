<?php
$message = $_POST['message'];

file_put_contents('index.log', $message . PHP_EOL, FILE_APPEND);
?>
