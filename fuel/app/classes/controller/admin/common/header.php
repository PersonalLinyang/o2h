<?php
/* 
 * 共通パーツ - ヘッダー
 */

class Controller_Admin_Common_Header extends Controller_App
{

	/*
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		$data = array();
		
		//ログアウト
		if(isset($_POST['logout'])) {
			unset($_SESSION['login_user']);
			header( 'Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/login.php' );
			exit;
		}

		$data['login_user_permission'] = $_SESSION['login_user']['permission'];
		
		return Response::forge(View::forge($this->template . '/admin/common/header', $data, false));
	}
	
}