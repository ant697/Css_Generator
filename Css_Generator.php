#!/usr/bin/php
<?php

function listage($path, $recurs = false) {
    //var_dump("------",$recurs);
    $tableau_elements = array();                                                                                        //On déclare le tableau qui contiendra tous les éléments de nos dossiers
    $dir = opendir($path);                                                                                              //On ouvre le dossier
    $regex = "/([\S+\s+]+).png$/";
    while (($element_dossier = readdir($dir)) !== FALSE) {
       // var_dump(is_dir($element_dossier));
        if ($element_dossier != '.' &&                                                                                  //Si l'élément est lui-même un dossier (en excluant les dossiers parent et actuel), on appelle la fonction de listage en modifiant la racine du dossier à ouvrir
            $element_dossier != '..' &&
            is_dir($path."/".$element_dossier) && $recurs) {
            $tableau_elements = array_merge($tableau_elements, listage($path.'/'.$element_dossier));


        } else if ($element_dossier != '.' && $element_dossier != '..' && preg_match($regex, $element_dossier, $names_images)) {    //Sinon, l'élément est un fichier : on l'enregistre dans le tableau
            $img = imagecreatefrompng($path . "/" .$element_dossier);
            $tableau_elements[$path . "/" . $element_dossier] = $img;
            //$tab_name_image[]
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
$padding;
$size;
$columns_number;
function add_options() {
    global $recursive, $my_arg, $sprite_name, $style_sheet, $dossier_cible, $padding, $columns_number, $size;
    $list_option = ["-r", "-recursive", "-i", "-s", "-p", "-o", "-c", "-output-style", "-output-image", "-padding", "-override-size", "-columns_number"];
    foreach ($my_arg as $key => $value) {
        $regex_name_sprite = "/(.+).png/";
        preg_match($regex_name_sprite,$sprite_name,$sprite_no_extension);
        $regex_name_style = "/(.+).css/";
        preg_match($regex_name_style,$style_sheet,$style_no_extension);
        //echo $sprite_no_extension;
        switch ($value) {
            case $sprite_no_extension[1]:
                echo "yiha";
                if ($my_arg[$key - 1] == "-i") {
                    continue 2;
                } else {
                    break;
                }
            case $style_no_extension[1]:
                if ($my_arg[$key - 1] == "-s") {
                    continue 2;
                } else {
                    break;
                }
            case $padding:
                if ($my_arg[$key - 1] == "-p") {
                    continue 2;
                } else {
                    break;
                }
            case $size:
                if ($my_arg[$key - 1] == "-o") {
                    continue 2;
                } else {
                    break;
                }
            case $columns_number:
                if ($my_arg[$key - 1] == "-c") {
                    continue 2;
                } else {
                    break;
                }
        }
        echo "$key => $value" . PHP_EOL;
        if ($key > 0 && $key < count($my_arg) - 1) {
            //echo "turn $key\n";
            $regex = "/(\S+)[=]{1}(\S+)/";
            if ($value[0] == "-") {
                //@TODO A voir si ca marche ca car $my_arg[$key + 1][0] != "-"
               // echo $value . "/////////";
                $value_suivante = "";
                if (preg_match($regex, $my_arg[$key + 1],$next_value_filter)) {
                    $value_suivante = $next_value_filter[1];
                } else {
                    $value_suivante = $my_arg[$key + 1];
                }
                switch ($value) {
                    case '-r':
                    case '-recursive':
                        echo "yahhhhhhhh";
                        $recursive = true;
                        break;
                    case "-i":
                        if (!in_array($value_suivante, $list_option)) {
                            echo "ok in array\n";
                            $sprite_name = $my_arg[$key + 1] . ".png";
                            echo "$sprite_name\n";
                        }
                        break;
                    case "-s":
                        if (!in_array($value_suivante, $list_option)) {
                        $style_sheet = $my_arg[$key + 1] . ".css";
                        }
                        break;
                    case "-p":
                        if (!in_array($value_suivante, $list_option)) {
                            $padding = $my_arg[$key + 1];
                        }
                        break;
                    case "-o":
                        if (!in_array($value_suivante, $list_option)) {
                            $size = $my_arg[$key + 1];
                        }
                        break;
                    case "-c":
                        if (!in_array($value_suivante, $list_option)) {
                            $columns_number = $my_arg[$key + 1];
                        }
                        break;
                    default:
                        echo "---------=============\n";
                        preg_match($regex, $value,$matches);
                        echo ".........".$matches[1];
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
                        break;
                }

            } else {
                echo "erreur argument inconnu : $value\n";
            }

        } else if ($key == count($my_arg) - 1) {
            if ($value[0] != "-" && is_dir($value)) {
                $dossier_cible = $value;
            } else {
                $dossier_cible = prompt_no_dir();
            }


        }
    }
}
function prompt_no_dir() {
    echo "Vous n'avez pas entré de nom de dossier.\nLancez dans le dossier actuel ? Y/N :\n";
    $command_user = fgets(STDIN);
    if ($command_user == "Y\n" || $command_user == "y\n") {
        return ".";
    } else if ($command_user == "N\n" || $command_user == "n\n") {
        die("Fin du script\n");
    } else {
        prompt_no_dir();
    }
}
if ($argc < 2) {
    die("vous n'avez pas entré\n");
} else {


}
function create_sprite($crush_sprite = false) {
    $str = "";
    global $recursive, $dossier_cible, $sprite_name, $style_sheet;
    add_options();
    //@TODO finir check si ecraser ou non sprite
    //if (in_array(listage("."),)
   // var_dump("recurs = ", $recursive, "sprite name = ", $sprite_name, "style sheet = ", $style_sheet);
    $list_image = listage($dossier_cible, $recursive);
    //var_dump($list_image);
    $max_heigth = 0;
    $max_width = 0;
    foreach ($list_image as $key => $img) {
        if ($key != $sprite_name) {
            $imgSize = getimagesize($key);
            $max_width += $imgSize[0];
            if ($max_heigth < $imgSize[1]) {
                $max_heigth = $imgSize[1];
            }
        }
    }
    $newimg = imagecreatetruecolor($max_width, $max_heigth);
    $white = imagecolorallocate($newimg, 255, 255, 255);
    $white = imagecolortransparent($newimg, $white);
    imagefill($newimg, 0, 0, $white);
    $x = 0;
    $file_css = fopen($style_sheet, "w");
    foreach ($list_image as $key => $img) {
        if ($key != $sprite_name) {

            echo "key = $key, x = $x, size0 = $imgSize[0], size1 = $imgSize[1]\n";
            $imgSize = getimagesize($key);
            imagecopy($newimg, $img, $x, 0, 0, 0, $imgSize[0], $imgSize[1]);
            $regex_name_image = "/(\/?.+\/)*(.+?)(\..+)?.png$/";
            preg_match($regex_name_image, $key, $matches_name);
            //var_dump("Here", $matches_name);
            $str .= ".$matches_name[2] {
   width: $imgSize[0]px; height: $imgSize[1]px;
   background: url('$sprite_name') -".$x."px -0px;
}\n";
            $x += $imgSize[0];
        }
    }

    fwrite($file_css,$str);

    imagepng($newimg, $sprite_name);
}
var_dump($argv);
create_sprite();