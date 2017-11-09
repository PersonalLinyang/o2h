<?php
/* 
 * 系统权限管理页
 */

class Controller_Admin_Service_Spotlist extends Controller_Admin_App
{

	/**
	 * 系统权限一览
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['success_message'] = '';
			$data['error_message'] = '';
			$data['area_list'] = Model_Area::GetAreaListAll();
			$data['spot_type_list'] = Model_SpotType::GetSpotTypeListAll();
			$data['spot_list'] = '';
			$data['spot_count'] = '';

			$data['spot_list'] = Model_Spot::SelectSpotList($param);

			//调用View
			return Response::forge(View::forge($this->template . '/admin/service/spot_list', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}

}