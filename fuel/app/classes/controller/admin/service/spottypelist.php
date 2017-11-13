<?php
/* 
 * 景点类别一览页
 */

class Controller_Admin_Service_Spottypelist extends Controller_Admin_App
{

	/**
	 * 景点类别一览
	 * @access  public
	 * @return  Response
	 */
	public function action_index($page = null)
	{
		$data = array();
		
		if(!is_numeric($page)) {
			$page = 1;
		}
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['success_message'] = '';
			$data['error_message'] = '';
			$data['spot_type_list'] = Model_SpotType::GetSpotTypeListAll();
			
			if(isset($_SESSION['add_spot_type_success'])) {
				$data['success_message'] = '景点类别添加成功';
				unset($_SESSION['add_spot_type_success']);
			}
			if(isset($_SESSION['modify_spot_type_success'])) {
				$data['success_message'] = '景点类别名称修改成功';
				unset($_SESSION['modify_spot_type_success']);
			}
			if(isset($_SESSION['delete_spot_type_success'])) {
				$data['success_message'] = '景点类别削除成功';
				unset($_SESSION['delete_spot_type_success']);
			}
			if(isset($_SESSION['delete_spot_type_error'])) {
				$data['error_message'] = '景点类别削除失敗';
				unset($_SESSION['delete_spot_type_error']);
			}

			//调用View
			return Response::forge(View::forge($this->template . '/admin/service/spot_type_list', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}
	
	/**
	 * 削除景点
	 * @access  public
	 * @return  Response
	 */
	public function action_deletespottype($param = null)
	{
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['delete_id'], $_POST['page'])) {
			if($_POST['page'] == 'spot_type_list') {
				//删除信息检查
				$result_check = Model_SpotType::CheckDeleteSpotTypeById($_POST['delete_id']);
				if($result_check['result']) {
					//数据删除
					$result_delete = Model_SpotType::DeleteSpotTypeById($_POST['delete_id']);
					
					if($result_delete) {
						$_SESSION['delete_spot_type_success'] = true;
						header('Location: ' . $_SERVER['HTTP_REFERER']);
						exit;
					}
				}
			}
//		}
		$_SESSION['delete_spot_type_error'] = true;
		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/spot_type_list/');
		exit;
	}

}