<?php
/* 
 * 路线详细信息页
 */

class Controller_Admin_Service_Routedetail extends Controller_Admin_App
{

	/**
	 * 路线详细信息页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($route_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['success_message'] = '';
			$data['error_message'] = '';
			
			$route_info = Model_Route::SelectRouteInfoByRouteId($route_id);
			//主图
			if(file_exists(DOCROOT . 'assets/img/pc/upload/route/' . $route_id . '/main.jpg')) {
				$route_info['main_image'] = '/assets/img/pc/upload/route/' . $route_id . '/main.jpg';
			} else {
				$route_info['main_image'] = '';
			}
			//成本
			if(isset($route_info['route_base_cost']) && isset($route_info['route_traffic_cost']) && isset($route_info['route_parking_cost'])) {
				$route_base_cost = 0;
				$route_traffic_cost = 0;
				$route_parking_cost = 0;
				if($route_info['route_base_cost'] != '') {
					$route_base_cost = $route_info['route_base_cost'];
				}
				if($route_info['route_traffic_cost'] != '') {
					$route_traffic_cost = $route_info['route_traffic_cost'];
				}
				if($route_info['route_parking_cost'] != '') {
					$route_parking_cost = $route_info['route_parking_cost'];
				}
				if(is_numeric($route_base_cost) && is_numeric($route_traffic_cost) && is_numeric($route_parking_cost)) {
					$route_info['route_total_cost'] = intval($route_base_cost) + intval($route_traffic_cost) + intval($route_parking_cost);
				} else {
					$route_info['route_total_cost'] = '';
				}
			}
			
			if($route_info) {
				$data['route_info'] = $route_info;
				
				if(isset($_SESSION['modify_route_status_success'])) {
					$data['success_message'] = '路线公开状态更新成功';
					unset($_SESSION['modify_route_status_success']);
				}
				if(isset($_SESSION['modify_route_status_error'])) {
					$data['error_message'] = '路线公开状态更新失敗 请重新尝试';
					unset($_SESSION['modify_route_status_error']);
				}
				if(isset($_SESSION['add_route_success'])) {
					$data['success_message'] = '路线添加成功';
					unset($_SESSION['add_route_success']);
				}
				if(isset($_SESSION['modify_route_success'])) {
					$data['success_message'] = '路线信息修改成功';
					unset($_SESSION['modify_route_success']);
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/route_detail', $data, false));
			} else {
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			}
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}
	
	/**
	 * 路线公开状态更新
	 * @access  public
	 * @return  Response
	 */
	public function action_modifyroutestatus($param = null)
	{
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['page'], $_POST['modify_id'], $_POST['modify_value'])) {
		if(isset($_POST['page'], $_POST['modify_id'], $_POST['modify_value'])) {
			if($_POST['page'] == 'route_detail') {
				//删除信息检查
				switch($_POST['modify_value']) {
					case 'publish':
						$route_status = '1';
						break;
					case 'protected':
						$route_status = '0';
						break;
					default:
						$route_status = '';
						break;
				}
				$params_update = array(
					'route_id' => $_POST['modify_id'],
					'route_status' => $route_status,
				);
				$result_check = Model_Route::CheckUpdateRouteStatusById($params_update);
				if($result_check['result']) {
					//数据删除
					$result_update = Model_Route::UpdateRouteStatusById($params_update);
					
					if($result_update) {
						$_SESSION['modify_route_status_success'] = true;
						header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/route_detail/' . $_POST['modify_id'] . '/');
						exit;
					}
				}
			}
		}
		$_SESSION['modify_route_status_error'] = true;
		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/route_detail/' . $_POST['modify_id'] . '/');
		exit;
	}

}