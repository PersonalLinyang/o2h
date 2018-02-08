<?php
/* 
 * 削除旅游路线
 */

class Controller_Admin_Service_Route_Deleteroute extends Controller_Admin_App
{

	/**
	 * 削除单个旅游路线
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = 1)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/route_list/';
		try {
			if(!isset($_POST['page'])) {
				//删除所需的数据不全
				$_SESSION['delete_route_error'] = 'error_system';
			} else {
				if(!isset($_POST['delete_id'])) {
					//删除所需的数据不全
					$_SESSION['delete_route_error'] = 'error_system';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 23)) {
					//当前登陆用户不具备削除旅游路线的权限
					$_SESSION['delete_route_error'] = 'error_permission';
				} else {
					//削除旅游路线
					$params_delete = array(
						'route_id_list' => array($_POST['delete_id']),
						'deleted_by' => $_SESSION['login_user']['id'],
						'self_only' => !Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 5),
					);
					
					$result_check = Model_Route::CheckDeleteRoute($params_delete);
					
					if($result_check['result']) {
						$result_delete = Model_Route::DeleteRoute($params_delete);
						
						if($result_delete) {
							//删除图片
							try {
								$device_index_list = array('pc', 'sp');
								foreach($device_index_list as $device_index) {
									$dir_image = DOCROOT . 'assets/img/' . $device_index . '/upload/route/' . $_POST['delete_id'] . '/';
									$file_image_list = scandir($dir_image);
									foreach($file_image_list as $file_image) {
										if($file_image != '.' && $file_image != '..') {
											unlink($dir_image . $file_image);
										}
									}
									rmdir($dir_image);
								}
								//削除成功
								$_SESSION['delete_route_success'] = true;
							} catch (Exception $e) {
								//削除成功 但图片削除失败
								$_SESSION['delete_route_error'] = 'error_image';
							}
						} else {
							//削除失败
							$_SESSION['delete_route_error'] = 'error_db';
						}
					} else {
						$_SESSION['delete_route_error'] = $result_check['error'][0];
					}
				}
				
				//页面返回目标
				switch($_POST['page']) {
					case 'route_list':
						if(isset($_SERVER['HTTP_REFERER'])) {
							if(strstr($_SERVER['HTTP_REFERER'], 'admin/route_list')) {
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
			$_SESSION['delete_route_error'] = 'error_system';
		}
		header('Location: ' . $header_url);
		exit;
	}
	
	/**
	 * 削除所有选中旅游路线
	 * @access  public
	 * @return  Response
	 */
	public function action_deleteroutechecked($param = null)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/route_list/';
		try {
			if(!isset($_POST['page'])) {
				//删除所需的数据不全
				$_SESSION['delete_route_checked_error'] = 'error_system';
			} else {
				if(!isset($_POST['delete_id_checked'])) {
					//删除所需的数据不全
					$_SESSION['delete_route_checked_error'] = 'empty_route_id';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 12)) {
					//当前登陆用户不具备削除旅游路线的权限
					$_SESSION['delete_route_checked_error'] = 'error_permission';
				} else {
					//削除餐饮店
					$params_delete = array(
						'route_id_list' => $_POST['delete_id_checked'],
						'deleted_by' => $_SESSION['login_user']['id'],
						'self_only' => !Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 5),
					);
					
					$result_check = Model_Route::CheckDeleteRoute($params_delete);
					
					if($result_check['result']) {
						$result_delete = Model_Route::DeleteRoute($params_delete);
						
						if($result_delete) {
							//删除图片
							try {
								$device_index_list = array('pc', 'sp');
								foreach($device_index_list as $device_index) {
									foreach($_POST['delete_id_checked'] as $delete_id) {
										$dir_image = DOCROOT . 'assets/img/' . $device_index . '/upload/route/' . $delete_id . '/';
										$file_image_list = scandir($dir_image);
										foreach($file_image_list as $file_image) {
											if($file_image != '.' && $file_image != '..') {
												unlink($dir_image . $file_image);
											}
										}
										rmdir($dir_image);
									}
								}
								//削除成功
								$_SESSION['delete_route_checked_success'] = true;
							} catch (Exception $e) {
								//削除成功 但图片削除失败
								$_SESSION['delete_route_checked_error'] = 'error_image';
							}
						} else {
							//削除失败
							$_SESSION['delete_route_checked_error'] = 'error_db';
						}
					} else {
						$_SESSION['delete_route_checked_error'] = $result_check['error'][0];
					}
				}
				
				//页面返回目标
				switch($_POST['page']) {
					case 'route_list':
						if(isset($_SERVER['HTTP_REFERER'])) {
							if(strstr($_SERVER['HTTP_REFERER'], 'admin/route_list')) {
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
			$_SESSION['delete_route_error'] = 'error_system';
		}
		header('Location: ' . $header_url);
		exit;
	}

}