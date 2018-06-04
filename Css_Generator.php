#!/usr/bin/php
<?php
function prompt(array $possibilite, $first = true) {
    echo $possibilite[0];
    foreach ($possibilite as $key => $option) {
        if ($key > 0 && $first) {
            $possibilite[$key] = $option."\n";
        }
    }
    $command = fgets(STDIN);
    if (in_array($command, $possibilite)) {
        echo "commande selected:\n";
        var_dump($command);
        return $command;
    } else {
        prompt($possibilite, false);
    }
}
$accept_jpg = false;
$refused_jpg = false;
$tab_img_validate = [];
$tab_img_refused = [];
//@TODO finir tableau regex

$regex_extension = ["css" => "/\/+([\S+\s+]+).css$/", "png" => "/\/+([\S+\s+]+).png$/", "jpg" => "/\/+([\S+\s+]+).jp[e]?g$/", "gif" => "/\/+([\S+\s+]+).gif$/"];
function listage($path, $recurs = false, $check_all_format = true) {
    //var_dump("------",$recurs);
    global $sprite_name, $accept_jpg, $tab_img_validate, $tab_img_refused, $refused_jpg, $regex_extension;
    $tableau_elements = array();                                                                                        //On déclare le tableau qui contiendra tous les éléments de nos dossiers
    $dir = opendir($path);                                                                                              //On ouvre le dossier

    //preg_match($regex_name_image, $sprite_name, $matches_name_sprite);
    //for ($i=1; array_key_exists("./$matches_name_sprite[2]($i).png",$dossier_courant); $i++) {}
    //@ TODO voir pour renommer avec la regex : (.+)\([0-9]?\)?\.png
    while (($element_dossier = readdir($dir)) !== FALSE) {
       // var_dump(is_dir($element_dossier));
        if ($element_dossier != '.' &&                                                                                  //Si l'élément est lui-même un dossier (en excluant les dossiers parent et actuel), on appelle la fonction de listage en modifiant la racine du dossier à ouvrir
            $element_dossier != '..' &&
            $element_dossier[0] != "." &&
            is_dir($path."/".$element_dossier) && $recurs) {

            $tableau_elements = array_merge($tableau_elements, listage($path.'/'.$element_dossier, $recurs));
        } else if ($element_dossier != '.' && $element_dossier != '..') {                                                //Sinon, l'élément est un fichier : on l'enregistre dans le tableau

            // if ()
            $text_jpg = "Le dossier contient une image en \".jpg\" :\nAccepter pour toutes : 1\nAccepter juste pour $element_dossier : 2\nRefuser juste pour $element_dossier : 3\nRefuser pour toutes : 4\n";

            if (preg_match($regex_extension["png"], $path."/".$element_dossier, $names_images)) {
                $img = imagecreatefrompng($path . "/" .$element_dossier);
                $tableau_elements[$path . "/" . $element_dossier] = $img;
            } else if (preg_match($regex_extension["jpg"], $path."/".$element_dossier, $names_images) && $check_all_format) {
                if (!$accept_jpg &&
                    !in_array($element_dossier, $tab_img_validate) &&
                    !in_array($element_dossier, $tab_img_refused) &&
                    !$refused_jpg) {
                    switch (prompt([$text_jpg, "1", "2", "3", "4"])) {
                        case "1\n":
                            $accept_jpg = true;
                            $img = imagecreatefromjpeg("$path/$element_dossier");
                            $tableau_elements[$path."/$element_dossier"] = $img;
                            break;
                        case "2\n":
                            $tab_img_validate[] = $element_dossier;
                            $img = imagecreatefromjpeg("$path/$element_dossier");
                            $tableau_elements[$path."/$element_dossier"] = $img;
                            break;
                        case "3\n":
                            $tab_img_refused[] = $element_dossier;
                            break;
                        case "4\n":
                            $refused_jpg = true;
                            break;
                    }
                } else {
                    $img = imagecreatefromjpeg("$path/$element_dossier");
                    $tableau_elements[$path."/$element_dossier"] = $img;
                }
            } else if (preg_match($regex_extension["css"], $element_dossier, $names_images) && $path == ".") {
                $tableau_elements[$path."/$element_dossier"] = "";
            }
            //$tab_name_image[]
        }
    }

    closedir($dir);                                                                                                     //On ferme le dossier
    var_dump("yeahhhhhhhhhh", $tableau_elements);
    return $tableau_elements;                                                                                           //On retourne le tableau
}
$my_arg = $argv;
$recursive = false;
$dossier_cible = "";
$sprite_name = "sprite.png";
$style_sheet = "style.css";
$padding = 0;
$size = false;
$columns_number = false;
function zap_option($value, $previous_value) {
    global $sprite_name, $style_sheet, $padding, $size, $columns_number, $regex_extension;
    preg_match($regex_extension["png"], $sprite_name, $sprite_no_extension);
    preg_match($regex_extension["css"], $style_sheet, $style_no_extension);
    switch ($value) {
        case $sprite_no_extension[1]:
            if ($previous_value == "-i") {
                return true;
            } else {
                return false;
            }
        case $style_no_extension[1]:
            if ($previous_value == "-s") {
                return true;
            } else {
                return false;
            }
        case $padding:
            if ($previous_value == "-p") {
                return true;
            } else {
                return false;
            }
        case $size:
            if ($previous_value == "-o") {
                return true;
            } else {
                return false;
            }
        case $columns_number:
            if ($previous_value == "-c") {
                return true;
            } else {
                return false;
            }
    }
}
function check_dir($value) {
    global $dossier_cible;
    if ($value[0] != "-" && is_dir($value)) {
        $dossier_cible = $value;
    } else {
        $dossier_cible = prompt_no_dir();
    }
}
function add_options() {
    global $recursive, $my_arg, $sprite_name, $style_sheet, $dossier_cible, $padding, $columns_number, $size, $regex_extension;
    $list_option = ["-r", "-recursive", "-i", "-s", "-p", "-o", "-c", "-output-style", "-output-image", "-padding", "-override-size", "-columns_number"];
    foreach ($my_arg as $key => $value) {
        if (zap_option($value, $my_arg[$key - 1])) {
            continue;
        }
        echo "$key => $value" . PHP_EOL;
        if ($key > 0 && $key < count($my_arg) - 1 || $key > 0 && !is_dir($my_arg[count($my_arg) - 1])) {
            $regex = "/(\S+)[=]{1}(\S+)/";
            if (preg_match($regex, $my_arg[$key + 1],$next_value_filter)) {
                $value_suivante = $next_value_filter[1];
            } else {
                $value_suivante = $my_arg[$key + 1];
            }
            switch ($value) {
                case '-r':
                case '-recursive':
                    $recursive = true;
                    break;
                case "-i":
                    if (!in_array($value_suivante, $list_option)) {
                        $sprite_name = $value_suivante . ".png";
                    }
                    break;
                case "-s":
                    if (!in_array($value_suivante, $list_option)) {
                    $style_sheet = $value_suivante . ".css";
                    }
                    break;
                case "-p":
                    if (!in_array($value_suivante, $list_option)) {
                        $padding = intval($value_suivante);
                    }
                    break;
                case "-o":
                    if (!in_array($value_suivante, $list_option)) {
                        $size = intval($value_suivante);
                    }
                    break;
                case "-c":
                    if (!in_array($value_suivante, $list_option)) {
                        $columns_number = intval($value_suivante);
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
        } else if ($key == count($my_arg) - 1) {
            check_dir($value);
        }
    }
}
function prompt_no_dir() {
    $txt = "Vous n'avez pas entré de nom de dossier.\nLancez dans le dossier actuel ? Y/N :\n";
    switch (prompt([$txt,"Y","y","n","N"])) {
        case "Y\n":
        case "y\n":
            return ".";
        case "N\n":
        case "n\n":
            die("Fin du script\n");
    }
}
if ($argc < 2) {
    echo file_get_contents("manuel-Css-Generator.txt");
    die();
} else {


}
function create_image($heigth, $width) {
    echo "h = $heigth, w = $width\n";
    $newimg = imagecreatetruecolor($heigth, $width);
    $transparency = imagecolorallocate($newimg, 255, 255, 255);
    $transparency = imagecolortransparent($newimg, $transparency);
    imagefill($newimg, 0, 0, $transparency);
    var_dump("==========", $newimg);
    return $newimg;
}
function calcul_Max_Size($tab_images, $tab_ignore) {
    global $padding;
    $max_heigth = 0;
    $max_width = 0;
    $w = 0;
    $h = 0;
    foreach ($tab_images as $key => $img) {
        if (!in_array($key,$tab_ignore)) {
            list($w,$h) = getimagesize($key);

            $max_width += $w + $padding;
            if ($max_heigth < $h) {
                $max_heigth = $h;
            }
        }
    }
    echo "max h = $max_heigth max w = $max_width\n";
    return [$max_heigth, $max_width];
}
function rename_sprite($sprite_name, $dossier_actuel) {
    $regex_name_image = "/(\/?.+\/)*(.+?)(\..+)?.png$/";
    preg_match($regex_name_image, $sprite_name, $matches_name_sprite);
    $name_sprite = $matches_name_sprite[2];
    $newName = $sprite_name;
    $tab_ignore = [];
    for ($i=1; array_key_exists("./".$newName,$dossier_actuel); $i++) {
        $newName = $name_sprite."($i).png";
        echo "newname = $newName\n";
    }

    return ["name" => $newName, "tab_ignore" => $tab_ignore];
}
function rename_css($css_name, $dossier_actuel) {
    $regex_name_image = "/(\/?.+\/)*(.+?)(\..+)?.css$/";
    preg_match($regex_name_image, $css_name, $matches_name_css);
    $name_css = $matches_name_css[2];
    $newName = $name_css.".css";
    $tab_ignore = [];
    var_dump($dossier_actuel);
    for ($i=1; array_key_exists("./".$newName,$dossier_actuel); $i++) {
        $tab_ignore[] = "./".$newName;
        $newName = $name_css."($i).css";
    }
    echo "le putain de nouveau nom : $newName\n";
    return $newName;
}
function add_image() {

}
function prompt_already_exist($tab_courant) {
    global $sprite_name, $style_sheet, $tab_ignore;


    $text = "Le sprite $sprite_name est déja présent.\nÉcraser : E\nRenommer : R\nQuitter : Q\n";
    switch (prompt([$text, "e","E","r","R","q","Q"])) {
        case "e\n":
        case "E\n":
                unlink("./" . $sprite_name);
                unlink("./$style_sheet");
            echo "Fichiers écrasés : $sprite_name, $style_sheet\n";
            $tab_ignore = [];
            break;
        case "r\n":
        case "R\n":
            $new_sprite = rename_sprite($sprite_name, $tab_courant);
            $style_sheet = rename_css($style_sheet, $tab_courant);
            $tab_ignore = $new_sprite["tab_ignore"];
            $sprite_name = $new_sprite["name"];
            echo "Fichiers renommés\nNouveau Sprite : $sprite_name\nNouveau Style : $style_sheet\n";
            break;
        case "q\n":
        case "Q\n":
            die("Good Bye ;)\n");
    }
}
function check_already_exist($name, $tab_courant) {
    if (array_key_exists("./".$name, $tab_courant)) {
        prompt_already_exist($tab_courant);

    } else {
        return true;
    }
}
$tab_ignore = [];
$regex_name_image = "/(\/?.+\/)*(.+?)(\..+)?.png$/";
function generate_txt_css($name, $w, $h, $pos_x) {
    global $regex_extension, $sprite_name;
    $str = "";
    $tab_name = explode("/",$name);
    $name = $tab_name[count($tab_name) - 1];
    $name = "/" . $name;
    foreach ($regex_extension as $key => $value) {
        var_dump($key, preg_match($value,$name,$name_no_ext));
        if (preg_match($value,$name,$name_no_ext)) {
            break;
        }
    }
        $str = ".$name_no_ext[1] {
   width: " . $w . "px; height: ".$h."px;
   background: url('$sprite_name') -". $pos_x ."px -0px;
}\n";
    return $str;
}
function concatenate_images($list_image, &$image_to_add, &$text_css) {
    global $tab_ignore, $regex_extension, $sprite_name, $padding;
    $text_css = "";
    $x = 0;
    $key_max = $list_image[count($list_image) - 1];
    foreach ($list_image as $key => $img) {
        if (!in_array($key,$tab_ignore)) {

            list($w,$h) = getimagesize($key);

            echo "key = $key, x = $x, width = $w, height = $h padding = $padding\n";
            if ($key == 0) {
                imagecopy($image_to_add, $img, $x+$padding, 0, 0, 0, $w, $h);
                $text_css .= generate_txt_css($key, $w+$padding, $h, $x+$padding);
            } else if ($key == $key_max) {

            } else {

            }

//            if ($key > 0 && $key < $key_max) {
//                imagecopy($image_to_add, $img, $x+$padding, 0, 0, 0, $w, $h);
//                $text_css .= generate_txt_css($key, $w+$padding, $h, $x+$padding);
//            } else if ($key == $key_max) {
//                imagecopy($image_to_add, $img, $x+$padding, 0, 0, 0, $w, $h);
//                $text_css .= generate_txt_css($key, $w, $h, $x+$padding);
//            } else {
//                imagecopy($image_to_add, $img, $x, 0, 0, 0, $w, $h);
//                $text_css .= generate_txt_css($key, $w+$padding, $h, $x + $padding);
//            }

            //var_dump("Here", $matches_name);

            $x += $w + $padding;
        }
    }

}
function create_sprite($crush_sprite = false) {
    $str = "";
    global $recursive, $dossier_cible, $sprite_name, $style_sheet, $tab_ignore, $padding, $regex_extension, $columns_number, $size;

    add_options();
    //@TODO finir check si ecraser ou non sprite
    $dossier_courant = listage(".", false, false);
    var_dump($dossier_courant, $sprite_name);
    check_already_exist($sprite_name,$dossier_courant);
    echo "recurs = , $recursive, sprite name = , $sprite_name, style sheet = , $style_sheet, padding = $padding, column = $columns_number size = $size".PHP_EOL;
    $list_image = listage($dossier_cible, $recursive);
    $size = calcul_Max_Size($list_image, $tab_ignore);
    $newimg = create_image($size[1], $size[0]);
    var_dump("-----------", $newimg, "----------");
    $file_css = fopen($style_sheet, "w");
    echo "-------list image --------\n";
    var_dump($list_image);

    concatenate_images($list_image, $newimg, $str);

    fwrite($file_css,$str);
    echo "this name : $sprite_name\n";
    var_dump($newimg);
    imagepng($newimg, $sprite_name);
}
//var_dump($argv);
create_sprite();
//var_dump(rename_sprite("dede"));

