#!/usr/bin/php
<?php

function listage($path, $recursive = false) {
    $tableau_elements = array();                                                                                        //On déclare le tableau qui contiendra tous les éléments de nos dossiers
    $dir = opendir($path);                                                                                              //On ouvre le dossier
    $regex = "/(\S+).png$/";
    while (($element_dossier = readdir($dir)) !== FALSE) {                                                              //Pour chaque élément du dossier...
        if ($element_dossier != '.' &&                                                                                  //Si l'élément est lui-même un dossier (en excluant les dossiers parent et actuel), on appelle la fonction de listage en modifiant la racine du dossier à ouvrir
            $element_dossier != '..' &&
            is_dir($element_dossier) && $recursive) {
            $tableau_elements = array_merge($tableau_elements, listage($path.'/'.$element_dossier));

        }
        else if ($element_dossier != '.' && $element_dossier != '..' && preg_match($regex, $element_dossier, $names_images)) {    //Sinon, l'élément est un fichier : on l'enregistre dans le tableau
            echo $path . "/" . $element_dossier . PHP_EOL;
            $img = imagecreatefrompng($path . "/" .$element_dossier);
            $tableau_elements[$path . "/" . $element_dossier] = $img;
        }
    }

    closedir($dir);                                                                                                     //On ferme le dossier

    return $tableau_elements;                                                                                           //On retourne le tableau
}
$my_arg = $argv;
$recursive = false;
$dossier_cible = "";
$sprite_name = "sprite.png";
$style_sheet = "style.css";
$name_newImage = "./$sprite_name";
$padding;
$size;
$columns_number;
function add_options() {
    global $recursive, $my_arg, $sprite_name, $style_sheet, $dossier_cible, $padding, $columns_number, $size;
    foreach ($my_arg as $key => $value) {
        switch ($value) {
            case $sprite_name && $my_arg[$key - 1] == "-i":
                continue;
            case $style_sheet && $my_arg[$key - 1] == "-s":
                continue;
                //@TODO finir cela
        }
        if ($key > 0 && $key != count($my_arg) - 1) {
            echo "turn $key\n";
            $regex = "/(\S+)[=]{1}(\S+)/";
            if ($value[0] == "-") {
                //@TODO A voir si ca marche ca car $my_arg[$key + 1][0] != "-"
                if ($my_arg[$key + 1][0] != "-") {
                    switch ($value) {
                        case "-r" || "-recursive":
                            $recursive = true;
                            break;
                        case "-i":
                            $sprite_name = $my_arg[$key + 1] . ".png";
                            continue;
                        case "-s":
                            $style_sheet = $my_arg[$key + 1] . ".css";
                            continue;
                        case "-p":
                            $padding = $my_arg[$key + 1];
                            continue;
                        case "-o":
                            $size = $my_arg[$key + 1];
                            continue;
                        case "-c":
                            $columns_number = $my_arg[$key + 1];
                            continue;
                        default:
                            preg_match($regex, $value,$matches);
                            switch ($matches[1]) {
                                case "-output-style":
                                    $style_sheet = $matches[2] . ".css";
                                    break;
                                case "-output-image":
                                    $sprite_name = $matches[2] . ".png";
                                    break;
                                case "-padding":
                                    $padding = $matches[2];
                                    break;
                                case "-override-size":
                                    $size = $matches[2];
                                    break;
                                case "-columns_number":
                                    $columns_number = $matches[2];
                                    break;
                                default:
                                    echo "erreur option inconnu : " . $value . PHP_EOL;
                                    break;
                            }
                    }
                }
            }

        } else if ($key == count($my_arg) - 1) {
            $dossier_cible = $value;
        }
    }
}
if ($argc < 2) {
    die("vous n'avez pas entré\n");
} else {
    add_options();

    var_dump("recurs = ", $recursive, "sprite name = ", $sprite_name, "style sheet = ", $style_sheet);
    $list_image = listage($dossier_cible, $recursive);
    var_dump($list_image);
    $max_heigth = 0;
    $max_width = 0;
    foreach ($list_image as $key => $img) {
        if ($key != $name_newImage) {
            $imgSize = getimagesize($key);
            $max_width += $imgSize[0];
            if ($max_heigth < $imgSize[1]) {
                echo $imgSize[1] . PHP_EOL;
                $max_heigth = $imgSize[1];
            }
        }
    }
    echo "max width = $max_width, max heigth = $max_heigth\n";
    $newimg = imagecreatetruecolor($max_width, $max_heigth);
    $white = imagecolorallocate($newimg, 255, 255, 255);
    imagefill($newimg, 0, 0, $white);
    $x = 0;
    foreach ($list_image as $key => $img) {
        if ($key != $name_newImage) {

            echo "key = $key, x = $x, size0 = $imgSize[0], size1 = $imgSize[1]\n";
            $imgSize = getimagesize($key);
            imagecopy($newimg, $img, $x, 0, 0, 0, $imgSize[0], $imgSize[1]);
            $x += $imgSize[0];
        }
    }
    //$img1 = imagecreatefrompng("./chrome");

    //$img2 = imagecreatefrompng("./pumba");
    //$img1Size = getimagesize("./img2.png");
    //$img2Size = getimagesize("./Small-mario.png");
    //imagecopymerge($newimg, $img1, $img2X, $img2Y, 0, 0, $img1X, $img1Y, 75);
    //imagecopy($newimg,$img1,0,0,0,0,$img1Size[0],$img1Size[1]);
    //imagecopy($newimg,$img2,0,0,0,0,$img2Size[0],$img2Size[1]);

    echo 'ok2' . PHP_EOL;

    imagepng($newimg, $name_newImage);
}

?>