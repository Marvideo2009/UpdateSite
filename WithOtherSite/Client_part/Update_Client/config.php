<?php
// The server url example : https://marvideo.fr/
$server_url = "https://marvideo.fr/";

// Le canal example "lts" or "dev"
$canal = "lts";
#get_current_version();
function get_current_version() {
	$data = fopen("version.txt", 'r');
	$ligne = fgets($data);
	$ligne = explode(" : ", $ligne);
	$ligne = preg_replace('/\s\s+/', ' ', $ligne);
	fclose($data);
	return $ligne[1];
}