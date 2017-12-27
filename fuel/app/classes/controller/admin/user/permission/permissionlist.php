<?php
/* 
 * 系统权限管理页
 */

class Controller_Admin_User_Permission_Permissionlist extends Controller_Admin_App
{

	/**
	 * 系统权限一览
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		$data['edit_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 1);
		$data['delete_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 2);
		
		if(Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'sub_group', 7)) {
			//获取权限数列
			$data['permission_list'] = Model_Permission::SelectSystemPermissionList();
			
			$data['success_message'] = '';
			$data['error_message'] = '';
			
			//表单处理后结果显示
			if(isset($_SESSION['add_master_group_success'])) {
				$data['success_message'] = '主功能组添加成功';
				unset($_SESSION['add_master_group_success']);
			}
			if(isset($_SESSION['add_sub_group_success'])) {
				$data['success_message'] = '副功能组添加成功';
				unset($_SESSION['add_sub_group_success']);
			}
			if(isset($_SESSION['add_function_success'])) {
				$data['success_message'] = '功能添加成功';
				unset($_SESSION['add_function_success']);
			}
			if(isset($_SESSION['add_authority_success'])) {
				$data['success_message'] = '权限添加成功';
				unset($_SESSION['add_authority_success']);
			}
			if(isset($_SESSION['update_master_group_success'])) {
				$data['success_message'] = '主功能组名称更新成功';
				unset($_SESSION['update_master_group_success']);
			}
			if(isset($_SESSION['update_sub_group_success'])) {
				$data['success_message'] = '副功能组名称更新成功';
				unset($_SESSION['update_sub_group_success']);
			}
			if(isset($_SESSION['update_function_success'])) {
				$data['success_message'] = '功能名称更新成功';
				unset($_SESSION['update_function_success']);
			}
			if(isset($_SESSION['update_authority_success'])) {
				$data['success_message'] = '权限名称更新成功';
				unset($_SESSION['update_authority_success']);
			}
			if(isset($_SESSION['delete_master_group_success'])) {
				$data['success_message'] = '主功能组削除成功';
				unset($_SESSION['delete_master_group_success']);
			}
			if(isset($_SESSION['delete_master_group_error'])) {
				$data['error_message'] = '主功能组削除失敗';
				unset($_SESSION['delete_master_group_error']);
			}
			if(isset($_SESSION['delete_sub_group_success'])) {
				$data['success_message'] = '副功能组削除成功';
				unset($_SESSION['delete_sub_group_success']);
			}
			if(isset($_SESSION['delete_sub_group_error'])) {
				$data['error_message'] = '副功能组削除失敗';
				unset($_SESSION['delete_sub_group_error']);
			}
			if(isset($_SESSION['delete_function_success'])) {
				$data['success_message'] = '功能削除成功';
				unset($_SESSION['delete_function_success']);
			}
			if(isset($_SESSION['delete_function_error'])) {
				$data['error_message'] = '功能削除失敗';
				unset($_SESSION['delete_function_error']);
			}
			if(isset($_SESSION['delete_authority_success'])) {
				$data['success_message'] = '权限削除成功';
				unset($_SESSION['delete_authority_success']);
			}
			if(isset($_SESSION['delete_authority_error'])) {
				$data['error_message'] = '权限削除失敗';
				unset($_SESSION['delete_authority_error']);
			}
			
			//调用View
			return Response::forge(View::forge($this->template . '/admin/user/permission/permission_list', $data, false));
		} else {
			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
		}
	}
	
	/**
	 * 削除主功能组
	 * @access  public
	 * @return  Response
	 */
	public function action_deletemastergroup($param = null)
	{
		if(Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 2) && isset($_POST['delete_id'], $_POST['page'])) {
			if($_POST['page'] == 'permission_list') {
				//删除信息检查
				$result_check = Model_Functiongroup::CheckDeleteMasterGroupById($_POST['delete_id']);
				if($result_check['result']) {
					//数据删除
					$result_delete = Model_Functiongroup::DeleteMasterGroupById($_POST['delete_id']);
					
					if($result_delete) {
						$_SESSION['delete_master_group_success'] = true;
						header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
						exit;
					}
				}
			}
		}
		$_SESSION['delete_master_group_error'] = true;
		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
		exit;
	}
	
	/**
	 * 削除副功能组
	 * @access  public
	 * @return  Response
	 */
	public function action_deletesubgroup($param = null)
	{
		if(Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 2) && isset($_POST['delete_id'], $_POST['page'])) {
			if($_POST['page'] == 'permission_list') {
				//删除信息检查
				$result_check = Model_Functiongroup::CheckDeleteSubGroupById($_POST['delete_id']);
				if($result_check['result']) {
					//数据删除
					$result_delete = Model_Functiongroup::DeleteSubGroupById($_POST['delete_id']);
					
					if($result_delete) {
						$_SESSION['delete_sub_group_success'] = true;
						header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
						exit;
					}
				}
			}
		}
		$_SESSION['delete_sub_group_error'] = true;
		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
		exit;
	}
	
	/**
	 * 削除功能
	 * @access  public
	 * @return  Response
	 */
	public function action_deletefunction($param = null)
	{
		if(Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 2) && isset($_POST['delete_id'], $_POST['page'])) {
			if($_POST['page'] == 'permission_list') {
				//删除信息检查
				$result_check = Model_Function::CheckDeleteFunctionById($_POST['delete_id']);
				if($result_check['result']) {
					//数据删除
					$result_delete = Model_Function::DeleteFunctionById($_POST['delete_id']);
					
					if($result_delete) {
						$_SESSION['delete_function_success'] = true;
						header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
						exit;
					}
				}
			}
		}
		$_SESSION['delete_function_error'] = true;
		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
		exit;
	}
	
	/**
	 * 削除权限
	 * @access  public
	 * @return  Response
	 */
	public function action_deleteauthority($param = null)
	{
		if(Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 2) && isset($_POST['delete_id'], $_POST['page'])) {
			if($_POST['page'] == 'permission_list') {
				//删除信息检查
				$result_check = Model_Authority::CheckDeleteAuthorityById($_POST['delete_id']);
				if($result_check['result']) {
					//数据删除
					$result_delete = Model_Authority::DeleteAuthorityById($_POST['delete_id']);
					
					if($result_delete) {
						$_SESSION['delete_authority_success'] = true;
						header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
						exit;
					}
				}
			}
		}
		$_SESSION['delete_authority_error'] = true;
		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
		exit;
	}

}