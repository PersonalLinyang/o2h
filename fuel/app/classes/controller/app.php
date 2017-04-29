<?php
/* 
 * 前サイトページの共通親
 */

class Controller_App extends Controller 
{

	//デフォルトでPCのthemeを使う
	public $template = 'pc';
	//デフォルトで中国語を使う
	public $language = 'cn';
	
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

		//使う言語を判別してthemeを切り替える
		if (isset($_GET['language'])) {
			switch($_GET['language']) {
				case 'ja' :
					//日本
					$this->language = 'ja';
					break;
//				case 'tw' :
//					//中国語(繁体)
//					$this->language = 'tw';
//					break;
				default :
					//中国語(簡体)
					$this->language = 'cn';
					break;
			}
		}
		
	}

}