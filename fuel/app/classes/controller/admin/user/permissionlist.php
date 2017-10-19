<?php
/* 
 * 权限管理页
 */

class Controller_Admin_User_Permissionlist extends Controller_Admin_App
{

	/**
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		//调用共用Header
//		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		//调用View
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			//获取权限数列
			$data['permission_list'] = Model_Permission::GetPermissionListAll();
			
			$data['success_message'] = '';
			
			if(isset($_SESSION['add_master_group_success'])) {
				$data['success_message'] = '追加成功';
				unset($_SESSION['add_master_group_success']);
			}
			if(isset($_SESSION['add_sub_group_success'])) {
				$data['success_message'] = '追加成功';
				unset($_SESSION['add_sub_group_success']);
			}
			if(isset($_SESSION['add_function_success'])) {
				$data['success_message'] = '追加成功';
				unset($_SESSION['add_function_success']);
			}
			if(isset($_SESSION['add_authority_success'])) {
				$data['success_message'] = '追加成功';
				unset($_SESSION['add_authority_success']);
			}
			if(isset($_SESSION['modify_master_group_success'])) {
				$data['success_message'] = '更新成功';
				unset($_SESSION['modify_master_group_success']);
			}
			if(isset($_SESSION['modify_sub_group_success'])) {
				$data['success_message'] = '更新成功';
				unset($_SESSION['modify_sub_group_success']);
			}
			if(isset($_SESSION['modify_function_success'])) {
				$data['success_message'] = '更新成功';
				unset($_SESSION['modify_function_success']);
			}
			if(isset($_SESSION['modify_authority_success'])) {
				$data['success_message'] = '更新成功';
				unset($_SESSION['modify_authority_success']);
			}
			if(isset($_SESSION['delete_master_group_success'])) {
				$data['success_message'] = '削除成功';
				unset($_SESSION['delete_master_group_success']);
			}
			if(isset($_SESSION['delete_master_group_error'])) {
				$data['success_message'] = '削除失敗';
				unset($_SESSION['delete_master_group_error']);
			}
			
			return Response::forge(View::forge($this->template . '/admin/user/permission_list', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}
	
	public function action_deletemastergroup($param = null)
	{
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['delete_id'], $_POST['page'])) {
			if($_POST['page'] == 'permission_list') {
				$result_check = Model_Functiongroup::CheckDeleteMasterGroupById($_POST['delete_id']);
				if($result_check['result']) {
					$result_delete = Model_Functiongroup::DeleteMasterGroupById($_POST['delete_id']);
					
					if($result_delete) {
						$_SESSION['delete_master_group_success'] = true;
						header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
						exit;
					}
				}
			}
//		}
//		$_SESSION['delete_master_group_error'] = true;
//		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
//		exit;
	}
	
	public function action_deletesubgroup($param = null)
	{
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['delete_id'], $_POST['page'])) {
			if($_POST['page'] == 'permission_list') {
				$result_check = Model_Functiongroup::CheckDeleteSubGroupById($_POST['delete_id']);
				if($result_check['result']) {
					$result_delete = Model_Functiongroup::DeleteSubGroupById($_POST['delete_id']);
					
					if($result_delete) {
						$_SESSION['delete_sub_group_success'] = true;
						header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
						exit;
					}
				}
			}
//		}
//		$_SESSION['delete_sub_group_error'] = true;
//		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
//		exit;
	}
	
	public function action_deletefunction($param = null)
	{
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['delete_id'], $_POST['page'])) {
			if($_POST['page'] == 'permission_list') {
				$result_check = Model_Function::CheckDeleteFunctionById($_POST['delete_id']);
				if($result_check['result']) {
					$result_delete = Model_Function::DeleteFunctionById($_POST['delete_id']);
					
					if($result_delete) {
						$_SESSION['delete_function_success'] = true;
						header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
						exit;
					}
				}
			}
//		}
//		$_SESSION['delete_function_error'] = true;
//		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
//		exit;
	}
	
	public function action_deleteauthority($param = null)
	{
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['delete_id'], $_POST['page'])) {
			if($_POST['page'] == 'permission_list') {
				$result_check = Model_Authority::CheckDeleteAuthorityById($_POST['delete_id']);
				if($result_check['result']) {
					$result_delete = Model_Authority::DeleteAuthorityById($_POST['delete_id']);
					
					if($result_delete) {
						$_SESSION['delete_authority_success'] = true;
						header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
						exit;
					}
				}
			}
//		}
//		$_SESSION['delete_authority_error'] = true;
//		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
//		exit;
	}

}