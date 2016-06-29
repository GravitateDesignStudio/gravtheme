<?php

define( 'ACF_LITE' , true ); // true hides acf from the admin panel. false shows it.


if(function_exists("register_field_group")){

    // grab all files from acf-fields directory and include them, add file names in excludes if you don't want it added
    $excludes = array();

    $dir = get_stylesheet_directory()."/library/acf-fields/";
    foreach(glob($dir.'*') as $file) {
        if(!in_array(basename($file), $excludes)){
            include_once($file);
        }
    }

}
