<?php
/* 
 * 共用Header
 */

class Controller_Admin_Common_Header extends Controller_Admin_App
{

	/*
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		$data = array();
		
		//退出登陆
		if(isset($_POST['logout'])) {
			unset($_SESSION['login_user']);
			header( 'Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/login.php' );
			exit;
		}
		
		$data['login_user_permission'] = Model_Permission::SelectPermissionList(array('user_id' => $_SESSION['login_user']['id']));
		
		return Response::forge(View::forge($this->template . '/admin/common/header', $data, false));
	}
	
}