<?php
/* 
 * 削除支出记录
 */

class Controller_Admin_Financial_Cost_Deletecost extends Controller_Admin_App
{

	/**
	 * 削除单个支出记录
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = 1)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/cost_list/';
		try {
			if(!isset($_POST['page'])) {
				//删除所需的数据不全
				$_SESSION['delete_cost_error'] = 'error_system';
			} else {
				if(!isset($_POST['delete_id'])) {
					//删除所需的数据不全
					$_SESSION['delete_cost_error'] = 'error_system';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 32)) {
					//当前登陆用户不具备削除支出记录的权限
					$_SESSION['delete_cost_error'] = 'error_permission';
				} else {
					//削除支出记录
					$params_delete = array(
						'cost_id_list' => array($_POST['delete_id']),
						'deleted_by' => $_SESSION['login_user']['id'],
						'self_only' => !Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 14),
					);
					
					$result_check = Model_Cost::CheckDeleteCost($params_delete);
					
					if($result_check['result']) {
						$result_delete = Model_Cost::DeleteCost($params_delete);
						
						if($result_delete) {
							//削除成功
							$_SESSION['delete_cost_success'] = true;
						} else {
							//削除失败
							$_SESSION['delete_cost_error'] = 'error_db';
						}
					} else {
						$_SESSION['delete_cost_error'] = $result_check['error'][0];
					}
				}
				
				//页面返回目标
				switch($_POST['page']) {
					case 'cost_list':
						if(isset($_SERVER['HTTP_REFERER'])) {
							if(strstr($_SERVER['HTTP_REFERER'], 'admin/cost_list')) {
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
			$_SESSION['delete_cost_error'] = 'error_system';
		}
		header('Location: ' . $header_url);
		exit;
	}

	/**
	 * 削除所有选中支出记录
	 * @access  public
	 * @return  Response
	 */
	public function action_deletecostchecked($param = null)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/cost_list/';
		try {
			if(!isset($_POST['page'])) {
				//删除所需的数据不全
				$_SESSION['delete_cost_error'] = 'error_system';
			} else {
				if(!isset($_POST['delete_id_checked'])) {
					//删除所需的数据不全
					$_SESSION['delete_cost_error'] = 'error_system';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 32)) {
					//当前登陆用户不具备削除支出记录的权限
					$_SESSION['delete_cost_error'] = 'error_permission';
				} else {
					//削除支出记录
					$params_delete = array(
						'cost_id_list' => $_POST['delete_id_checked'],
						'deleted_by' => $_SESSION['login_user']['id'],
						'self_only' => !Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 14),
					);
					
					$result_check = Model_Cost::CheckDeleteCost($params_delete);
					
					if($result_check['result']) {
						$result_delete = Model_Cost::DeleteCost($params_delete);
						
						if($result_delete) {
							//削除成功
							$_SESSION['delete_cost_checked_success'] = true;
						} else {
							//削除失败
							$_SESSION['delete_cost_checked_error'] = 'error_db';
						}
					} else {
						$_SESSION['delete_cost_checked_error'] = $result_check['error'][0];
					}
				}
				
				//页面返回目标
				switch($_POST['page']) {
					case 'cost_list':
						if(isset($_SERVER['HTTP_REFERER'])) {
							if(strstr($_SERVER['HTTP_REFERER'], 'admin/cost_list')) {
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
			$_SESSION['delete_cost_checked_error'] = 'error_system';
		}
		header('Location: ' . $header_url);
		exit;
	}

}