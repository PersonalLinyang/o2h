<?php
/* 
 * 削除景点类别
 */

class Controller_Admin_Service_Spottype_Deletespottype extends Controller_Admin_App
{

	/**
	 * 削除景点类别
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = 1)
	{
		try {
			if(!isset($_POST['page'])) {
				//删除所需的数据不全
				$_SESSION['delete_spot_type_error'] = 'error_system';
			} else {
				if(!isset($_POST['delete_id'])) {
					//删除所需的数据不全
					$_SESSION['delete_spot_type_error'] = 'error_system';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 10)) {
					//当前登陆用户不具备景点类别管理的权限
					return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
				} else {
					//削除景点类别
					$params_delete = array(
						'spot_type_id' => $_POST['delete_id'],
					);
					
					$result_check = Model_Spottype::CheckDeleteSpotType($params_delete);
					
					if($result_check['result']) {
						$result_delete = Model_Spottype::DeleteSpotType($params_delete);
						
						if($result_delete) {
							//更新批量导入景点用模板
							$result_excel = Model_Spot::ModifySpotModelExcel();
							
							if($result_excel) {
								//削除成功
								$_SESSION['delete_spot_type_success'] = true;
							} else {
								//模板更新失败
								$_SESSION['delete_spot_type_error'] = 'error_excel';
							}
						} else {
							//削除失败
							$_SESSION['delete_spot_type_error'] = 'error_db';
						}
					} else {
						$_SESSION['delete_spot_type_error'] = $result_check['error'][0];
					}
				}
			}
		} catch (Exception $e) {
			//发生系统异常
			$_SESSION['delete_spot_type_error'] = 'error_system';
		}
		header('Location: //' . $_SERVER['HTTP_HOST'] . '/admin/spot_type_list/');
		exit;
	}

}