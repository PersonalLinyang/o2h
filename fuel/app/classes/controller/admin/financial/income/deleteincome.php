<?php
/* 
 * 削除收入记录
 */

class Controller_Admin_Financial_Income_Deleteincome extends Controller_Admin_App
{

	/**
	 * 削除单个收入记录
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = 1)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/income_list/';
		try {
			if(!isset($_POST['page'])) {
				//删除所需的数据不全
				$_SESSION['delete_income_error'] = 'error_system';
			} else {
				if(!isset($_POST['delete_id'])) {
					//删除所需的数据不全
					$_SESSION['delete_income_error'] = 'error_system';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 32)) {
					//当前登陆用户不具备削除收入记录的权限
					$_SESSION['delete_income_error'] = 'error_permission';
				} else {
					//削除收入记录
					$params_delete = array(
						'income_id_list' => array($_POST['delete_id']),
						'deleted_by' => $_SESSION['login_user']['id'],
						'self_only' => !Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 14),
					);
					
					$result_check = Model_Income::CheckDeleteIncome($params_delete);
					
					if($result_check['result']) {
						$result_delete = Model_Income::DeleteIncome($params_delete);
						
						if($result_delete) {
							//削除成功
							$_SESSION['delete_income_success'] = true;
						} else {
							//削除失败
							$_SESSION['delete_income_error'] = 'error_db';
						}
					} else {
						$_SESSION['delete_income_error'] = $result_check['error'][0];
					}
				}
				
				//页面返回目标
				switch($_POST['page']) {
					case 'income_list':
						if(isset($_SERVER['HTTP_REFERER'])) {
							if(strstr($_SERVER['HTTP_REFERER'], 'admin/income_list')) {
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
			$_SESSION['delete_income_error'] = 'error_system';
		}
		header('Location: ' . $header_url);
		exit;
	}

	/**
	 * 削除所有选中收入记录
	 * @access  public
	 * @return  Response
	 */
	public function action_deleteincomechecked($param = null)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/income_list/';
		try {
			if(!isset($_POST['page'])) {
				//删除所需的数据不全
				$_SESSION['delete_income_error'] = 'error_system';
			} else {
				if(!isset($_POST['delete_id_checked'])) {
					//删除所需的数据不全
					$_SESSION['delete_income_error'] = 'error_system';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 32)) {
					//当前登陆用户不具备削除收入记录的权限
					$_SESSION['delete_income_error'] = 'error_permission';
				} else {
					//削除收入记录
					$params_delete = array(
						'income_id_list' => $_POST['delete_id_checked'],
						'deleted_by' => $_SESSION['login_user']['id'],
						'self_only' => !Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 14),
					);
					
					$result_check = Model_Income::CheckDeleteIncome($params_delete);
					
					if($result_check['result']) {
						$result_delete = Model_Income::DeleteIncome($params_delete);
						
						if($result_delete) {
							//削除成功
							$_SESSION['delete_income_checked_success'] = true;
						} else {
							//削除失败
							$_SESSION['delete_income_checked_error'] = 'error_db';
						}
					} else {
						$_SESSION['delete_income_checked_error'] = $result_check['error'][0];
					}
				}
				
				//页面返回目标
				switch($_POST['page']) {
					case 'income_list':
						if(isset($_SERVER['HTTP_REFERER'])) {
							if(strstr($_SERVER['HTTP_REFERER'], 'admin/income_list')) {
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
			$_SESSION['delete_income_checked_error'] = 'error_system';
		}
		header('Location: ' . $header_url);
		exit;
	}

}