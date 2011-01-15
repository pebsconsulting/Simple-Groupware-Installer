<?php
/**
 * This script is used to slim down Archive_Tar
 * Read data from Tar_137.php and write to tar.php
 * @see http://pear.php.net/package/Archive_Tar
 */

define("DEBUG", false);
if (!DEBUG) exit("No debug.");

$content = file_get_contents("Tar_137.php");

// we only need functions to extract a tar file
$funcs = array("_writeFooter", "_addList", "_addFile", "_addString", "_writeHeader", "_writeHeaderBlock", "_writeLongHeader",
  "_extractInString", "_openAppend", "_append", "_pathReduction", "create", "add", "createModify", "addModify", "addString",
  "extractInString", "setAttribute", "setIgnoreRegexp", "setIgnoreList", "_openWrite", "_openReadWrite", "_cleanFile", 
  "_writeBlock", "destructor", "_warning",
);

// drop unneeded functions
foreach ($funcs as $func) {
  $seq = preg_quote("// {{{ $func()");
  $seq2 = preg_quote("// }}}");
  $content = preg_replace("!$seq.*?$seq2!msi", "", $content);
}

// remove comments: // ...
$content = preg_replace("|^\s+//.+?\$|msi", "", $content);

// remove comments: /* ... */
$content = preg_replace("|^\s*/\*.+?\*/|msi", "", $content);

$content = str_replace("\r", "", $content);

// reduce empty lines
$content = preg_replace("|\n\n|msi", "\n", $content);

// reduce line breaks
$content = preg_replace("|(function .+?\))\s+\{|msi", "\\1 {", $content);

// reduce empty lines
$content = preg_replace("|\n\n|msi", "\n", $content);

// use tabs instead of spaces
$content = str_replace("    ", "\t", $content);

// remove PEAR requirement
$remove = array(
  "require_once 'PEAR.php';",
  " extends PEAR",
  "\$this->PEAR();",
  "PEAR::loadExtension(\$extname);",
  "\$this->_PEAR();",
  "?>",
);
$content = trim(str_replace($remove, "", $content));

// replace mkdir
$content = str_replace("@mkdir(", "sys_mkdir(", $content);

echo strlen($content);

file_put_contents("tar.php", $content);