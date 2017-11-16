<?php
/* 
 * 餐饮一览页
 */

class Controller_Admin_Service_Restaurantlist extends Controller_Admin_App
{

	/**
	 * 餐饮一览
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
			$data['restaurant_type_list'] = Model_RestaurantType::GetRestaurantTypeListAll();
			$data['restaurant_list'] = array();
			$data['restaurant_count'] = 0;
			$data['start_number'] = 0;
			$data['end_number'] = 0;
			$data['page'] = $page;
			$data['page_number'] = 1;
			//本页前后最大可链接页数
			$data['page_link_max'] = 3;
			$data['select_name'] = '';
			$data['select_status'] = isset($_GET['select_status']) ? $_GET['select_status'] : array();
			$data['select_area'] = isset($_GET['select_area']) ? $_GET['select_area'] : array();
			$data['select_restaurant_type'] = isset($_GET['select_restaurant_type']) ? $_GET['select_restaurant_type'] : array();
			$data['select_price_min'] = isset($_GET['select_price_min']) ? $_GET['select_price_min'] : '';
			$data['select_price_max'] = isset($_GET['select_price_max']) ? $_GET['select_price_max'] : '';
			$data['sort_column'] = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'created_at';
			$data['sort_method'] = isset($_GET['sort_method']) ? $_GET['sort_method'] : 'desc';
			$data['get_params'] = isset($_GET) ? '?' . http_build_query($_GET) : '';
			$restaurant_count = 0;
			$num_per_page = 20;

			$restaurant_name_list = array();
			if(isset($_GET['select_name'])) {
				$restaurant_name_list_tmp = explode(' ', $_GET['select_name']);
				foreach($restaurant_name_list_tmp as $restaurant_name_tmp) {
					$restaurant_name_list = array_merge($restaurant_name_list, explode('　', $restaurant_name_tmp));
				}
			}
			$data['select_name'] = implode(' ', $restaurant_name_list);
			
			$params = array(
				'restaurant_name' => $restaurant_name_list,
				'restaurant_status' => isset($_GET['select_status']) ? $_GET['select_status'] : array(),
				'restaurant_area' => isset($_GET['select_area']) ? $_GET['select_area'] : array(),
				'restaurant_type' => isset($_GET['select_restaurant_type']) ? $_GET['select_restaurant_type'] : array(),
				'price_min' => isset($_GET['select_price_min']) ? $_GET['select_price_min'] : '',
				'price_max' => isset($_GET['select_price_max']) ? $_GET['select_price_max'] : '',
				'sort_column' => isset($_GET['sort_column']) ? $_GET['sort_column'] : 'created_at',
				'sort_method' => isset($_GET['sort_method']) ? $_GET['sort_method'] : 'desc',
				'page' => $page,
				'num_per_page' => $num_per_page,
			);
			$result_select = Model_Restaurant::SelectRestaurantList($params);
			if($result_select) {
				$restaurant_count = $result_select['restaurant_count'];
				$data['restaurant_count'] = $restaurant_count;
				$data['restaurant_list'] = $result_select['restaurant_list'];
				$data['start_number'] = $result_select['start_number'];
				$data['end_number'] = $result_select['end_number'];
			}
			
			if($restaurant_count > $num_per_page) {
				$data['page_number'] = ceil($restaurant_count/$num_per_page);
			}
			
			if(isset($_SESSION['delete_restaurant_success'])) {
				$data['success_message'] = '餐饮削除成功';
				unset($_SESSION['delete_restaurant_success']);
			}
			if(isset($_SESSION['delete_restaurant_error'])) {
				$data['error_message'] = '餐饮削除失敗';
				unset($_SESSION['delete_restaurant_error']);
			}
			
			if(isset($_SESSION['delete_checked_restaurant_success'])) {
				$data['success_message'] = '选中餐饮削除成功';
				unset($_SESSION['delete_checked_restaurant_success']);
			}
			if(isset($_SESSION['delete_checked_restaurant_error'])) {
				$data['error_message'] = '选中餐饮削除失敗';
				unset($_SESSION['delete_checked_restaurant_error']);
			}

			//调用View
			return Response::forge(View::forge($this->template . '/admin/service/restaurant_list', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}
	
	/**
	 * 削除餐饮
	 * @access  public
	 * @return  Response
	 */
	public function action_deleterestaurant($param = null)
	{
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['delete_id'], $_POST['page'])) {
			if($_POST['page'] == 'restaurant_list') {
				//删除信息检查
				$result_check = Model_Restaurant::CheckDeleteRestaurantById($_POST['delete_id']);
				if($result_check['result']) {
					//数据删除
					$result_delete = Model_Restaurant::DeleteRestaurantById($_POST['delete_id']);
					
					if($result_delete) {
						$_SESSION['delete_restaurant_success'] = true;
						header('Location: ' . $_SERVER['HTTP_REFERER']);
						exit;
					}
				}
			}
//		}
		$_SESSION['delete_restaurant_error'] = true;
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		exit;
	}

	/**
	 * 削除所有选中餐饮
	 * @access  public
	 * @return  Response
	 */
	public function action_deletecheckedrestaurant($param = null)
	{
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['delete_id'], $_POST['page'])) {
			if($_POST['page'] == 'restaurant_list') {
				//删除信息检查
				$result_check = Model_Restaurant::CheckDeleteRestaurantByIdList($_POST['delete_id_checked']);
				if($result_check['result']) {
					//数据删除
					$result_delete = Model_Restaurant::DeleteRestaurantByIdList($_POST['delete_id_checked']);
					
					if($result_delete) {
						$_SESSION['delete_checked_restaurant_success'] = true;
						header('Location: ' . $_SERVER['HTTP_REFERER']);
						exit;
					}
				}
			}
//		}
		$_SESSION['delete_checked_restaurant_error'] = true;
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		exit;
	}


}