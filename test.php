<?php 
date_default_timezone_set('Asia/Shanghai');
echo date('Y-m-d H:i:s')."<br/>";
?>

<?php
require_once(dirname(__FILE__) . '/app.php');

$template_path = dirname(__FILE__) . '/include/data/template/template_write.doc';
echo $template_path."<br/>";
// starting word
$word = new COM("word.application") or die("Unable to instantiate Word");
echo "Loaded Word, version {$word->Version}\n";

//bring it to front
$word->Visible = 1;

//open an empty document
$word->Documents->Add();

//do some weird stuff
$word->Selection->TypeText("This is a test...");
$word->Documents[1]->SaveAs("Useless test.doc");

//closing word
$word->Quit();

//free the object
$word = null;

echo "<br/>"; 
?>