<?php
/* 
 * 管理画面ホームページ
 */

class Controller_Admin_Index extends Controller_Admin_App
{

	/**
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		//共通ヘッダー取得
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		//View呼び出す
		return Response::forge(View::forge($this->template . '/admin/index', $data, false));
	}
	
}