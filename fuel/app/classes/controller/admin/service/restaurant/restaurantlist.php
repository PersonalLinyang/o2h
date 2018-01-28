<?php
/* 
 * 餐饮一览页
 */

class Controller_Admin_Service_Restaurant_Restaurantlist extends Controller_Admin_App
{

	/**
	 * 餐饮一览
	 * @access  public
	 * @return  Response
	 */
	public function action_index($page = 1)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		try {
			if(!is_numeric($page)) {
				//页数不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'sub_group', 10)) {
				//当前登陆用户不具备查看餐饮的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['success_message'] = '';
				$data['error_message'] = '';
				
				//获取自身用户ID
				$data['user_id_self'] = $_SESSION['login_user']['id'];
				//获取地区列表
				$data['area_list'] = Model_Area::GetAreaList(array('active_only' => true));
				//获取餐饮类型列表
				$data['restaurant_type_list'] = Model_RestaurantType::SelectRestaurantTypeList(array('active_only' => true));
				//是否具备餐饮编辑权限
				$data['edit_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 11);
				//是否具备其他用户所登陆的餐饮编辑权限
				$data['edit_other_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 4);
				//是否具备餐饮删除权限
				$data['delete_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 12);
				//是否具备其他用户所登陆的餐饮删除权限
				$data['delete_other_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 5);
				//是否具备批量导入餐饮信息权限
				$data['import_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 13);
				//是否具备导出餐饮信息权限
				$data['export_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 14);
				//是否具备餐饮类别管理权限
				$data['spot_type_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 15);
				
				//每页现实景点数
				$num_per_page = 20;
				//本页前后最大可链接页数
				$data['page_link_max'] = 3;
				
				//检索条件
				$data['select_name'] = isset($_GET['select_name']) ? preg_replace('/( |　)/', ' ', $_GET['select_name']) : '';
				$data['select_status'] = isset($_GET['select_status']) ? $_GET['select_status'] : array();
				$data['select_area'] = isset($_GET['select_area']) ? $_GET['select_area'] : array();
				$data['select_restaurant_type'] = isset($_GET['select_restaurant_type']) ? $_GET['select_restaurant_type'] : array();
				$data['select_price_min'] = isset($_GET['select_price_min']) ? $_GET['select_price_min'] : '';
				$data['select_price_max'] = isset($_GET['select_price_max']) ? $_GET['select_price_max'] : '';
				$data['sort_column'] = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'created_at';
				$data['sort_method'] = isset($_GET['sort_method']) ? $_GET['sort_method'] : 'desc';
				$data['get_params'] = count($_GET) ? '?' . http_build_query($_GET) : '';
				
				//显示结果默认值
				$data['restaurant_list'] = array();
				$data['restaurant_count'] = 0;
				$data['start_number'] = 0;
				$data['end_number'] = 0;
				$data['page_number'] = 1;
				$data['page'] = $page;
				
				//获取餐饮信息
				$params_select = array(
					'hotel_name' => $data['select_name'] ? explode(' ', $data['select_name']) : array(),
					'hotel_status' => $data['select_status'],
					'hotel_area' => $data['select_area'],
					'hotel_type' => $data['select_restaurant_type'],
					'price_min' => $data['select_price_min'],
					'price_max' => $data['select_price_max'],
					'sort_column' => $data['sort_column'],
					'sort_method' => $data['sort_method'],
					'page' => $page,
					'num_per_page' => $num_per_page,
					'active_only' => true,
				);
				
				$result_select = Model_Restaurant::SelectRestaurantList($params_select);
				
				//整理显示内容
				if($result_select) {
					$restaurant_count = $result_select['restaurant_count'];
					$data['restaurant_count'] = $restaurant_count;
					$data['restaurant_list'] = $result_select['restaurant_list'];
					$data['start_number'] = $result_select['start_number'];
					$data['end_number'] = $result_select['end_number'];
					if($restaurant_count > $num_per_page) {
						$data['page_number'] = ceil($restaurant_count/$num_per_page);
					}
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
				return Response::forge(View::forge($this->template . '/admin/service/restaurant/restaurant_list', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}