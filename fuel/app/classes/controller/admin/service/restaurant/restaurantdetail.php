<?php
/* 
 * 餐饮详细信息页
 */

class Controller_Admin_Service_Restaurant_Restaurantdetail extends Controller_Admin_App
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
		
		try {
			if(!is_numeric($restaurant_id)) {
				//餐饮ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'sub_group', 10)) {
				//当前登陆用户不具备查看餐饮的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['success_message'] = '';
				$data['error_message'] = '';
				
				//获取返回一览页时的一览页URL
				$data['restaurant_list_url'] = '/admin/restaurant_list/';
				if(isset($_SERVER['HTTP_REFERER'])) {
					if(strstr($_SERVER['HTTP_REFERER'], 'admin/restaurant_list')) {
						//通过一览页链接进入
						$data['restaurant_list_url'] = $_SERVER['HTTP_REFERER'];
					} elseif(strstr($_SERVER['HTTP_REFERER'], 'admin/modify_restaurant/' . $restaurant_id) || strstr($_SERVER['HTTP_REFERER'], 'admin/restaurant_detail/' . $restaurant_id)) {
						if(isset($_SESSION['restaurant_list_url_detail'])) {
							$data['restaurant_list_url'] = $_SESSION['restaurant_list_url_detail'];
						}
					}
				}
				//暂时保留一览页URL
				$_SESSION['restaurant_list_url_detail'] = $data['restaurant_list_url'];
				
				//获取餐饮信息
				$restaurant = Model_Restaurant::SelectRestaurant(array('restaurant_id' => $restaurant_id, 'active_only' => true));
				
				if(!$restaurant) {
					//不存在该ID的餐饮
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
				
				//餐饮信息
				$data['restaurant_info'] = $restaurant;
				
				if($restaurant['created_by'] == $_SESSION['login_user']['id']) {
					//是否具备餐饮编辑权限
					$data['edit_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 11);
				} else {
					//是否具备修改其他用户所登陆的餐饮信息权限
					$data['edit_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 4);
				}
				
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
				return Response::forge(View::forge($this->template . '/admin/service/restaurant/restaurant_detail', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}