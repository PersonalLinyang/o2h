<?php
/* 
 * 登陆页
 */

class Controller_Admin_Login extends Controller_App
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
		
		//登陆信息确认
		if(isset($_POST['user_email']) && isset($_POST['user_password'])) {
			if(!$_POST['user_email']) {
				$data['error_message'] = '※请输入邮箱※';
			} elseif(!$_POST['user_password']) {
				$data['error_message'] = '※请输入密码※';
			} else {
				$login_user = Model_User::Login($_POST['user_email'], $_POST['user_password']);
				
				if($login_user) {
					$_SESSION['login_user']['id'] = $login_user['user_id'];
					$_SESSION['login_user']['name'] = $login_user['user_name'];
					
					header( 'Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/' );
					exit;
				} else {
					$data['user_email'] = $_POST['user_email'];
					$data['error_message'] = '※邮箱或密码输入错误※';
				}
			}
		}
		
		//调用View
		return Response::forge(View::forge($this->template . '/admin/login', $data, false));
	}
	
}