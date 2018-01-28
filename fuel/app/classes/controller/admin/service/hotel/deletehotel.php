<?php
/* 
 * 削除酒店
 */

class Controller_Admin_Service_Hotel_Deletehotel extends Controller_Admin_App
{

	/**
	 * 削除单个酒店
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = 1)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/hotel_list/';
//		try {
			if(!isset($_POST['page'])) {
				//删除所需的数据不全
				$_SESSION['delete_hotel_error'] = 'error_system';
			} else {
				if(!isset($_POST['delete_id'])) {
					//删除所需的数据不全
					$_SESSION['delete_hotel_error'] = 'error_system';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 7)) {
					//当前登陆用户不具备削除酒店的权限
					$_SESSION['delete_hotel_error'] = 'error_permission';
				} else {
					//削除酒店
					$params_delete = array(
						'hotel_id_list' => array($_POST['delete_id']),
						'deleted_by' => $_SESSION['login_user']['id'],
						'self_only' => !Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 3),
					);
					
					$result_check = Model_Hotel::CheckDeleteHotel($params_delete);
					
					if($result_check['result']) {
						$result_delete = Model_Hotel::DeleteHotel($params_delete);
						
						if($result_delete) {
							//削除成功
							$_SESSION['delete_hotel_success'] = true;
						} else {
							//削除失败
							$_SESSION['delete_hotel_error'] = 'error_db';
						}
					} else {
						$_SESSION['delete_hotel_error'] = $result_check['error'][0];
					}
				}
				
				//页面返回目标
				switch($_POST['page']) {
					case 'hotel_list':
						if(isset($_SERVER['HTTP_REFERER'])) {
							if(strstr($_SERVER['HTTP_REFERER'], 'admin/hotel_list')) {
								$header_url = $_SERVER['HTTP_REFERER'];
							}
						}
						break;
					default:
						break;
				}
			}
//		} catch (Exception $e) {
//			//发生系统异常
//			$_SESSION['delete_hotel_error'] = 'error_system';
//		}
		header('Location: ' . $header_url);
		exit;
	}

	/**
	 * 削除所有选中酒店
	 * @access  public
	 * @return  Response
	 */
	public function action_deletehotelchecked($param = null)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/hotel_list/';
		try {
			if(!isset($_POST['page'])) {
				//删除所需的数据不全
				$_SESSION['delete_hotel_checked_error'] = 'error_system';
			} else {
				if(!isset($_POST['delete_id_checked'])) {
					//删除所需的数据不全
					$_SESSION['delete_hotel_checked_error'] = 'empty_hotel_id';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 7)) {
					//当前登陆用户不具备削除酒店的权限
					$_SESSION['delete_hotel_checked_error'] = 'error_permission';
				} else {
					//削除酒店
					$params_delete = array(
						'hotel_id_list' => $_POST['delete_id_checked'],
						'deleted_by' => $_SESSION['login_user']['id'],
						'self_only' => !Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 3),
					);
					
					$result_check = Model_Hotel::CheckDeleteHotel($params_delete);
					
					if($result_check['result']) {
						$result_delete = Model_Hotel::DeleteHotel($params_delete);
						
						if($result_delete) {
							//削除成功
							$_SESSION['delete_hotel_checked_success'] = true;
						} else {
							//削除失败
							$_SESSION['delete_hotel_checked_error'] = 'error_db';
						}
					} else {
						$_SESSION['delete_hotel_checked_error'] = $result_check['error'][0];
					}
				}
				
				//页面返回目标
				switch($_POST['page']) {
					case 'hotel_list':
						if(isset($_SERVER['HTTP_REFERER'])) {
							if(strstr($_SERVER['HTTP_REFERER'], 'admin/hotel_list')) {
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
			$_SESSION['delete_hotel_error'] = 'error_system';
		}
		header('Location: ' . $header_url);
		exit;
	}

}