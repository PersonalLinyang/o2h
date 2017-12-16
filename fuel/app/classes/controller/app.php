<?php
/* 
 * 宣传系统通用Controller父类
 */

class Controller_App extends Controller 
{

	//默认使用PC式样
	public $template = 'pc';
	
	/**
	 *
	 * @access  public
	 */
	public function before() {

		$ua = $_SERVER['HTTP_USER_AGENT'];
		
		//PC、SP判定并切换template
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

		//调整时间
		date_default_timezone_set('PRC');
		
	}

}