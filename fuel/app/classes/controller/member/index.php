<?php
/* 
 * 个人专属首页
 */

class Controller_Member_Index extends Controller_App
{

	/**
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();

		if(!isset($_SESSION['login_member'])) {
			header( 'Location: //' . $_SERVER['HTTP_HOST'] . '/member/login/' );
			exit;
		}
		
		//共同Header调用
		$data['header'] = Request::forge('common/header')->execute()->response();
		//共同Footer调用
		$data['footer'] = Request::forge('common/footer')->execute()->response();
		
		//View调用
		return Response::forge(View::forge($this->template . '/member/index', $data, false));
	}
	
}