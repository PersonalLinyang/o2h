<?php
/* 
 * 削除景点
 */

class Controller_Admin_Service_Spot_Deletespot extends Controller_Admin_App
{

	/**
	 * 削除单个景点
	 * @access  public
	 * @return  Response
	 */
	public function action_index($page = 1)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/spot_list/';
		try {
			if(!isset($_POST['page'])) {
				//删除所需的数据不全
				$_SESSION['delete_spot_error'] = 'error_system';
			} else {
				if(!isset($_POST['delete_id'])) {
					//删除所需的数据不全
					$_SESSION['delete_spot_error'] = 'error_system';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 7)) {
					//当前登陆用户不具备削除景点的权限
					$_SESSION['delete_spot_error'] = 'error_permission';
				} else {
					//削除景点
					$params_delete = array(
						'spot_id_list' => array($_POST['delete_id']),
						'deleted_by' => $_SESSION['login_user']['id'],
						'self_only' => !Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 3),
					);
					
					$result_check = Model_Spot::CheckDeleteSpot($params_delete);
					
					if($result_check['result']) {
						$result_delete = Model_Spot::DeleteSpot(array('spot_id_list' => array($_POST['delete_id']), 'deleted_by' => $_SESSION['login_user']['id']));
						
						if($result_delete) {
							//削除成功
							$_SESSION['delete_spot_success'] = true;
						} else {
							//削除失败
							$_SESSION['delete_spot_error'] = 'error_db';
						}
					} else {
						$_SESSION['delete_spot_error'] = $result_check['error'][0];
					}
				}
				
				//页面返回目标
				switch($_POST['page']) {
					case 'spot_list':
						if(isset($_SERVER['HTTP_REFERER'])) {
							if(strstr($_SERVER['HTTP_REFERER'], 'admin/spot_list')) {
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
			$_SESSION['delete_spot_error'] = 'error_system';
		}
		header('Location: ' . $header_url);
		exit;
	}

	/**
	 * 削除所有选中景点
	 * @access  public
	 * @return  Response
	 */
	public function action_deletespotchecked($param = null)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/spot_list/';
		try {
			if(!isset($_POST['page'])) {
				//删除所需的数据不全
				$_SESSION['delete_spot_checked_error'] = 'error_system';
			} else {
				if(!isset($_POST['delete_id_checked'])) {
					//删除所需的数据不全
					$_SESSION['delete_spot_checked_error'] = 'empty_spot_id';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 7)) {
					//当前登陆用户不具备削除景点的权限
					$_SESSION['delete_spot_checked_error'] = 'error_permission';
				} else {
					//削除景点
					$params_delete = array(
						'spot_id_list' => $_POST['delete_id_checked'],
						'deleted_by' => $_SESSION['login_user']['id'],
						'self_only' => !Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 3),
					);
					
					$result_check = Model_Spot::CheckDeleteSpot($params_delete);
					
					if($result_check['result']) {
						$result_delete = Model_Spot::DeleteSpot($params_delete);
						
						if($result_delete) {
							//削除成功
							$_SESSION['delete_spot_checked_success'] = true;
						} else {
							//削除失败
							$_SESSION['delete_spot_checked_error'] = 'error_db';
						}
					} else {
						$_SESSION['delete_spot_checked_error'] = $result_check['error'][0];
					}
				}
				
				//页面返回目标
				switch($_POST['page']) {
					case 'spot_list':
						if(isset($_SERVER['HTTP_REFERER'])) {
							if(strstr($_SERVER['HTTP_REFERER'], 'admin/spot_list')) {
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
			$_SESSION['delete_spot_error'] = 'error_system';
		}
		header('Location: ' . $header_url);
		exit;
	}

}