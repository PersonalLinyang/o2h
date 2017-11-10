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
	public function action_index($page = null)
	{
		$data = array();
		
		if(!is_numeric($page)) {
			$page = 1;
		}
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['success_message'] = '';
			$data['error_message'] = '';
			$data['area_list'] = Model_Area::GetAreaListAll();
			$data['spot_type_list'] = Model_SpotType::GetSpotTypeListAll();
			$data['spot_list'] = array();
			$data['spot_count'] = 0;
			$data['start_number'] = 0;
			$data['end_number'] = 0;
			$data['page'] = $page;
			$data['page_number'] = 1;
			$num_per_page = 20;
			
			$spot_count = Model_Spot::GetSpotTotalCount();
			if($spot_count) {
				$data['spot_count'] = $spot_count;
				
				$params = array(
					'page' => $page,
					'num_per_page' => $num_per_page,
				);
				$result_select = Model_Spot::SelectSpotList($params);
				if($result_select) {
					$data['spot_list'] = $result_select['spot_list'];
					$data['start_number'] = $result_select['start_number'];
					$data['end_number'] = $result_select['end_number'];
				}
				
				if($spot_count > $num_per_page) {
					$data['page_number'] = ceil($spot_count/$num_per_page);
				}
			}

			//调用View
			return Response::forge(View::forge($this->template . '/admin/service/spot_list', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}

}