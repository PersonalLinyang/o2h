<?php
/* 
 * 管理画面ホームページ
 */

class Controller_Admin_Userlist extends Controller_Admin_App
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
		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			return Response::forge(View::forge($this->template . '/admin/user_list', $data, false));
		} else {
			return Response::forge(View::forge($this->template . '/admin/permission_error', $data, false));
		}
	}
	
}