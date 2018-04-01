<?php
/* 
 * 削除收入项目
 */

class Controller_Admin_Financial_Incometype_Deleteincometype extends Controller_Admin_App
{

	/**
	 * 削除收入项目
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = 1)
	{
		try {
			if(!isset($_POST['page'])) {
				//删除所需的数据不全
				$_SESSION['delete_income_type_error'] = 'error_system';
			} else {
				if(!isset($_POST['delete_id'])) {
					//删除所需的数据不全
					$_SESSION['delete_income_type_error'] = 'error_system';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 20)) {
					//当前登陆用户不具备收入项目管理的权限
					return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
				} else {
					//削除收入项目
					$params_delete = array(
						'income_type_id' => $_POST['delete_id'],
					);
					
					$result_check = Model_Incometype::CheckDeleteIncomeType($params_delete);
					
					if($result_check['result']) {
						$result_delete = Model_Incometype::DeleteIncomeType($params_delete);
						
						if($result_delete) {
							//削除成功
							$_SESSION['delete_income_type_success'] = true;
						} else {
							//削除失败
							$_SESSION['delete_income_type_error'] = 'error_db';
						}
					} else {
						$_SESSION['delete_income_type_error'] = $result_check['error'][0];
					}
				}
			}
		} catch (Exception $e) {
			//发生系统异常
			$_SESSION['delete_income_type_error'] = 'error_system';
		}
		header('Location: //' . $_SERVER['HTTP_HOST'] . '/admin/income_type_list/');
		exit;
	}

}