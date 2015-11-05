<?php
session_start();

/*
 切り抜き処理PHP
 2009/12/24 修正
*/

$dir="";
if(isset($_POST["dir"])){
	$dir=$_POST["dir"];
}

if($dir==""){
	exit;
}else{
	//妥当性チェック
	$dir = basename($dir);
	$dir = ereg_replace("\.","", $dir); //.削除
	if($dir == ""){
		exit;
	}
}


//ユーザごとのパス設定
//$userPath = "cropped/" . htmlentities(session_id());
$userPath = "cropped/" . $dir;

//ユーザごとのディレクトリを含むURL
$selfURL = "http://" . $_SERVER["HTTP_HOST"] . str_replace("/cropManager.php","",$_SERVER["PHP_SELF"]) . "/" . $userPath;

//サムネイル作成時の縦横の圧縮率（パーセント表示）
$widthCompressibility = 20;
$heightCompressibility = 20;

//処理区分取得
$divide = "";
if(isset($_POST["divide"]))
{
	$divide = $_POST["divide"];
}

if(strcmp($divide, "save") === 0 || strcmp($divide, "ImageMagick") === 0)
{
	//まず、今何枚切り抜いているかを数える。
	$cnt = 0;
	if(file_exists($userPath . "/image"))
	{
		if($dir = opendir($userPath . "/image"))
		{
			while(($file = readdir($dir)) !== false)
			{
				if(strcmp($file,".") != 0 && strcmp($file,"..") != 0 && ! is_dir($file))
				{
					$cnt++;
				}
			}
			closedir($dir);
		}
	}
	
	//40枚切り抜いてたらメッセージを返す。
	if($cnt >= 40)
	{
		echo "lists=crop_count_over";
		exit;
	}
	
	$pageNum = 0;
	//ページ番号受取
	if(isset($_POST["page_num"]))
	{
		$pageNum = $_POST["page_num"];
	}
	
	//ユーザごとにフォルダを作成
	if(! file_exists($userPath))
	{
		//フォルダを作る。
		mkdir($userPath,0777);
		chmod($userPath,0777);
		
		//通常サイズとサムネイルのフォルダ
		mkdir($userPath . "/image",0775);
		chmod($userPath . "/image",0775);
		mkdir($userPath . "/thumbnail",0775);
		chmod($userPath . "/thumbnail",0775);
	}
	if(! file_exists($userPath . "/image"))
	{
		//通常サイズとサムネイルのフォルダ
		mkdir($userPath . "/image",0775);
		chmod($userPath . "/image",0775);
	}
	if(! file_exists($userPath . "/thumbnail"))
	{
		//通常サイズとサムネイルのフォルダ
		mkdir($userPath . "/thumbnail",0775);
		chmod($userPath . "/thumbnail",0775);
	}
	
	
	//ファイル名をつける。拡張子なしのも。
	if($pageNum != 0)
	{
		$fileName = date("YmdHis") . "_" . $pageNum . ".jpg";
	}
	else
	{
		$fileName = date("YmdHis") . "_0000.jpg";
	}
	$fileNameWithoutSafix = substr($fileName,0,strpos($fileName,".jpg"));
	
	//$docRoot = "/var/www/html/my_pageview";
	
	//ドラッグ開始座標と終了座標
	$startX = 0;
	$startY = 0;
	$height = 0;
	$width = 0;
	
	//左右ページのページ番号。
	$leftPageNo = "";
	$rightPageNo = "";
	
	//ドラッグ開始座標と幅と高さを取得
	if(isset($_POST["startX"]))
	{
		$startX = $_POST["startX"];
	}
	if(isset($_POST["startY"]))
	{
		$startY = $_POST["startY"];
	}
	if(isset($_POST["height"]))
	{
		$height = $_POST["height"];
	}
	if(isset($_POST["width"]))
	{
		$width = $_POST["width"];
	}
	
	//左右ページのページ番号を取得
	if(isset($_POST["leftPageNo"]))
	{
		$leftPageNo = $_POST["leftPageNo"];
	}
	if(isset($_POST["rightPageNo"]))
	{
		$rightPageNo = $_POST["rightPageNo"];
	}
	
	$heightThumb = 0;
	$widthThumb = 0;
	//サムネイルの幅と高さを取得
	if(isset($_POST["heightThumb"]))
	{
		$heightThumb = $_POST["heightThumb"];
	}
	if(isset($_POST["widthThumb"]))
	{
		$widthThumb = $_POST["widthThumb"];
	}
	
	$open = "";
	//見開きか、左のみか、右のみか。のフラグを取得
	if(isset($_POST["open"]))
	{
		$open = $_POST["open"];
	}
	
	$scale = "";
	//拡大倍率。アンダーバーと一緒に送られる。
	if(isset($_POST["scale"]))
	{
		$scale = $_POST["scale"];
	}
	
	switch($open)
	{
		case "lr":
			//左右ページを結合して、ユーザフォルダの直下（暫定）に保存。
			exec("convert +append jpg" . $scale . "/" . sprintf("%04d",$leftPageNo) . ".jpg jpg" . $scale . "/" . sprintf("%04d",$rightPageNo) . ".jpg " . $userPath . "/" . $fileNameWithoutSafix . ".jpg");
			break;
		case "l":
			//左のみの場合、左ページを切り抜き作業用フォルダにコピー
			copy("jpg" . $scale . "/" . sprintf("%04d",$leftPageNo) . ".jpg", $userPath . "/" . $fileNameWithoutSafix . ".jpg");
			break;
		case "r":
			//みぎのみの場合、みぎページを切り抜き作業用フォルダにコピー
			copy("jpg" . $scale . "/" . sprintf("%04d",$rightPageNo) . ".jpg", $userPath . "/" . $fileNameWithoutSafix . ".jpg");
			break;
	}
	
	switch($divide)
	{
		//画像保存処理
		case "save":
			
			//Imlib2を使っての切り抜き
			exec("perl " . realpath(".") . "/imlib2ForDownload.pl " . realpath(".") . "/" . $userPath . "/" . $fileNameWithoutSafix . ".jpg " . realpath(".") . "/" . $userPath . "/image/" . $fileNameWithoutSafix . ".jpg " . $startX . " " . $startY . " " . $width . " " . $height);
			
			//切り抜いたやつを縮小してサムネイル用のを作る
			exec("perl " . realpath(".") . "/imlib2ForThumbnail.pl " . realpath(".") . "/" . $userPath . "/image/" . $fileNameWithoutSafix . ".jpg " . realpath(".") . "/" . $userPath . "/thumbnail/" . $fileNameWithoutSafix . ".jpg " . $widthThumb . " " . $heightThumb . ";");
			
			
			break;
		
		//画像保存処理
		case "ImageMagick":
			
			//ImageMagickを使って切抜
			exec("convert -crop " . $width . "x" . $height . "+" . $startX . "+" . $startY . " " . $userPath . "/" . $fileNameWithoutSafix . ".jpg " . $userPath . "/image/" . $fileNameWithoutSafix . ".jpg");
			
			//切り抜いたやつを縮小してサムネイル用のを作る
			exec("convert -geometry " . $widthThumb . "x" . $heightThumb . " " . $userPath . "/image/" . $fileNameWithoutSafix . ".jpg " . $userPath . "/thumbnail/" . $fileNameWithoutSafix . ".jpg");
			
			
			break;
	}
	
	
	//ユーザ個別のディレクトリ直下にある、結合した画像を削除
	exec("rm -rf " . $userPath . "/" . $fileName);
	
	//ファイル名のリスト出して、それをecho。
	$strFiles = "";
	$cnt = 0;
	if($dir = opendir($userPath . "/image"))
	{
		while(($file = readdir($dir)) !== false)
		{
			if(strcmp($file,".") != 0 && strcmp($file,"..") != 0 && ! is_dir($file))
			{
				//通常サイズのURL,サムネイルのURL,ベース名\nでつなげる。
				$strFiles .= "\n" . $selfURL . "/image/" . $file . "," . $selfURL . "/thumbnail/" . $file . "," . substr($file,0,strpos($file,".jpg"));
			
				$cnt++;
			}
		}
	
		closedir($dir);
		//先頭の改行コード取り除き
		$strFiles = substr($strFiles,1);
	}

	$strFiles = "lists=" . $strFiles;
	echo $strFiles;
}
else
{
	switch($divide)
	{
		//リストを返す
		case "reply_list":
		
			//ファイル名のリスト出して、それをecho。
			$strFiles = "";
			$cnt = 0;
			if($dir = opendir($userPath . "/image"))
			{
				while(($file = readdir($dir)) !== false)
				{
					if(strcmp($file,".") != 0 && strcmp($file,"..") != 0 && ! is_dir($file))
					{
						//通常サイズのURL,サムネイルのURL,ベース名\nでつなげる。
						$strFiles .= "\n" . $selfURL . "/image/" . $file . "," . $selfURL . "/thumbnail/" . $file . "," . substr($file,0,strpos($file,".jpg"));
					
						$cnt++;
					}
				}
			
				closedir($dir);
				//先頭の改行コード取り除き
				$strFiles = substr($strFiles,1);
			}
		
			$strFiles = "lists=" . $strFiles;
			echo $strFiles;
		
			break;
		
		//一個だけ削除処理
		case "delete_this":
		
			$fileName = "";
			//送られた、画像のファイル名を取得。
			if(isset($_POST["file_name"]))
			{
				$fileName = $_POST["file_name"];
			}
		
		
		
			$compFlg = 0;
			//該当のファイルを削除
			//まずファイルの存在を確認。
			if(file_exists($userPath))
			{
				//目当てのファイルめがけて削除処理をかける。
				if(unlink($userPath . "/image/" . $fileName))
				{
					$compFlg++;
				}
				if(unlink($userPath . "/thumbnail/" . $fileName))
				{
					$compFlg++;
				}
			
				if($compFlg == 2)
				{
					echo "lists=true";
				}
				else
				{
					echo "lists=false";
				}
			}
			else
			{
				echo "lists=incomplete";
			}
		
			break;
		
		//全削除処理
		case "delete_all":
		
			//フォルダの中身を全削除
			if(file_exists($userPath))
			{
				/*exec("rm -rf " . $userPath . "/image/*.jpg");
				exec("rm -rf " . $userPath . "/thumbnail/*.jpg");
				exec("rm -rf " . $userPath . "/image");
				exec("rm -rf " . $userPath . "/thumbnail");*/
				exec("rm -rf " . $userPath);
			
				echo "lists=all_done";
			}
			else
			{
				echo "lists=all_not_done";
			}
		
			break;
		
		//一個だけDL処理
		case "dl_this":
		
			$fileaName = $_POST["file_name"];
		
			echo "lists=" . $selfURL . "/image/" . $fileName;
		
			break;
		
		//一括DL処理
		case "dl_all":
		
			//一回、圧縮ファイルを消す。
			if(file_exists($userPath . "/cropped_images.lzh"))
			{
				unlink($userPath . "/cropped_images.lzh");
			}
		
			exec("cd " . $userPath . ";lha -a cropped_images.lzh image/*.jpg");
			chmod($userPath . "/cropped_images.lzh",0777);
		
		
			//圧縮ファイルが出来ている場合があるので、あったら消す。
			if(file_exists($userPath . "/cropped_images.bak"))
			{
				chmod($userPath . "/cropped_images.bak",0777);
				unlink($userPath . "/cropped_images.bak");
			}
		
			echo "lists=" . $selfURL . "/cropped_images.lzh";
		
			break;
		
		//何もしないとき
		default :
			//以下デバッグ用
			$str = "not found!";
			if(file_exists("../admin/.cataloglist"))
			{
				$str = "found it!!";
			}
			break;
	}
}




exit;
?>