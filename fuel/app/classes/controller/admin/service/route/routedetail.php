<?php
/* 
 * 旅游路线详细信息页
 */

class Controller_Admin_Service_Route_Routedetail extends Controller_Admin_App
{

	/**
	 * 旅游路线详细信息页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($route_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		try {
			if(!is_numeric($route_id)) {
				//旅游路线ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'sub_group', 12)) {
				//当前登陆用户不具备查看旅游路线的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['success_message'] = '';
				$data['error_message'] = '';
				
				//获取返回一览页时的一览页URL
				$data['route_list_url'] = '/admin/route_list/';
				if(isset($_SERVER['HTTP_REFERER'])) {
					if(strstr($_SERVER['HTTP_REFERER'], 'admin/route_list')) {
						//通过一览页链接进入
						$data['route_list_url'] = $_SERVER['HTTP_REFERER'];
					} elseif(strstr($_SERVER['HTTP_REFERER'], 'admin/modify_route/' . $route_id) || strstr($_SERVER['HTTP_REFERER'], 'admin/route_detail/' . $route_id)) {
						if(isset($_SESSION['route_list_url_detail'])) {
							$data['route_list_url'] = $_SESSION['route_list_url_detail'];
						}
					}
				}
				//暂时保留一览页URL
				$_SESSION['route_list_url_detail'] = $data['route_list_url'];
				
				//获取旅游路线信息
				$route = Model_Route::SelectRoute(array('route_id' => $route_id, 'active_only' => true));
				
				if(!$route) {
					//不存在该ID的旅游路线
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
				
				//主图
				if(file_exists(DOCROOT . 'assets/img/pc/upload/route/' . $route_id . '/main.jpg')) {
					$route['main_image'] = '/assets/img/pc/upload/route/' . $route_id . '/main.jpg';
				} else {
					$route['main_image'] = '';
				}
				
				//成本
				$route_base_cost = empty($route['route_base_cost']) ? 0 : $route['route_base_cost'];
				$route_traffic_cost = empty($route['route_traffic_cost']) ? 0 : $route['route_traffic_cost'];
				$route_parking_cost = empty($route['route_parking_cost']) ? 0 : $route['route_parking_cost'];
				if(is_numeric($route_base_cost) && is_numeric($route_traffic_cost) && is_numeric($route_parking_cost)) {
					$route['route_total_cost'] = intval($route_base_cost) + intval($route_traffic_cost) + intval($route_parking_cost);
				} else {
					$route['route_total_cost'] = '';
				}
				
				$data['route_info'] = $route;
				
				if($route['created_by'] == $_SESSION['login_user']['id']) {
					//是否具备旅游路线编辑权限
					$data['edit_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 22);
				} else {
					//是否具备修改其他用户所登陆的旅游路线信息权限
					$data['edit_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 8);
				}
				
				//输出提示信息
				if(isset($_SESSION['add_route_success'])) {
					$data['success_message'] = '旅游路线添加成功';
					unset($_SESSION['add_route_success']);
				}
				if(isset($_SESSION['modify_route_success'])) {
					$data['success_message'] = '旅游路线修改成功';
					unset($_SESSION['modify_route_success']);
				}
				if(isset($_SESSION['modify_route_status_success'])) {
					$data['success_message'] = '旅游路线公开状态更新成功';
					unset($_SESSION['modify_route_status_success']);
				}
				if(isset($_SESSION['modify_route_status_error'])) {
					$data['error_message'] = '旅游路线公开状态更新失敗 请重新尝试';
					unset($_SESSION['modify_route_status_error']);
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/route/route_detail', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}