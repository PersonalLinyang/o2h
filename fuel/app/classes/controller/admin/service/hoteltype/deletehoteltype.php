<?php
/* 
 * 削除酒店类别
 */

class Controller_Admin_Service_Hoteltype_Deletehoteltype extends Controller_Admin_App
{

	/**
	 * 削除单个酒店类别
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = 1)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/hotel_type_list/';
		try {
			if(!isset($_POST['page'])) {
				//删除所需的数据不全
				$_SESSION['delete_hotel_type_error'] = 'error_system';
			} else {
				if(!isset($_POST['delete_id'])) {
					//删除所需的数据不全
					$_SESSION['delete_hotel_type_error'] = 'error_system';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 10)) {
					//当前登陆用户不具备酒店类别管理的权限
					return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
				} else {
					//削除酒店类别
					$params_delete = array(
						'hotel_type_id' => $_POST['delete_id'],
					);
					
					$result_check = Model_Hoteltype::CheckDeleteHotelType($params_delete);
					
					if($result_check['result']) {
						$result_delete = Model_Hoteltype::DeleteHotelType($params_delete);
						
						if($result_delete) {
							//更新酒店信息导入模板
							$result_excel = Model_Hoteltype::ModifyHotelModelExcel();
							
							if($result_excel) {
								//削除成功
								$_SESSION['delete_hotel_type_success'] = true;
							} else {
								//模板更新失败
								$_SESSION['delete_hotel_type_error'] = 'error_excel';
							}
						} else {
							//削除失败
							$_SESSION['delete_hotel_type_error'] = 'error_db';
						}
					} else {
						$_SESSION['delete_hotel_type_error'] = $result_check['error'][0];
					}
				}
				
				//页面返回目标
				switch($_POST['page']) {
					case 'hotel_type_list':
						if(isset($_SERVER['HTTP_REFERER'])) {
							if(strstr($_SERVER['HTTP_REFERER'], 'admin/hotel_type_list')) {
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
			$_SESSION['delete_hotel_type_error'] = 'error_system';
		}
		header('Location: ' . $header_url);
		exit;
	}

}