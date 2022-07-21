<?php
$input = file_get_contents("php://input");

if($input == false || $input == "")
{
    echo "bad input";
    return;
}

echo $input;