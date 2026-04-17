<?php
//Handle AJAX

include APP_ROOT."/libraries"."/Functions.php";

$name  = $_POST['name']  ?? null;
$props = $_POST['props'] ?? [];

if ($name) {
  resetAssets();
  ob_start();
  renderComponent($name,$props);
  $html = ob_get_clean();
  $assets = getAssets();
  header("Content-Type: application/json");
  echo json_encode(["html"=>$html,"css"=>$assets['css'],"js"=>$assets['js']]);
}
