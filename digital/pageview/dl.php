<?php

/********************************************************************
 * Android用圧縮ファイルダウンロードプログラム
 * 予め管理画面でconfig以下に作成されたAndroid用カタログデータ(zip)を
 * pageview側からダウンロードする。
 *
 ********************************************************************/




// ダウンロードデータ(zip形式)を返すプログラム
define('DL_FILE_DIR', dirname(__FILE__));




$list = split( "/", DL_FILE_DIR );
$catalogId = $list[ count( $list ) - 2 ];
$path = "../config/dl_".$catalogId.".zip";
$file_name = "dl_".$catalogId.".zip";

$buff = '';
$length = 0;
$buff_size = 8192;

if (true === is_file($path)) {
    $length = filesize($path);
} else {
    exit;
}


// ヘッダー出力
header('Accept-Ranges: none');
header('Content-Disposition: inline; filename=' . $file_name);
header('Content-Transfer-Encoding: binary');
header('Content-Length: '. $length);
header('Content-Type: application/zip;name=' . $file_name);
header('Content-Type: application/download');

// ファイル出力
$fp = fopen($path, 'rb');
if (false !== $fp) {
    // 一定バイト数を読込んで出力
    while (!feof($fp)) {
        $buff = fread($fp, $buff_size);
        echo $buff;
    }
    fclose($fp);
}
exit;


