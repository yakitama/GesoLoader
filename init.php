<?php

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//
//  アップローダーでゲソ！
//    設定ファイルじゃなイカ？
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// ----- 基本設定 -----------------------------------------------------------------

// アップローダーの名前
$uploader_title = "ゲソろだ";

// アップロードしたファイルを保存するディレクトリ。最後に / を忘れずに。相対パスで書くこと。
$up_dir = "files/";

// アップロードしたファイルを記憶するログファイル
$up_log = "files/log.txt";

// HTML テンプレートを保存するディレクトリ。最後に / を忘れずに。
$html_template_dir = "/var/www/gl/template/";

// アップローダーアクセス用の PHP ファイル名
$self_scriptname = "index.php";

// 1 ページに表示するファイルの数
$page_line = 20;

// アップロードできる最大サイズ
$max_file_size = 300 * 1000 * 1000;
// 300MB

// アップロードしたらファイルの拡張子を自動的に txt に書替える
$change_extension = array(
	'c','php','cpp','js','html','java','cgi','pl','h','v','htm','cs','ini',
);

// アップロードできるファイルの拡張子
$arrow_extension = array(
	'jpg','png','gif','3gp','amc','mld', 'mid','phps','txt','zip','rar','mpg','avi','mp4','mp3','bmp','odt','ods','odp','odg','odb','pdf','ogg','wmv','flv',
);

// パスワードハッシュ化の salt
$passwd_salt = 'yakitama';

// クッキーの保存期間（単位: 日）
$cookie_time = 30;

// ----- HTML テンプレート設定 --------------------------------------------------------

// HTML ヘッダ部分のテンプレート
$template_header = "header.txt";

// HTML フッタ部分のテンプレート
$template_footer = "footer.txt";

// タイトル画面のテンプレート
$template_title = "title.txt";
$title_title = $uploader_title;

// アップロード用フォーム部分のテンプレート
$template_upform = "upload_form.txt";

// ファイル一覧部分のテンプレート
$template_flist = "file_link2.txt";

// 前後ページへ移動するテンプレート
$template_flist_navi = "file_link2_navi.txt";

// アップロード処理のエラーテンプレート
$template_up_error = "upload_err.txt";
$title_up_error = "アップロードに失敗しました";

// ファイル削除画面のテンプレート
$template_delete = "file_delete.txt";
$title_delete = "ファイルを削除します";

// ファイル削除エラー時のテンプレート
$template_delete_error = "file_delete_error.txt";
$title_delete_error = "削除できません";

// ----- HTML テンプレート関連設定 ------------------------------------------------------

// 置換タグ（可変）
$variable_replace_tags = array(
"title" => "##THIS_IS_A_TITLE##", // HTML ヘッダ部分 タイトル文字列置き換えタグ
"page_list" => "##PAGE_LIST##", // タイトル画面 ページリンクを表示する場所のタグ
"upload_form" => "##FILE_UPLOAD##", // タイトル画面 ファイルアップロード用フォームを表示する場所のタグ
"password_form" => "##PASSWORD_FORM##", // タイトル画面 パスワードフォーム入力欄タグ
"file_list" => "##FILE_LIST##", // タイトル画面 ファイル一覧を表示する場所のタグ
"file_search"=> "##SEARCH##",	// タイトル画面 ファイル検索リンクを表示する場所のタグ
"error" => "##ERROR_MESSAGE##", // エラーメッセージ表示位置
// ----- flist: ファイル一覧で変換するタグです。
"flist_ext_img"=> "##EXT_IMG##", // ファイル一覧: 拡張子アイコン表示タグ
"flist_fname"=> "##FILE_NAME##", // ファイル一覧: ファイル名表示タグ
"flist_delete"=> "##DEL_LINK##", // ファイル一覧: ファイル削除リンクタグ
"flist_comment"=> "##COMMENT##", // ファイル一覧: コメント表示
"flist_ftime"=> "##UPLOAD_TIME##",	// ファイル一覧: ファイルアップロード時刻表示タグ
"flist_fnum"=> "##FILE_NUMBER##", // ファイル一覧: ファイルのログ番号タグ
"flist_navi"=> "##NAVIGATION_LINK##", // ファイル一覧: 前後ページへの移動
);

// 置換タグ（固定）
$constant_replace_tags = array("##MYSELF_FILENAME##" => $self_scriptname, "##UPLOAD_MAX_SIZE##" => $max_file_size, );

// 画像サムネイルの最大サイズ
$image_thumb_max = 190;

// アイコンの格納フォルダ。最後に / を忘れずに。相対パスで。
$icon_dir = "./icon";

// アップロードできる拡張子に対して、それぞれのアイコンを指定する
$icon_image = array(
	'jpg'=> '##THUMBNAIL##',
	'png'=> '##THUMBNAIL##',
	'gif'=> 'test',
	'3gp'=> 'test',
	'amc'=> 'test',
	'mld'=> 'test',
	'mid'=> 'test',
	'phps'=> 'test',
	'txt'=> 'test',
	'zip'=> 'test',
	'rar'=> 'test',
	'mpg'=> 'test',
	'avi'=> 'test',
	'mp4'=> 'test',
	'mp3'=> 'test',
	'bmp'=> 'test',
	'odt'=> 'test',
	'ods'=> 'test',
	'odp'=> 'test',
	'odg'=> 'test',
	'odb'=> 'test',
	'pdf'=> 'test',
	'ogg'=> 'test',
	'wmv'=> 'test',
	'flv'=> 'test',
);


// ----- データ処理設定 --------------------------------------------------------------

// ログファイルのデータ区切り文字
$data_delimiter = "<>";
