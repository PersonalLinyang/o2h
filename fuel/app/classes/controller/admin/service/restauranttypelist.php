<?php
/* 
 * 餐饮类别一览页
 */

class Controller_Admin_Service_Restauranttypelist extends Controller_Admin_App
{

	/**
	 * 餐饮类别一览
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
			$data['restaurant_type_list'] = Model_RestaurantType::GetRestaurantTypeListAll();
			
			if(isset($_SESSION['add_restaurant_type_success'])) {
				$data['success_message'] = '餐饮类别添加成功';
				unset($_SESSION['add_restaurant_type_success']);
			}
			if(isset($_SESSION['modify_restaurant_type_success'])) {
				$data['success_message'] = '餐饮类别名称修改成功';
				unset($_SESSION['modify_restaurant_type_success']);
			}
			if(isset($_SESSION['delete_restaurant_type_success'])) {
				$data['success_message'] = '餐饮类别削除成功';
				unset($_SESSION['delete_restaurant_type_success']);
			}
			if(isset($_SESSION['delete_restaurant_type_error'])) {
				$data['error_message'] = '餐饮类别削除失敗';
				unset($_SESSION['delete_restaurant_type_error']);
			}

			//调用View
			return Response::forge(View::forge($this->template . '/admin/service/restaurant_type_list', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}
	
	/**
	 * 削除餐饮类别
	 * @access  public
	 * @return  Response
	 */
	public function action_deleterestauranttype($param = null)
	{
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['delete_id'], $_POST['page'])) {
			if($_POST['page'] == 'restaurant_type_list') {
				//删除信息检查
				$result_check = Model_RestaurantType::CheckDeleteRestaurantTypeById($_POST['delete_id']);
				if($result_check['result']) {
					//数据删除
					$result_delete = Model_RestaurantType::DeleteRestaurantTypeById($_POST['delete_id']);
					
					if($result_delete) {
						$_SESSION['delete_restaurant_type_success'] = true;
						header('Location: ' . $_SERVER['HTTP_REFERER']);
						exit;
					}
				}
			}
//		}
		$_SESSION['delete_restaurant_type_error'] = true;
		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/restaurant_type_list/');
		exit;
	}

}