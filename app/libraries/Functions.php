<?php

$GLOBALS['ASSETS'] = ['css'=>[], 'js'=>[], 'names'=>[]];

function addAssets($method,$name) {
    if (in_array($name.'_'.$method, $GLOBALS['ASSETS']['names'])) return;
    
    // File system path
    $basePath = $_SERVER['DOCUMENT_ROOT'] . "/test/public/components/$method/$name";
    
    // Web URL path
    $baseUrl = "/test/public/components/$method/$name";

    // check config.json for deps
    $cfg = "$basePath/config.json";

    if (file_exists($cfg)) {
        $conf = json_decode(file_get_contents($cfg), true);
        foreach ($conf['dependencies'] ?? [] as $key=>$values) {
            foreach($values as $value){
                addAssets($key,$value);
            } 
        }
    }

    echo "<!-- File exists: " . (file_exists("$basePath/$name.css") ? 'YES' : 'NO') . " -->\n";
    
    // Check using file system path, but store web URL
    if (file_exists("$basePath/$name.css")) $GLOBALS['ASSETS']['css'][] = "$baseUrl/$name.css";
    if (file_exists("$basePath/$name.js"))  $GLOBALS['ASSETS']['js'][]  = "$baseUrl/$name.js";

    $GLOBALS['ASSETS']['names'][] = $name.'_'.$method;
}

function renderComponent($method,$name, $props = []) {
    
    addAssets($method,$name);
    extract($props);
    
    // Use file system path for including PHP files
    $filePath = $_SERVER['DOCUMENT_ROOT'] . "/test/public/components/$method/$name/$name.php";
    if (file_exists($filePath)) {
        include $filePath;
    } else {
        echo "<!-- Component not found: $filePath -->";
    }
}

function printAssets() {
    foreach ($GLOBALS['ASSETS']['css'] as $href) echo "<link rel='stylesheet' href='$href'>\n";
    foreach ($GLOBALS['ASSETS']['js'] as $src)   echo "<script src='$src' defer></script>\n";
}

function getAssets() { return $GLOBALS['ASSETS']; }
function resetAssets(){ $GLOBALS['ASSETS']=['css'=>[], 'js'=>[], 'names'=>[]]; }