<?php
$server_url = "https://marvideo.fr/versions/";

if (isset($_POST["name"])) {
    $zip = $_FILES['zip'];
    $target_dir = "download/";
    $file_name = $_POST["version"] . "-" . $_POST["type"] . "." . pathinfo($zip['name'], PATHINFO_EXTENSION);
    $target_file = $target_dir . basename($file_name);
    $uploadOk = 1;
    $FileType = pathinfo($zip['name'], PATHINFO_EXTENSION);

    if (
        $FileType != "zip"
    ) {
        echo "Sorry, only ZIP files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        $upload = move_uploaded_file($zip["tmp_name"], $target_file);
    }

    $file = fopen("version.txt", "w");
    $now = date("Y-m-d");
    $write = "version : " . $_POST["version"] . "-" . $_POST["type"];
    $write .= "\nname : " . $_POST["name"];
    $write .= "\ndate : " . $now;
    $write .= "\ncategory : " . $_POST["category"];
    $write .= "\nlink : " . $server_url . "download/" . $_POST["version"] . "-" . $_POST["type"] . ".zip";
    fwrite($file, $write);

    $zip = new ZipArchive;
    $res = $zip->open($target_file);
    $zip->addFile("version.txt", "version.txt");
    $zip->close();
    fclose($file);
    unlink("version.txt");

    $data = file_get_contents("versions.json");
    $new_data = '{"version": "' . $_POST["version"] . '",';
    $new_data .= '"date": "' . $now . '",';
    $new_data .= '"type": "' . $_POST["type"] . '",';
    $new_data .= '"category": "' . $_POST["category"] . '",';
    $new_data .= '"name": "' . $_POST["name"] . '",';
    $new_data .= '"link": "' . $server_url . "download/" . $_POST["version"] . "-" . $_POST["type"] . '.zip"},';
    $data = substr($data, 1);
    $data = "[" . $new_data . $data;

    file_put_contents("versions.json", $data);
}


$ch = curl_init($server_url . "?latest");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
$data = json_decode(curl_exec($ch));

$latest_version = $data->version;
$latest_type = $data->type;
?>
<link rel="stylesheet" href="style.css">
<form action="ui.php" method="post" enctype="multipart/form-data">
    <label for="name">Enter the name : </label>
    <input type="text" name="name" id="name" required />

    <label for="version">Veuillez Choisir une version : (Latest Version : <?= $latest_version ?>)</label>
    <input type="text" name="version" id="version" required />

    <label for="type">Veuillez choisir un type : (Latest Type : <?= $latest_type ?>)</label>
    <select name="type" id="type">
        <option value="release">Release</option>
        <option value="beta">Beta</option>
        <option value="alpha">Alpha</option>
    </select>

    <label for="category">Veuillez choisir une category</label>
    <select name="category" id="category">
        <option value="major">Major</option>
        <option value="patch">Patch</option>
    </select>

    <label for="canal">Veuillez choisir un canal</label>
    <select name="canal" id="canal">
        <option value="lts">LTS</option>
        <option value="dev">DEV</option>
    </select>

    <label for="zip">Veuillez envoyer votre fichier zip</label>
    <input type="file" id="zip" name="zip" accept=".zip" />

    <input type="submit" value="Creer la versions">
</form>

<?php

function object_to_array($data) {

    if (is_object($data)) {
        $data = get_object_vars($data);
    }

    if (is_array($data)) {
        return array_map(__FUNCTION__, $data);
    }
    else {
        return $data;
    }
}

?>