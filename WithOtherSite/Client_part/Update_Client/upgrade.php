<?php

include('config.php');
echo get_update();

function get_update()
{
    $i = 0;
    $updates = [];
    foreach (get_versions()[0] as $key => $value) {
        if ($value->version == get_current_version()) {
            break;
        }
        array_push($updates, $value->version);
        $i++;
    }

    $current_version = get_current_version();

    if($updates) {
        echo download_update($updates[0]);
    }

    return 'You are in version ' . $current_version . ' and the last version is ' . $updates[0];
}

function get_versions()
{

    global $server_url;

    try {
        $ch = curl_init($server_url . "versions/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = json_decode(curl_exec($ch));

        if (!curl_errno($ch)) {
            switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
                case 200:
                    curl_close($ch);
                    return [$data, true];
                default:
                    curl_close($ch);
                    return ['Unable to check the versions', false];
            }
        } else {
            curl_close($ch);
            return ['Unable to check the versions', false];
        }
    } catch (Exception $e) {
        return ['Unable to check the versions', false];
    }
}

function download_update($version) {
    $i = 0;
    foreach (get_versions()[0] as $key => $value) {
        if ($value->version == $version) {
            $data = file_get_contents($value->link);
            $file = fopen("update.zip", "w");
            fwrite($file, $data);
            fclose($file);

            $zip = new ZipArchive;
            $res = $zip->open("update.zip");
            if($res === true) {
                $zip->extractTo(".");
                $zip->close();
                unlink('update.zip');
            }

            break;
        }
        $i++;
    }
}