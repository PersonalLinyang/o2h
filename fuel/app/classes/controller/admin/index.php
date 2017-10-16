<?php
/* 
 * 首页
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
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		//调用View
		return Response::forge(View::forge($this->template . '/admin/index', $data, false));
	}
	
}