<?php
/* 
 * 首页
 */

class Controller_Index extends Controller_App
{

	/**
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		//共同Header调用
		$data['header'] = Request::forge('common/header')->execute()->response();
		//共同Footer调用
		$data['footer'] = Request::forge('common/footer')->execute()->response();

		//取得当前季节，切换form显示内容
		$datenow = date('m-d', time());
		if($datenow >= '02-03' && $datenow < '06-21') {
			$data['season'] = 'spring';
		} elseif($datenow >= '06-21' && $datenow < '08-07') {
			$data['season'] = 'summer';
		} elseif($datenow >= '08-07' && $datenow < '12-22') {
			$data['season'] = 'autumn';
		} else {
			$data['season'] = 'winter';
		}
		
		//View调用
		return Response::forge(View::forge($this->template . '/index', $data, false));
	}
	
}