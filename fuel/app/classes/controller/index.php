<?php
/* 
 * ホームページ
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
		
		//共通ヘッダー取得
		$data['header'] = Request::forge('common/header')->execute()->response();
		//共通フッター取得
		$data['footer'] = Request::forge('common/footer')->execute()->response();
		
		//View呼び出す
		return Response::forge(View::forge($this->template . '/index', $data, false));
	}
	
}