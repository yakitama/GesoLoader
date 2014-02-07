<?php

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//
//  アップローダーでゲソ！
//    アクセス用ファイルじゃなイカ？
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// アップローダー GesoLoader クラスをインクルード
require_once "GesoLoader.php";

// インスタンスを生成
$su = new GesoLoader();

// ----- POST コマンド取得 ----------------------------------------------------------
$cmd = (isset($_POST["cmd"])) ? $_POST["cmd"] : "";
if ( $cmd == "" ) {
	$cmd = (isset($_GET["cmd"])) ? $_GET["cmd"] : "";
}


// ----- 呼び出す関数を選択 ------------------------------------------------------------

switch ( $cmd ) {
	case 'upfile':
		$su->file_upload();
		break;
	
	case 'th':
		$num = (isset($_GET['number'])) ? $_GET['number'] : 0;
		$su->thumb_image($num);
		break;
	
	case 'delfile':
		$num = (isset($_GET['number'])) ? $_GET['number'] : 0;
		if ( $num == 0 ) {
			$num = (isset($_POST['number'])) ? $_POST['number'] : 0;
		}
		$su->file_delete($num);
		break;
		
	// ----- cmd test: テスト関数実行 ---------------------------------
	case 'test':
		$su->test();
		
		break;
	
	// ----- cmd 指定なし: アップローダーのファイル一覧を表示 -----------------------
	default:
		// ページ番号が指定されているか確認
		$page = (isset($_GET["p"])) ? $_GET["p"] : 1;
		$su->show_title($page);
		break;
}
