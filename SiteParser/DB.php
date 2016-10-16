<?php

$dataBase = @mysql_connect("localhost", "root", "")
or die("Could not connect: " . mysql_error());
mysql_select_db("*****");
echo $charset = mysql_client_encoding($dataBase);
@mysql_set_charset('utf8', $dataBase);
?>

