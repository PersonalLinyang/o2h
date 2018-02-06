<?php
/* 
 * 削除餐饮店
 */

class Controller_Admin_Service_Restaurant_Deleterestaurant extends Controller_Admin_App
{

	/**
	 * 削除单个餐饮店
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = 1)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/restaurant_list/';
		try {
			if(!isset($_POST['page'])) {
				//删除所需的数据不全
				$_SESSION['delete_restaurant_error'] = 'error_system';
			} else {
				if(!isset($_POST['delete_id'])) {
					//删除所需的数据不全
					$_SESSION['delete_restaurant_error'] = 'error_system';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 12)) {
					//当前登陆用户不具备削除餐饮店的权限
					$_SESSION['delete_restaurant_error'] = 'error_permission';
				} else {
					//削除餐饮店
					$params_delete = array(
						'restaurant_id_list' => array($_POST['delete_id']),
						'deleted_by' => $_SESSION['login_user']['id'],
						'self_only' => !Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 5),
					);
					
					$result_check = Model_Restaurant::CheckDeleteRestaurant($params_delete);
					
					if($result_check['result']) {
						$result_delete = Model_Restaurant::DeleteRestaurant($params_delete);
						
						if($result_delete) {
							//削除成功
							$_SESSION['delete_restaurant_success'] = true;
						} else {
							//削除失败
							$_SESSION['delete_restaurant_error'] = 'error_db';
						}
					} else {
						$_SESSION['delete_restaurant_error'] = $result_check['error'][0];
					}
				}
				
				//页面返回目标
				switch($_POST['page']) {
					case 'restaurant_list':
						if(isset($_SERVER['HTTP_REFERER'])) {
							if(strstr($_SERVER['HTTP_REFERER'], 'admin/restaurant_list')) {
								$header_url = $_SERVER['HTTP_REFERER'];
							}
						}
						break;
					default:
						break;
				}
			}
		} catch (Exception $e) {
			//发生系统异常
			$_SESSION['delete_restaurant_error'] = 'error_system';
		}
		header('Location: ' . $header_url);
		exit;
	}

	/**
	 * 削除所有选中餐饮店
	 * @access  public
	 * @return  Response
	 */
	public function action_deleterestaurantchecked($param = null)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/restaurant_list/';
		try {
			if(!isset($_POST['page'])) {
				//删除所需的数据不全
				$_SESSION['delete_restaurant_checked_error'] = 'error_system';
			} else {
				if(!isset($_POST['delete_id_checked'])) {
					//删除所需的数据不全
					$_SESSION['delete_restaurant_checked_error'] = 'empty_restaurant_id';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 12)) {
					//当前登陆用户不具备削除餐饮店的权限
					$_SESSION['delete_restaurant_checked_error'] = 'error_permission';
				} else {
					//削除餐饮店
					$params_delete = array(
						'restaurant_id_list' => $_POST['delete_id_checked'],
						'deleted_by' => $_SESSION['login_user']['id'],
						'self_only' => !Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 5),
					);
					
					$result_check = Model_Restaurant::CheckDeleteRestaurant($params_delete);
					
					if($result_check['result']) {
						$result_delete = Model_Restaurant::DeleteRestaurant($params_delete);
						
						if($result_delete) {
							//削除成功
							$_SESSION['delete_restaurant_checked_success'] = true;
						} else {
							//削除失败
							$_SESSION['delete_restaurant_checked_error'] = 'error_db';
						}
					} else {
						$_SESSION['delete_restaurant_checked_error'] = $result_check['error'][0];
					}
				}
				
				//页面返回目标
				switch($_POST['page']) {
					case 'restaurant_list':
						if(isset($_SERVER['HTTP_REFERER'])) {
							if(strstr($_SERVER['HTTP_REFERER'], 'admin/restaurant_list')) {
								$header_url = $_SERVER['HTTP_REFERER'];
							}
						}
						break;
					default:
						break;
				}
			}
		} catch (Exception $e) {
			//发生系统异常
			$_SESSION['delete_restaurant_error'] = 'error_system';
		}
		header('Location: ' . $header_url);
		exit;
	}

}