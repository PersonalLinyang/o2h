<?php
/* 
 * 路线一览页
 */

class Controller_Admin_Service_Routelist extends Controller_Admin_App
{

	/**
	 * 路线一览
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
			$data['route_list'] = array();
			$data['route_count'] = 0;
			$data['start_number'] = 0;
			$data['end_number'] = 0;
			$data['page'] = $page;
			$data['page_number'] = 1;
			//本页前后最大可链接页数
			$data['page_link_max'] = 3;
			$data['select_name'] = '';
			$data['select_status'] = isset($_GET['select_status']) ? $_GET['select_status'] : array();
			$data['select_price_min'] = isset($_GET['select_price_min']) ? $_GET['select_price_min'] : '';
			$data['select_price_max'] = isset($_GET['select_price_max']) ? $_GET['select_price_max'] : '';
			$data['select_base_cost_min'] = isset($_GET['select_base_cost_min']) ? $_GET['select_base_cost_min'] : '';
			$data['select_base_cost_max'] = isset($_GET['select_base_cost_max']) ? $_GET['select_base_cost_max'] : '';
			$data['select_traffic_cost_min'] = isset($_GET['select_traffic_cost_min']) ? $_GET['select_traffic_cost_min'] : '';
			$data['select_traffic_cost_max'] = isset($_GET['select_traffic_cost_max']) ? $_GET['select_traffic_cost_max'] : '';
			$data['select_parking_cost_min'] = isset($_GET['select_parking_cost_min']) ? $_GET['select_parking_cost_min'] : '';
			$data['select_parking_cost_max'] = isset($_GET['select_parking_cost_max']) ? $_GET['select_parking_cost_max'] : '';
			$data['select_total_cost_min'] = isset($_GET['select_total_cost_min']) ? $_GET['select_total_cost_min'] : '';
			$data['select_total_cost_max'] = isset($_GET['select_total_cost_max']) ? $_GET['select_total_cost_max'] : '';
			$data['sort_column'] = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'created_at';
			$data['sort_method'] = isset($_GET['sort_method']) ? $_GET['sort_method'] : 'desc';
			$data['get_params'] = isset($_GET) ? '?' . http_build_query($_GET) : '';
			$route_count = 0;
			$num_per_page = 20;

			$route_name_list = array();
			if(isset($_GET['select_name'])) {
				$route_name_list_tmp = explode(' ', $_GET['select_name']);
				foreach($route_name_list_tmp as $route_name_tmp) {
					$route_name_list = array_merge($route_name_list, explode('　', $route_name_tmp));
				}
			}
			$data['select_name'] = implode(' ', $route_name_list);
			
			$params = array(
				'route_name' => $route_name_list,
				'route_status' => isset($_GET['select_status']) ? $_GET['select_status'] : array(),
				'price_min' => isset($_GET['select_price_min']) ? $_GET['select_price_min'] : '',
				'price_max' => isset($_GET['select_price_max']) ? $_GET['select_price_max'] : '',
				'base_cost_min' => isset($_GET['select_base_cost_min']) ? $_GET['select_base_cost_min'] : '',
				'base_cost_max' => isset($_GET['select_base_cost_max']) ? $_GET['select_base_cost_max'] : '',
				'traffic_cost_min' => isset($_GET['select_traffic_cost_min']) ? $_GET['select_traffic_cost_min'] : '',
				'traffic_cost_max' => isset($_GET['select_traffic_cost_max']) ? $_GET['select_traffic_cost_max'] : '',
				'parking_cost_min' => isset($_GET['select_parking_cost_min']) ? $_GET['select_parking_cost_min'] : '',
				'parking_cost_max' => isset($_GET['select_parking_cost_max']) ? $_GET['select_parking_cost_max'] : '',
				'total_cost_min' => isset($_GET['select_total_cost_min']) ? $_GET['select_total_cost_min'] : '',
				'total_cost_max' => isset($_GET['select_total_cost_max']) ? $_GET['select_total_cost_max'] : '',
				'sort_column' => isset($_GET['sort_column']) ? $_GET['sort_column'] : 'created_at',
				'sort_method' => isset($_GET['sort_method']) ? $_GET['sort_method'] : 'desc',
				'page' => $page,
				'num_per_page' => $num_per_page,
			);
			$result_select = Model_Route::SelectRouteList($params);
			if($result_select) {
				$route_count = $result_select['route_count'];
				$data['route_count'] = $route_count;
				$data['route_list'] = $result_select['route_list'];
				$data['start_number'] = $result_select['start_number'];
				$data['end_number'] = $result_select['end_number'];
			}
			
			if($route_count > $num_per_page) {
				$data['page_number'] = ceil($route_count/$num_per_page);
			}
			
			if(isset($_SESSION['delete_route_success'])) {
				$data['success_message'] = '路线削除成功';
				unset($_SESSION['delete_route_success']);
			}
			if(isset($_SESSION['delete_route_error'])) {
				$data['error_message'] = '路线削除失敗';
				unset($_SESSION['delete_route_error']);
			}
			
			if(isset($_SESSION['delete_checked_route_success'])) {
				$data['success_message'] = '选中路线削除成功';
				unset($_SESSION['delete_checked_route_success']);
			}
			if(isset($_SESSION['delete_checked_route_error'])) {
				$data['error_message'] = '选中路线削除失敗';
				unset($_SESSION['delete_checked_route_error']);
			}

			//调用View
			return Response::forge(View::forge($this->template . '/admin/service/route_list', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}
	
	/**
	 * 削除路线
	 * @access  public
	 * @return  Response
	 */
	public function action_deleteroute($param = null)
	{
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['delete_id'], $_POST['page'])) {
			if($_POST['page'] == 'route_list') {
				//删除信息检查
				$result_check = Model_Route::CheckDeleteRouteById($_POST['delete_id']);
				if($result_check['result']) {
					//数据删除
					$result_delete = Model_Route::DeleteRouteById($_POST['delete_id']);
					
					if($result_delete) {
						$_SESSION['delete_route_success'] = true;
						header('Location: ' . $_SERVER['HTTP_REFERER']);
						exit;
					}
				}
			}
//		}
		$_SESSION['delete_route_error'] = true;
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		exit;
	}

	/**
	 * 削除所有选中路线
	 * @access  public
	 * @return  Response
	 */
	public function action_deletecheckedroute($param = null)
	{
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['delete_id'], $_POST['page'])) {
			if($_POST['page'] == 'route_list') {
				//删除信息检查
				$result_check = Model_Route::CheckDeleteRouteByIdList($_POST['delete_id_checked']);
				if($result_check['result']) {
					//数据删除
					$result_delete = Model_Route::DeleteRouteByIdList($_POST['delete_id_checked']);
					
					if($result_delete) {
						$_SESSION['delete_checked_route_success'] = true;
						header('Location: ' . $_SERVER['HTTP_REFERER']);
						exit;
					}
				}
			}
//		}
		$_SESSION['delete_checked_route_error'] = true;
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		exit;
	}


}