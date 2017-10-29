<?php
/* 
 * 管理系统共用父Controller
 */

class Controller_Admin_App extends Controller 
{

	//默认为PC
	public $template = 'pc';
	
	/**
	 *
	 * @access  public
	 */
	public function before() {

		$ua = $_SERVER['HTTP_USER_AGENT'];
		
		//根据登陆终端切换显示内容
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

		//日本时间
		date_default_timezone_set('Asia/Tokyo');

		
		session_start();
		$data = array();
		
		//未登陆时向登陆页跳转
		if(!isset($_SESSION['login_user'])){
			header( 'Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/login.php' );
			exit;
		}
		
	}

}