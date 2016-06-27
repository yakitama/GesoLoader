<?php

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//
//  アップローダーでゲソ！
//   by yakitama
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class GesoLoader {
	
	private $_uploader_title;
	private $_up_dir;
	private $_up_dir_web;
	private $_up_log;
	private $_html_template_dir;
	private $_self_scriptname;
	private $_self_directory;
	private $_page_line;
	private $_pages;
	private $_password_salt;
	private $_cookie_time;
	
	private $_c_ext;
	private $_a_ext;
	
	private $_log_array;
	private $_log_count;
	private $_log_load_flag;
	
	private $_template_head;
	private $_template_foot;
	private $_template_title;
	private $_title_title;
	private $_template_upform;
	private $_template_flist;
	private $_template_flist_navi;
	private $_template_up_error;
	private $_title_up_error;
	private $_template_delete;
	private $_title_delete;
	private $_template_delete_error;
	private $_title_delete_error;
	
	private $_replace_tags_v;
	private $_replace_tags_c;
	private $_image_max;
	
	private $_output_header_flag;
	private $_output_footer_flag;
	private $_output_html_array;
	
	private $_error_message_array;
	private $_error_template_title_not_found;
	private $_error_template_footer_not_found;
	private $_error_template_upform_not_found;
	private $_error_title_upform_not_found;
	private $_error_title_pglist_not_found;
	private $_error_log_cannot_write;
	private $_error_template_flist_not_found;
	private $_error_title_flist_not_found;
	private $_error_up_file_no_uploaded;
	private $_error_up_file_not_arrowed;
	private $_error_up_file_failed;
	private $_error_del_file_not_found;
	private $_error_del_wrong_password;
	private $_error_del_fail;
	
	private $_data_delimiter;
	
	
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	コンストラクタ
	// 機能:	
	// 引数:	
	// 戻り値:	
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	function __construct() {
		
		// ----- スクリプト開始の初期設定 -------------------------------------------------
		
		// タイムゾーンを Asia/Tokyo に設定
		date_default_timezone_set ("Asia/Tokyo");
		
		// カレントディレクトリ設定
		$this->_self_directory = getcwd();
		
		// ログファイルを読み込む配列を初期化
		$this->_log_array = array();
		$this->_log_count = 0;
		
		// フラグ初期化
		$this->_log_load_flag = false;
		$this->_output_header_flag = false;
		$this->_output_footer_flag = false;
		
		// エラーメッセージ配列を初期化
		$this->_error_message_array = array();
		
		// HTML 出力バッファを初期化
		$this->_output_html_array = array();
		

		// ----- 設定項目 -----------------------------------------------------------------
		include_once("init.php");
		$this->_uploader_title = $uploader_title;									// アップローダーの名前
		$this->_up_dir = $this->_self_directory.'/'.$up_dir;						// アップロードファイルを保存するディレクトリ
		$this->_up_dir_web = $up_dir;												// アップロードファイルのディレクトリ（ウェブアクセス用相対パス）
		$this->_up_log = $up_log;													// アップロードファイルを記憶するログファイル
		$this->_html_template_dir = $html_template_dir;								// HTML テンプレートを保存するディレクトリ
		$this->_self_scriptname = $self_scriptname;									// アクセス用のスクリプトファイル名
		$this->_page_line = $page_line;												// 1 ページに表示する行数
		$this->_password_salt = $passwd_salt;										// パスワードハッシュ化の salt
		$this->_cookie_time = 60*60*24*$cookie_time;								// クッキーの保存期間（init.phpで日で指定される数字を秒に変換）
		
		$this->_c_ext = $change_extension;											// .txt に書き換える拡張子のリスト
		$this->_a_ext = $arrow_extension;											// アップロード可能な拡張子のリスト
		
		$this->_replace_tags_v = $variable_replace_tags;							// 可変 置換タグ
		$this->_replace_tags_c = $constant_replace_tags;							// 固定 置換タグ
		$this->_image_max = $image_thumb_max;										// 画像サムネイルの最大サイズ
		
		$this->_template_head = $template_header;									// HTML ヘッダ部分のテンプレートファイル名
		$this->_template_foot = $template_footer;									// HTML フッタ部分のテンプレートファイル名
		$this->_template_title = $template_title;									// タイトル画面のテンプレートファイル名
		$this->_title_title = $title_title;											// タイトル画面のタイトル文字列
		$this->_template_upform = $template_upform;									// タイトル画面のファイルアップロード部分のテンプレートファイル名
		$this->_template_flist = $template_flist;									// タイトル画面のファイル一覧部分のテンプレートファイル名
		$this->_template_flist_navi = $template_flist_navi;							// タイトル画面の前後ページ移動用リンクのテンプレートファイル名
		$this->_template_up_error = $template_up_error;								// アップロード処理のエラーテンプレート
		$this->_title_up_error = $title_up_error;									// アップロード処理のエラーテンプレート（タイトル文字列）
		$this->_template_delete = $template_delete;									// ファイル削除画面のテンプレート
		$this->_title_delete = $title_delete;										// ファイル削除画面のテンプレート（タイトル文字列）
		$this->_template_delete_error = $template_delete_error;						// ファイル削除時のエラーテンプレート
		$this->_title_delete_error = $title_delete_error;							// ファイル削除時のエラーテンプレート（タイトル文字列）
		
		
		include_once("message.php");												
		
		$this->_error_template_footer_not_found = $error_template_footer_not_found;	// HTML フッター部分のテンプレートファイルが行方不明
		$this->_error_template_title_not_found = $error_template_title_not_found;	// タイトル画面のテンプレートファイルが行方不明
		$this->_error_template_upform_not_found = $error_template_upform_not_found;	// タイトル画面のアップロード用フォームのテンプレートファイルが行方不明
		$this->_error_title_upform_not_found = $error_title_upform_not_found;		// タイトル画面のアップロード用フォーム置き換えタグが行方不明
		$this->_error_title_pglist_not_found = $error_title_pglist_not_found;		// タイトル画面のページリスト置き換えタグが行方不明
		$this->_error_log_cannot_write = $error_log_cannot_write;					// ログファイルが作成できない
		$this->_error_template_flist_not_found = $error_template_flist_not_found;	// タイトル画面のファイル一覧のテンプレートファイルが行方不明
		$this->_error_title_flist_not_found = $error_title_flist_not_found;			// タイトル画面のファイル一覧置き換えタグが行方不明
		$this->_error_up_file_no_uploaded = $error_up_file_no_uploaded;				// アップロード処理で、ファイルがアップロードされていない
		$this->_error_up_file_not_arrowed = $error_up_file_not_arrowed;				// アップロード処理で、許可されていないファイル拡張子
		$this->_error_up_file_failed = $error_up_file_failed;						// アップロード処理に失敗した
		$this->_error_del_file_not_found = $error_del_file_not_found;				// ファイル削除処理で、ファイルが見つからない
		$this->_error_del_wrong_password = $error_del_wrong_password;				// ファイル削除処理で、パスワードが一致しない
		$this->_error_del_fail = $error_del_fail;									// ファイル削除処理に失敗
		
		$this->_data_delimiter = $data_delimiter;									// ログファイルのデータ区切り文字
		
	}


	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	デストラクタ
	// 機能:	
	// 引数:	
	// 戻り値:	
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	function __destruct() {
		// ----- エラーメッセージが存在する場合、エラー表示用 HTML を作成する ----------------------------
		// エラータグを検索する
		$error_tag_found_flag = false;
		foreach ( $this->_output_html_array as $error_key => &$output ) {
			if ( strpos($output, $this->_replace_tags_v["error"]) !== false ) {
				// タグを削除する
				$output = "";
				
				// ループから抜ける
				$error_tag_found_flag = true;
				break;
			}
		}
		
		// エラー表示用 HTML を挿入する
		if ( count($this->_error_message_array) > 0 ) {
			$error_line = array();
			$error_line[] = '<ul class="error">';
			foreach ( $this->_error_message_array as $error_message ) {
				$error_line[] = '<li>'.$error_message.'</li>';
			}
			$error_line[] = '</ul>';
			
			if ( $error_tag_found_flag === true ) {
				$this->_output_html_array = $this->array_insert($this->_output_html_array, $error_line, $error_key);
			}
		}
		
		// ----- HTML 出力バッファを出力する ---------------------------------------------
		echo implode("", $this->_output_html_array);
		
	}


	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	show_title
	// 機能:		ファイル一覧を表示する	
	// 引数:		$page = 1 : ページ番号を指定する
	// 戻り値:	なし
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	public function show_title ( $page = 1 ) {
		// ----- テンプレートを読み込む --------------------------------------------------
		$template_failed_flag = false;
		
		// タイトル画面全体のテンプレート読み込み
		$html_line = $this->load_template($this->_template_title);
		if ( $html_line === false ) {
			$this->echo_error_message($this->_error_template_title_not_found);
			$template_failed_flag = true;
		}
		
		// タイトル画面のアップロード用フォーム部分のテンプレート読み込み
		$upload_line = $this->load_template($this->_template_upform);
		if ( $upload_line === false ) {
			$this->echo_error_message($this->_error_template_upform_not_found);
			$template_failed_flag = true;
		}
		
		// タイトル画面のファイル一覧部分のテンプレート読み込み
		$flist_line = $this->load_template($this->_template_flist);
		if ( $flist_line === false ) {
			$this->echo_error_message($this->_error_template_flist_not_found);
			$template_failed_flag = true;
		}
		
		// ヘッダーテンプレート読み込み
		$this->echo_html_head($this->_title_title);
		
		if ( $template_failed_flag === true ) {
			return false;
		}
		
		// ログファイル読み込み
		$this->load_log();
		
		// ----- クッキー取得 -----------------------------------------------------------
		$c_password = (isset($_COOKIE["delete_password"])) ? $_COOKIE["delete_password"] : '';
		
		// ----- 置き換えタグを置き換え --------------------------------------------------
		
		// ----- ファイルアップロード用フォーム -----------------------------------------
		$upform_tag_found_flag = false; 
		foreach ( $html_line as $upfile_key => &$line ) {
			if ( strpos($line, $this->_replace_tags_v["upload_form"]) !== false ) {
				
				// タグを削除する
				$line = "";
				
				// 発見フラグをセット
				$upform_tag_found_flag = true;
				
				// アップロード用フォームのパスワード入力欄を入れ替え
				$password_form = "<input type=\"password\" name=\"delete\" value=\"{$c_password}\" />\n";
				foreach ( $upload_line as &$up_oneline ) {
					$up_oneline = str_replace($this->_replace_tags_v["password_form"], $password_form, $up_oneline);
				}
				
				// タグ位置にアップロード用フォーム部分のテンプレートを挿入
				$html_line = $this->array_insert($html_line, $upload_line, $upfile_key);
				
				// 複数行のタグを変換できるように、検索ループは抜けない
				// break;
			}
		}
		// タグ位置が見つからない場合はエラーメッセージを表示
		if ( $upform_tag_found_flag === false ) {
			$this->echo_error_message($this->_error_title_upform_not_found);
		}
		
		// ----- ページ一覧 ---------------------------------------------------
		
		// タグ位置を検索
		$pglist_tag_found_flag = false;
		foreach ( $html_line as $pglist_key => &$pglist ) {
			if ( strpos($pglist, $this->_replace_tags_v["page_list"]) !== false ) {
				// タグを削除する
				$pglist = "";
				
				// 発見フラグをセット
				$pglist_tag_found_flag = true;
				
				// ページ一覧の表示用 HTML
				$pglist_html = '<div class="pglist">';
				if ( $this->_pages > 0 ) {
					
					// 前に戻るリンク
					if ( $page == 1 ) {
						$pglist_html .= "<div class=\"linkbox\">&lt;&lt;</div>";
					}
					else {
						$back_page_number = $page - 1;
						$pglist_html .= "<a class=\"linkbox\" href=\"{$this->_self_scriptname}?p={$back_page_number}\">&lt;&lt;</a>";
					}
					
					for ( $p = 1; $p <= $this->_pages; $p++ ) {
						// 現在表示中のページと一致したときは、リンクを貼らない
						if ( $p == $page ) {
							$pglist_html .= "<div class=\"linkbox\">{$p}</div>";
						}
						else {
							$pglist_html .= "<a class=\"linkbox\" href=\"{$this->_self_scriptname}?p={$p}\">{$p}</a>";
						}
					}
					
					// 次に進むリンク
					if ( $page == $this->_pages ) {
						$pglist_html .= "<div class=\"linkbox\">&gt;&gt;</div>";
					}
					else {
						$next_page_number = $page + 1;
						$pglist_html .= "<a class=\"linkbox\" href=\"{$this->_self_scriptname}?p={$next_page_number}\">&gt;&gt;</a>";
					}
					
				}
				else {
					$pglist_html .= "<div class=\"linkbox\">0</div>";
				}
				$pglist_html .= "</div>\n";
				
				// ページ一覧 HTMLを置き換える
				$html_line[$pglist_key] = $pglist_html;
				
				//複数行のタグを変換できるように、検索ループは抜けない
				// break;
			}
		}
		
		// タグ位置が見つからない場合はエラーメッセージを表示
		if ( $pglist_tag_found_flag === false ) {
			$this->echo_error_message($this->_error_title_pglist_not_found);
		}
		
		// ----- ファイル一覧 --------------------------------------------------
		
		// タグ位置を検索
		$flist_tag_found_flag = false;
		foreach ( $html_line as $flist_key => &$flist ) {
			if ( strpos($flist, $this->_replace_tags_v["file_list"]) !== false ) {
				// タグを削除する
				$flist = "";
				
				// 発見フラグをセット
				$flist_tag_found_flag = true;
				
				// ファイル一覧部分のHTML保存用配列
				$flist_html = array();
				
				// ファイル一覧の最初のログ行を計算
				$start_log = ($page - 1) * $this->_page_line;
				
				for ( $fcount = 0; $fcount < $this->_page_line; $fcount++ ) {
					
					$log_key = $fcount + $start_log;
					
					// ログの最大行数を超えていたら処理終了
					if ( $log_key >= $this->_log_count ) {
						break;
					}
					
					// テンプレートから読み込み、変換する
					$now_log = $flist_line;
									
					foreach ( $now_log as &$flist_value ) {
						
						$file_name = $this->get_filename($log_key);
						$file_path = $this->get_file_path($log_key);
						
						$file_link_start = '<a href="'.$file_path.'" target="_blank">';
						$file_link_end = '</a>';
						
						$file_link = $file_link_start . $file_name . $file_link_end;
						
						$del_link = "<a href=\"{$this->_self_scriptname}?cmd=delfile&number={$this->_log_array[$log_key]['number']}\">d</a>\n";
						
						if ( ($this->_log_array[$log_key]['extension'] == 'jpg') || ($this->_log_array[$log_key]['extension'] == 'png') ) {
							$file_image = $file_link_start . '<img src="'.$this->_self_scriptname.'?cmd=th&number='.$this->_log_array[$log_key]['number'].'" />' . $file_link_end;
						}
						else {
							$file_image = $file_link_start . 'test' . $file_link_end;
						}
						
						$file_time = date("Y-m-d H:i:s", $this->_log_array[$log_key]['up_time']);
						$file_comment = $this->_log_array[$log_key]['comment'];
						
						// タグを置き換える
						$flist_value = str_replace($this->_replace_tags_v["flist_ext_img"], $file_image, $flist_value);					
						$flist_value = str_replace($this->_replace_tags_v["flist_fname"], $file_link, $flist_value);
						$flist_value = str_replace($this->_replace_tags_v["flist_delete"], $del_link, $flist_value);
						$flist_value = str_replace($this->_replace_tags_v["flist_comment"], $file_comment, $flist_value);
						$flist_value = str_replace($this->_replace_tags_v["flist_ftime"], $file_time, $flist_value);					
					}
					
					// ファイル更新部分の HTML バッファにためこむ
					$flist_html = array_merge($flist_html, $now_log);
				}
				
				// ファイル更新部分の HTML バッファを html_line にコピー
				$html_line = $this->array_insert($html_line, $flist_html, $flist_key);


				//複数行のタグを変換できるように、検索ループは抜けない
				// break;
			}
		}
		
		// タグが見つからないときはエラーメッセージを表示
		if ( $flist_tag_found_flag === false ) {
			$this->echo_error_message($this->_error_title_flist_not_found);
		}
		
		
		// ----- HTML を出力 -----------------------------------------------------
		$this->echo_html_content($html_line);
		
		// ----- フッタテンプレート読み込み ------------------------------------------------
		$this->echo_html_foot();
		
		return true; 
	}


	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	get_filename
	// 機能:	スクリプト位置からアップロードファイルまでの相対パスを返却します
	// 引数:	$number ログ番号
	// 戻り値:	アップロードしたファイルの名前
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	private function get_filename ( $number ) {
		$this->load_log();
		$file_name = $this->_log_array[$number]['number'].'.'.$this->_log_array[$number]['extension'];
		
		return $file_name;
	}
	
	
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	get_file_path
	// 機能:	スクリプト位置からアップロードファイルまでの相対パスを返却します
	// 引数:	$number ログ番号
	// 戻り値:	アップロードしたファイルの名前
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	private function get_file_path ( $number ) {
		$file_name = $this->get_filename($number);
		$file_path = $this->_up_dir_web.$file_name;
		
		return $file_path;
	}
	
	
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	redirect_myself
	// 機能:	自分自身にリダイレクトするHTTPヘッダーを送信します
	// 引数:	なし
	// 戻り値:	なし
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	private function redirect_myself ( ) {
		$url = '//' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		header("Location: {$url}");
	}
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	file_upload
	// 機能:	ファイルをアップロードします
	// 引数:	
	// 戻り値:	戻り値を返しません
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	public function file_upload ( ) {
		
		// ファイルがアップロードされていないなら強制終了
		
		if ( (isset($_FILES['upfile']['tmp_name']) === false) || (isset($_FILES['upfile']['size']) === false) || ($_FILES['upfile']['size'] === 0) ) {
			$this->echo_error_message($this->_error_up_file_no_uploaded);
			$this->echo_html_head($this->_title_up_error);
			$html_line = $this->load_template($this->_template_up_error);
			$this->echo_html_content($html_line);
			$this->echo_html_foot();
			return;
		}
		
		// ログファイルを読み込む
		$this->load_log();
		
		// 次のアップロードファイル番号を設定
		if ( $this->_log_count > 0 ) {
			$newest_log = $this->_log_array[0];
			$next_number = $newest_log["number"]+1;
		}
		else {
			$next_number = 1;
		}
		
		// ----- アップロードされたファイルやデータを受け取る ---------------------------------------
		
		// アップロードされたファイル名を取得
		$upfile_name = (isset($_FILES['upfile']['name'])) ? $_FILES['upfile']['name'] : '';
		
		// ファイルの拡張子を取得
		$ext_pos = strrpos($upfile_name, '.');
		$ext = substr($upfile_name, $ext_pos+1, strlen($upfile_name)-$ext_pos);
		$ext = strtolower($ext);
		
		// txt に書替える対象か検索
		$prev_ext = '';											// 書換え前の拡張子
		if ( in_array($ext, $this->_c_ext) === true ) {
			$prev_ext = '.'.$ext;									// 書換え前の拡張子を記憶
			$ext = 'txt';										// 拡張子を txt に強制設定
		}
		
		// アップロード可能な拡張子でないなら、エラー
		if ( in_array($ext, $this->_a_ext) === false ) {
			$this->echo_error_message($this->_error_up_file_not_arrowed);
			$this->echo_html_head($this->_title_up_error);
			$html_line = $this->load_template($this->_template_up_error);
			$this->echo_html_content($html_line);
			$this->echo_html_foot();
			return;
		}
		
		// アップロード後の新しいファイル名を設定
		$file_name = $next_number.'.'.$ext;

		// ファイルをアップロードディレクトリに移動
		if ( move_uploaded_file($_FILES['upfile']['tmp_name'], $this->_up_dir . $file_name) === true ) {
			chmod($this->_up_dir.$file_name, 0604);
			
			// ログに書き込むデータを用意
			$mime = (isset($_FILES['upfile']['type'])) ? $this->replace_special_chars($_FILES['upfile']['type']) : 'text/example';
			$comment = (isset($_POST['comment'])) ? $this->replace_special_chars($_POST['comment']) : '';
			$comment = $this->make_url_link($comment);
			$host = $this->replace_special_chars(gethostbyaddr($_SERVER['REMOTE_ADDR']));
			$size = $_FILES['upfile']['size'];
			$del = (isset($_POST['delete'])) ? $_POST['delete'] : '';
			
			// ログに書き込む
			$this->add_log($next_number, $ext, $comment, $host, $size, $mime, $del, $upfile_name);
			
			// クッキーを設定
			setcookie("delete_password", $del, time()+$this->_cookie_time);
			
			// リダイレクト
			$this->redirect_myself();
		}
		// ファイルをアップロードディレクトリに移動できなかった場合
		else {
			$this->echo_error_message($this->_error_up_file_failed);
			$this->echo_html_head($this->_title_up_error);
			$html_line = $this->load_template($this->_template_up_error);
			$this->echo_html_content($html_line);
			$this->echo_html_foot();
			return;
		}
		
		return;
		
	}


	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	file_delete
	// 機能:	ファイルを削除します
	// 引数:	
	// 戻り値:	戻り値を返しません
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	public function file_delete ($number) {
		
		// フォームにパスワードが設定されているか確認
		$fpasswd = (isset($_POST['delete'])) ? $_POST['delete'] : '';
		
		// ファイルが存在するか確認
		$log_key = $this->search_file_number($number);
		
		try {
			
			// ファイルが存在しない 
			if ( $log_key === false ) {				
				throw new Exception($this->_error_del_file_not_found);
			}
			
			// パスワードが設定されていない
			if ( $fpasswd == '' ) {
				
				// テンプレートファイルを読み込む
				$html_line = $this->load_template($this->_template_delete);
				
				// 可変タグを変換する
				foreach ( $html_line as $html_key => &$line ) {
					$line = str_replace($this->_replace_tags_v["flist_fname"], $this->get_filename($log_key), $line);
					$line = str_replace($this->_replace_tags_v["flist_fnum"], $number, $line);
				}
				
				// HTML表示
				$this->echo_html_head($this->_title_delete);
				$this->echo_html_content($html_line);
				$this->echo_html_foot();
				return 0;
			}
			
			// パスワードを暗号化する
			$fpasswd = $this->enc_password($fpasswd);
			
			// パスワードが一致しない場合
			if ( $fpasswd != $this->_log_array[$log_key]["del_passwd"] ) {				
				throw new Exception($this->_error_del_wrong_password);
			}
			
			// ファイルを削除する
			$result = $this->delete_log($number);
			if ( $result != 0 ) {
				throw new Exception($this->_error_del_fail);
			}
			
			// ファイル削除完了 リダイレクト
			$this->redirect_myself();
			
			return 0;
			
		}
		catch (Exception $e) {
			$this->echo_error_message($e->getMessage());
			$this->echo_html_head($this->_title_delete_error);
			$html_line = $this->load_template($this->_template_delete_error);
			$this->echo_html_content($html_line);
			$this->echo_html_foot();
			return 1;
		}
		
	}
	
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	load_log
	// 機能:	ログファイルを読み込み、メンバ変数に登録する
	// 引数:	$force = false すでにログファイルを読み込み済みでも、強制的に読み込む
	// 戻り値:	正常終了時: 0
	//        	異常終了時: 1
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	private function load_log ( $force = false ) {
		
		// 強制的にログファイルを読み込むフラグがセットされているなら、処理する
		if ( $force === true ) {
			
		}
		// すでにログファイルを読み込み済なら、何もせずに終了
		else if ( $this->_log_load_flag === true ) {
			return 1;
		}
		
		// すでに読み込まれているかもしれないログファイルを読み込む配列を初期化
		$this->_log_array = array();
		$this->_log_count = 0;
			
		// ログファイルを読み込む
		$log_line = @file($this->_up_log);
		if ( $log_line === false ) {
			// ログファイルが見つからない場合は新規作成
			$fpc = @file_put_contents($this->_up_log, "");
			if ( $fpc === false ) {
				$this->echo_error_message($this->_error_log_cannot_write);
				return 2;
			}
			
			// ページ数を計算
			$this->_pages = ceil($this->_log_count / $this->_page_line);
						
			// ログファイル読み込み完了フラグをセット
			$this->_log_load_flag = true;
			return 0;
		}
		
		// ログファイルを分解して、配列に登録していく
		foreach ( $log_line as $log ) {
			$log = str_replace(array("\r\n","\r","\n"), "", $log);
			list($lnum, $lext, $lcom, $lhos, $ltim, $lsiz, $lmim, $ldel, $lorg) = explode($this->_data_delimiter, $log);
			$log_one = array(
				"number"=> $lnum,
				"extension"=> $lext,
				"comment"=> $lcom,
				"up_host"=> $lhos,
				"up_time"=> $ltim,
				"file_size"=> $lsiz,
				"mime_type"=> $lmim,
				"del_passwd"=> $ldel,
				"orig_file_name"=> $lorg
			);
			
			$this->_log_array[] = $log_one;
			$this->_log_count++;
		}
		
		// ページ数を計算
		$this->_pages = ceil($this->_log_count / $this->_page_line);
		
		// ログファイル読み込み完了フラグをセット
		$this->_log_load_flag = true;
		
		return 0;
	}
	
	
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	add_log
	// 機能:	ログを追加する
	// 引数:	$ext	ファイル拡張子
	//		$com	コメント
	//		$hos	アップロードした人のホスト名(またはIPアドレス)
	//		$siz	ファイルサイズ
	//		$mim	MIME TYPE
	//		$del	DELETE PASSWORD
	//		$org	オリジナルファイルネーム
	// 戻り値:	なし
	//        	
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	private function add_log ( $num, $ext, $com, $hos, $siz, $mim, $del, $org ) {
		
		// ----- 引数で指定しない、自動設定のデータを作成する ---------------------------------------
		if ( $num === 0 ) {
			if ( $this->_log_count > 0 ) {
				$last_number = $this->_log_array[0]["number"];
				
				// number 作成
				$num = $last_number + 1;
			}
			else {
				$num = 1;
			}
		}
		
		// up_time 作成
		$tim = strtotime("now");
		
		// ----- ログ配列に挿入する要素の作成 -----------------------------------------------
		$new_log = array(
			"number"=> $num,
			"extension"=> $ext,
			"comment"=> $com,
			"up_host"=> $hos,
			"up_time"=> $tim,
			"file_size"=> $siz,
			"mime"=> $mim,
			"del_passwd"=> $this->enc_password($del),
			"org_file_name"=> $org
		);
		
		// ----- ログ配列に挿入する
		$this->_log_array = $this->array_insert(array($new_log), $this->_log_array, 1);
		
		// ----- ログを書き出す
		$this->flush_log();
	}
	
	
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	enc_password
	// 機能:	パスワードを記録するために暗号化する
	// 引数:	$passwd	暗号化するパスワード
	// 戻り値:	暗号化したパスワード文字列。
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	private function enc_password ( $passwd ) {
		
		return crypt($passwd, $this->_password_salt);
		
	}
	
	
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	delete_log
	// 機能:	ログを削除する
	// 引数:	$number	削除する番号
	// 戻り値:	成功した場合 0
	//        	対象の番号がない場合 1
	//        	ファイルが削除できない場合 2
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	private function delete_log ( $number ) {
		
		$log_key = $this->search_file_number($number);
		
		// 対象の番号がないので エラー
		if ( $log_key === false ) {
			return 1;
		}
		
		// ファイルを削除する
		if ( unlink($this->get_file_path($log_key)) == FALSE ) {
			return 2;
		}
		
		// 配列要素を unset!
		unset($this->_log_array[$log_key]);
		
		// ログを書き出す
		$this->flush_log();
		
		return 0;
	}
	
	
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	flush_log
	// 機能:	ログをディスクに書き出す
	// 引数:	なし
	// 戻り値:	成功した場合 0
	//        	書き出しに失敗した場合 1
	// 			書き出すログファイルがない場合 2
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	private function flush_log ( ) {
		
		// ログ配列に何かが読み込まれていることを確認
		if ( $this->_log_load_flag === true ) {
			// ディスク書き出し
			if ( ($fp = fopen($this->_up_log, "w")) !== false ) {
				foreach ( $this->_log_array as $log ) {
					fwrite($fp, implode($this->_data_delimiter, $log)."\n");
				}
				fclose($fp);
			}
			// ファイルオープンに失敗
			else {
				return 1;
			}
			
		}
		// ログ配列になにもないならエラー
		else {
			return 2;
		}
		
		// 正常終了
		return 0;
	}/**/
	
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	echo_html_head
	// 機能:	ヘッダテンプレートを出力する
	// 引数:	$title: タイトル文字列
	// 戻り値:	正常終了時: 0
	//        	異常終了時: 1
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	private function echo_html_head ( $title ) {
		
		// すでにヘッダー部分の出力が終わっているなら、何もせずに終了
		if ( $this->_output_header_flag === true )	{
			return 1;
		}
		
		// テンプレートを読み込む
		$html_line = $this->load_template($this->_template_head);
		if ( $html_line === false ) {
			// テンプレートファイルが見つからない場合は、強制終了
			echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><title>致命的なエラー</title></head><body><p>HTML ヘッダー用テンプレートファイルが見つかりません。プログラムを続行できません。</p></body></html>';
			exit(1);
		}
		
		foreach ( $html_line as &$value ) {
			// リプレースタグを引数 $title に置き換え
			$value = str_replace($this->_replace_tags_v["title"], $title, $value);
		}
		
		// テンプレートを出力
		$this->echo_html_content($html_line);
		
		// ヘッダー部分の出力完了フラグをセット
		$this->_output_header_flag = true;
		
		return 0;
	}
	
	
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	echo_html_foot
	// 機能:	ヘッダテンプレートを出力する
	// 引数:	$title: タイトル文字列
	// 戻り値:	なし
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	private function echo_html_foot() {
		if ( $this->_output_footer_flag === true ) {
			// すでにフッターの出力が終わっていたら、何もせずに終了
			return 1;
		}
		
		// テンプレートを読み込む
		$html_line = $this->load_template($this->_template_foot);
		if ( $html_line === false ) {
			// テンプレートファイルが見つからない場合は、それっぽいデータを登録
			$this->echo_error_message($this->_error_template_footer_not_found);
			$this->echo_html_content("</body></html>\n");
			return 2;
		}
		
		// その他のスペシャルタグを置き換え
		$html_line = $this->replace_special_tags($html_line);
		
		// テンプレートを出力
		$this->echo_html_content($html_line);
		
		// フッター出力フラグを設定
		$this->_output_footer_flag = true;
		
		return 0;
	}


	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	echo_html_content
	// 機能:	コンテンツ登録
	// 引数:	$string: 出力したい HTML など（配列OK）
	// 戻り値:	なし
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	private function echo_html_content ( $string ) {
		// ----- 受け取った引数が配列の場合 -----------------------------------------------
		if ( is_array($string) === true ) {
			foreach ( $string as $line ) {
				$this->_output_html_array[] = $line;
			}
		}
		// ----- 受け取った引数が文字列の場合 ---------------------------------------------
		else {
			$this->_output_html_array[] = $string;
		}
	}


	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	replace_special_tags
	// 機能:	置き換える
	// 引数:	$subject: 置き換え対象の文字列（または配列）
	// 戻り値:	置き換え後の文字列（または配列）
	//        	置換パターン配列が空の場合は false
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	private function replace_special_tags ( $subject ) {
		
		// ----- 受け取った引数が配列の場合 ------------------------------------------------
		if ( is_array($subject) === true ) {
			foreach ( $subject as &$value ) {
				// 文字列にしてから、再帰呼び出し。
				$value = $this->replace_special_tags($value);
			}
		}
		// ----- 受け取った引数が文字列の場合 -----------------------------------------------
		else {
			// 置き換えパターンが空配列ではないことを確認
			if ( !empty($this->_replace_tags_c) ) {
				// 置き換えパターン配列のキーを検索タグ、要素を変換後の文字列として置換を実行
				foreach ($this->_replace_tags_c as $search => $replace) {
					$subject = str_replace($search, $replace, $subject);
				}
			}
			// 置き換えパターンが空の場合は false を返却します
			else {
				return false;
			}
		}
		
		return $subject;
	}
	
	
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	replace_special_chars
	// 機能:	HTMLのスペシャル文字などを置き換える
	// 引数:	$subject: 置き換え対象の文字列（または配列）
	// 戻り値:	置き換え後の文字列（または配列）
	//        	置換パターン配列が空の場合は false
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	private function replace_special_chars ( $subject ) {
		
		// ----- 受け取った引数が配列の場合 ------------------------------------------------
		if ( is_array($subject) === true ) {
			foreach ($subject as &$value) {
				// 文字列にしてから、再帰呼び出し
				$value = $this->replace_special_chars($value);
			}
		}
		// ----- 受け取った引数が文字列の場合 -----------------------------------------------
		else {
			$search = array('&','<','>');
			$replace = array('&amp;','&lt;','&gt;');

			$subject = str_replace($search, $replace, $subject);
		}
		
		return $subject;
	}
	
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	make_url_link
	// 機能:	指定された文字列からURLを検索し、<a>タグを使ったリンクを生成します
	// 引数:	$str: 文字列を指定する
	// 戻り値:	<a>タグを使ったリンクを含む文字列
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	private function make_url_link ( $str ) {
		
		// ----- リンクにできるURLを検索する ------------------------------------------------
		$url_start_needle = array("http://", "https://");
		foreach ( $url_start_needle as $needle ) {
			$url_start_pos = mb_strpos($str, $needle);
			if ( $url_start_pos !== FALSE ) {
				break;
			}
		}
		
		// 見つからなければ、そのままの文字列を返却する
		if ( $url_start_pos === FALSE ) {
			return $str;
		}
		
		// URL を切り取る
		$url_end_needle = array(" ", "　");
		foreach ( $url_end_needle as $needle ) {
			$url_end_pos = mb_strpos($str, $needle, $url_start_pos);
			if ( $url_end_pos !== FALSE ) {
				break;
			}
		}
		if ( $url_end_pos === FALSE ) {
			$url_string = mb_substr($str, $url_start_pos);
		}
		else {
			$url_string = mb_substr($str, $url_start_pos, $url_end_pos-$url_start_pos);
		}
		
		// <a タグを検索する
		$tag_start_pos = mb_strpos($str, "<a");
		
		// <a が見つかったら、</a> を検索する
		if ( $tag_start_pos !== FALSE ) {
			$tag_end_pos = mb_strpos($str, "</a>", $tag_start_pos);
		}
		
		// <a と </a> の両方が見つかったら、部分を切り取って、URLが含まれているか確認する
		if ( ($tag_start_pos !== FALSE) && ($tag_end_pos !== FALSE) ) {
			$tag_string = mb_substr($str, $tag_start_pos, $tag_end_pos-$tag_start_pos);
			if ( mb_strpos($tag_string, $url_string) !== FALSE ) {
				return $str;
			}
		}
		
		// ----- URL をリンクタグに置き換える -----------------------------------------------
		$new_string = "<a href=\"{$url_string}\" target=\"_blank\">LINK</a>";
		$str = str_replace($url_string, $new_string, $str);
		
		return $str;
	}
	
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	load_template
	// 機能:	HTMl テンプレートを読み込む
	// 引数:	$file: 読み込むファイル名
	// 戻り値:	成功: 読み込んだファイルを各行ごとに配列に格納したもの（最後に改行コードを含む）
	//        	失敗: ファイルが存在しない場合は false
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	private function load_template( $file ) {
		// ファイル名に template 保存用ディレクトリを追加
		$file = $this->_html_template_dir . $file;
		
		// ファイルを読み込む
		$load = @file($file);
		if ( $load === false ) {
			return false;
		}
		
		// 固定文字列変換用のタグを検索して変換する
		$load = $this->replace_special_tags($load);
		
		return $load;
	}

	
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	echo_error_message
	// 機能:	エラーメッセージを登録する
	// 引数:	$message: エラーメッセージ
	// 戻り値:	なし
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	private function echo_error_message ( $message ) {
		if ( strlen($message) !== 0 ) {
			$this->_error_message_array[] = $message;
			return strlen($message);
		}
		else {
			return FALSE;
		}
	}
	
	
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	array_insert
	// 機能:	配列を、引数で指定した位置に挿入する
	// 引数:	$array1:	挿入される配列
	// 			$array2:	挿入する配列
	// 			$key:		array1 の挿入位置を指定する 
	// 戻り値:	挿入が終わった後の配列
	//			指定された $key が数字以外の場合は、false
	// その他:	$key に数字以外のキーを指定すると、エラーになります
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	private function array_insert ( $array1, $array2, $key ) {
		// 指定されたキーが数字以外ならエラー
		if ( is_int($key) === FALSE ) {
			return FALSE;
		}
		// 指定されたキーがマイナス値でもエラー
		else if ( $key < 0 ) {
			return FALSE;
		}

		$array1_count = count($array1);
		$array2_count = count($array2);
		
		// $array1 が空配列
		if ( $array1_count == 0 ) {
			$return = $array2;
		}
		// 指定されたキーが $array1 の配列個数以上
		else if ( $key > $array1_count ) {
			$return = array_merge($array1, $array2);
		}
		// 指定されたキーが $array1 の配列要素未満
		else {
			
			// 挿入位置より後ろの要素と前の要素で配列を切る
			$return = array_slice($array1, 0, $key);
			$array1_after = array_slice($array1, $key+1);
			
			// 前の要素が入った配列に、挿入対象の配列をくっつける
			$return = array_merge($return, $array2);
			
			// さらに、配列の後半をくっつける
			$return = array_merge($return, $array1_after);

		}
		
		return $return;
	}
	
	
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	thumb_image
	// 機能:	サムネイル画像を作成する
	// 引数:	$number: 作成したい画像のログ番号
	// 戻り値:	成功: true
	//			失敗: false
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	public function thumb_image ( $number ) {
		
		// ----- ログファイルを読み込む --------------------------------------------------
		$this->load_log();
		
		// 対象のログ番号を探す
		$log_found_flag = false;
		foreach ($this->_log_array as $log_key => $log_value) {
			if ( $log_value['number'] == $number ) {
				$log_found_flag = true;
				break;
			}
		}
		
		// 対象のログ番号は見つからなかったため、失敗
		if ( $log_found_flag !== true ) {
			return false;
		}
		
		// 対象ログの mime_type を調べる
		/*
		if ( ($log_value['mime_type'] != 'image/jpeg') && ($log_value['mime_type'] != 'image/png') ) {
			// JPG でも PNG でもないなら、サムネイルは作成できない。
			echo "Debug: mime type not matched<br />\n";
			return false;
		}
		*/
		
		// ----- 画像を読み込む -----------------------------------------------------
		// ファイル名を設定
		$file_name = $this->_log_array[$log_key]['number'].'.'.$this->_log_array[$log_key]['extension'];
		
		// ファイルを相対位置で設定
		$file_url = $this->_up_dir_web.$file_name;
		
		// ファイルを読み込む
		$image_size = getimagesize($file_url);
		
		// 読み込んだファイルが jpeg か png ではないと GD が言ったら、失敗
		if ( ($image_size[2] != IMAGETYPE_JPEG) && ($image_size[2] != IMAGETYPE_PNG) ) {
			return false;
		}
		
		// 画像を開く
		switch ($image_size[2]) {
			case IMAGETYPE_JPEG:
				$src_image = imagecreatefromjpeg($file_url);
				break;

			case IMAGETYPE_PNG:
				
				// 画像オープン
				$src_image = imagecreatefrompng($file_url);
				
				break;

			default:
				// それ以外の時？ 上の if 文ですでに return されているはずだよ。
				return false;
				break;
		}
		
		// ----- 新しい画像サイズを計算する ------------------------------------------------
		
		// 縦 or 横の最大サイズを取得
		$max_size = ($image_size[0] > $image_size[1]) ? $image_size[0] : $image_size[1];
		
		// 最大サイズが設定値より大きい
		if ( $max_size > $this->_image_max ) {
			// 縮小倍率を計算
			$div = $this->_image_max / $max_size;
		}
		// 小さい場合はそのまま表示します
		else {
			$div = 1;
		}
		
		// 新しい画像サイズを計算
		$out_w = $image_size[0] * $div;
		$out_h = $image_size[1] * $div;
		
		// ----- 新しい画像イメージを作成する -----------------------------------------------
		$new_image = imagecreatetruecolor($out_w, $out_h);
		
		// PNG の場合はアルファチャンネルを有効にする
		if ( $src_image[2] == IMAGETYPE_PNG ) {
			// アルファチャンネルを有効にする 
			imageAlphaBlending($new_image, false);
			imageSaveAlpha($new_image, true);
			
			// 透過色を作る
			$transparent = imageColorAllocateAlpha($new_image, 0x00, 0x00, 0x00, 127);
			
			// 透過色で画像を埋める
			imageFill($new_image, 0, 0, $transparent);
		
		}
		
		// 新しい画像を元画像からコピーして作成
		imagecopyresampled($new_image, $src_image, 0, 0, 0, 0, $out_w, $out_h, $image_size[0], $image_size[1]);
			
		// 画像を出力する
		switch ( $image_size[2] ) {
			case IMAGETYPE_JPEG:
				header('Content-Type: '.image_type_to_mime_type(IMAGETYPE_JPEG));
				imagejpeg($new_image, NULL, 85);
				break;
			case IMAGETYPE_PNG:
				header('Content-Type: '.image_type_to_mime_type(IMAGETYPE_PNG));
				imagepng($new_image);
			default:
				// それ以外のパターンはすでに return 済み
				return false;
				break;
		}
		
		// 開いたイメージを閉じる
		imagedestroy($src_image);
		imagedestroy($new_image);
		
		exit;
	}
	
	
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	search_file_number
	// 機能:	ファイル番号を指定すると、ログ配列のキーを返却します
	// 引数:	$number ログ番号
	// 戻り値:	ログ配列のキー。失敗した場合はFALSE
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	private function search_file_number ( $number ) {
		// ログファイルを読み込む
		$this->load_log();
		
		// 対象の番号を持つログ行を探す
		$log_found_flag = false;
		foreach ( $this->_log_array as $log_key => $log_line ) {
			if ( $log_line["number"] == $number ) {
				// フラグセット
				$log_found_flag = true;
				break;
			}
		}
		
		if ( $log_found_flag === false ) {
			return false;
		}
		
		return $log_key;
	}
	

	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	// 関数名:	test
	// 機能:	テスト関数。好きに実装しやがれです。
	// 引数:
	// 戻り値:
	// ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ----- ------
	public function test (  ) {
		
		$this->echo_html_head("テスト");
		echo $this->make_url_link("http://www.pixiv.net/member_illust.php?mode=medium&illust_id=37712166");
		$this->echo_html_foot();
		
	}

}
