<?php

header('Content-Type: application/json');

$versions = file_get_contents("versions.json");

if(isset($_GET["latest"])){
	$latest = json_decode($versions);
	$latest = json_encode($latest[0]);
	echo $latest;
} else {
	echo $versions;
}