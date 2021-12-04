<?php
//XML FILES
if(file_exists(__MERGADO_TMP_DIR__)) {
$xmlFiles = array_diff(scandir(__MERGADO_XML_DIR__), array('..', '.'));
    $xmlDir = __MERGADO_XML_DIR__ . '0';

    createDir($xmlDir);

    foreach($xmlFiles as $item) {
        if(is_file(__MERGADO_XML_DIR__ . $item)) {
            move_file(__MERGADO_XML_DIR__ . $item, __MERGADO_XML_DIR__ . '0');
        }
    }
}

//TMP FILES
$tmpDir = __MERGADO_TMP_DIR__ . '0';
$importDir = $tmpDir . '/importPrices';
$productDir = $tmpDir . '/productFeed';

createDir($tmpDir);
createDir($importDir);
createDir($productDir);

if(file_exists(__MERGADO_TMP_DIR__ . 'importPrices')) {
    $importFiles = array_diff(scandir(__MERGADO_TMP_DIR__ . 'importPrices'), array('..', '.'));
    foreach($importFiles as $item) {
        if(is_file(__MERGADO_TMP_DIR__ . '/importPrices/' . $item)) {
            move_file(__MERGADO_TMP_DIR__ . '/importPrices/' . $item, $importDir);
        }
    }

    rmdir(__MERGADO_TMP_DIR__ . 'importPrices');
}

if(file_exists(__MERGADO_TMP_DIR__ . 'productFeed')) {
    $productFiles = array_diff(scandir(__MERGADO_TMP_DIR__ . 'productFeed'), array('..', '.'));
    foreach($productFiles as $item) {
        if(is_file(__MERGADO_TMP_DIR__ . '/productFeed/' . $item)) {
            move_file(__MERGADO_TMP_DIR__ . '/productFeed/' . $item, $productDir);
        }
    }

    rmdir(__MERGADO_TMP_DIR__ . 'productFeed');
}

function createDir($dir)
{
    if (!is_dir($dir)) {
        mkdir($dir);
    }
}

function move_file($file, $to) {
    $path_parts = pathinfo($file);
    $newplace   = "$to/{$path_parts['basename']}";
    if(rename($file, $newplace)) {
        return $newplace;
    } else {
        return null;
    }
}