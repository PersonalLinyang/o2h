<?php
/* 
 * 注文ページ
 */

class Controller_Admin_Order_Index extends Controller_App
{

	/**
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		session_start();
		$data = array();
		
		//未ログインとときログインページに遷移
		if(!isset($_SESSION['login_user_id'])) {
			header( 'Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/login.php' );
			exit;
		}
		//普通店員でログインしてないときエラーページを表示
		if($_SESSION['login_user_type'] != USER_TYPE_STAFF) {
			$data_error = array();
			$data_error['error_message'] = 'このユーザーはこのページを閲覧する権限を持っていません';
			return Response::forge(View::forge($this->template . '/admin/error', $data_error, false));
		}
		
		//共通ヘッダー取得
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		//View呼び出す
		return Response::forge(View::forge($this->template . '/admin/order/index_' . $_SESSION['login_agent_id'], $data, false));
	}
	
}