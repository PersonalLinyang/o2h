<?php
/* 
 * 餐饮详细信息页
 */

class Controller_Admin_Service_Restaurantdetail extends Controller_Admin_App
{

	/**
	 * 餐饮详细信息页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($restaurant_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['success_message'] = '';
			$data['error_message'] = '';
			
			$restaurant_info = Model_Restaurant::SelectRestaurantInfoByRestaurantId($restaurant_id);
			
			if($restaurant_info) {
				$data['restaurant_info'] = $restaurant_info;
				
				if(isset($_SESSION['modify_restaurant_status_success'])) {
					$data['success_message'] = '餐饮公开状态更新成功';
					unset($_SESSION['modify_restaurant_status_success']);
				}
				if(isset($_SESSION['modify_restaurant_status_error'])) {
					$data['error_message'] = '餐饮公开状态更新失敗 请重新尝试';
					unset($_SESSION['modify_restaurant_status_error']);
				}
				if(isset($_SESSION['add_restaurant_success'])) {
					$data['success_message'] = '餐饮添加成功';
					unset($_SESSION['add_restaurant_success']);
				}
				if(isset($_SESSION['modify_restaurant_success'])) {
					$data['success_message'] = '餐饮信息修改成功';
					unset($_SESSION['modify_restaurant_success']);
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/restaurant_detail', $data, false));
			} else {
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			}
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}
	
	/**
	 * 餐饮公开状态更新
	 * @access  public
	 * @return  Response
	 */
	public function action_modifyrestaurantstatus($param = null)
	{
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['page'], $_POST['modify_id'], $_POST['modify_value'])) {
		if(isset($_POST['page'], $_POST['modify_id'], $_POST['modify_value'])) {
			if($_POST['page'] == 'restaurant_detail') {
				//删除信息检查
				switch($_POST['modify_value']) {
					case 'publish':
						$restaurant_status = '1';
						break;
					case 'protected':
						$restaurant_status = '0';
						break;
					default:
						$restaurant_status = '';
						break;
				}
				$params_update = array(
					'restaurant_id' => $_POST['modify_id'],
					'restaurant_status' => $restaurant_status,
				);
				$result_check = Model_Restaurant::CheckUpdateRestaurantStatusById($params_update);
				if($result_check['result']) {
					//数据删除
					$result_update = Model_Restaurant::UpdateRestaurantStatusById($params_update);
					
					if($result_update) {
						$_SESSION['modify_restaurant_status_success'] = true;
						header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/restaurant_detail/' . $_POST['modify_id'] . '/');
						exit;
					}
				}
			}
		}
		$_SESSION['modify_restaurant_status_error'] = true;
		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/restaurant_detail/' . $_POST['modify_id'] . '/');
		exit;
	}

}