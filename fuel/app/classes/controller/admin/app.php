<?php
/* 
 * 前サイトページの共通親
 */

class Controller_Admin_App extends Controller 
{

	//デフォルトでPCのthemeを使う
	public $template = 'pc';
	
	/**
	 *
	 * @access  public
	 */
	public function before() {

		$ua = $_SERVER['HTTP_USER_AGENT'];
		
		//PC、SPを判別してSPまたはFPならthemeをきりかえる
		if ((strpos($ua, 'iPhone') !== false)
	    || (strpos($ua, 'Windows Phone') !== false)
	    || (strpos($ua, 'DoCoMo') !== false)
	    || (strpos($ua, 'KDDI') !== false)
	    || (strpos($ua, 'SoftBank') !== false)
	    || (strpos($ua, 'Vodafone') !== false)
	    || (strpos($ua, 'J-PHONE') !== false)
	    || (strpos($ua, 'Android') !== false && strpos($ua, 'Mobile') !== false)) {
			$this->template = 'sp';
		}

		//時間を日本時間に調整
		date_default_timezone_set('Asia/Tokyo');

		
		session_start();
		$data = array();
		
		//未ログインとときログインページに遷移
		if(!isset($_SESSION['login_user'])){
			header( 'Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/login.php' );
			exit;
		}
		
	}

}