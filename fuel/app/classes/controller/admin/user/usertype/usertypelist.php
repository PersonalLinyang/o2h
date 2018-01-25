<?php
/* 
 * 用户类型一览页
 */

class Controller_Admin_User_Usertype_Usertypelist extends Controller_Admin_App
{

	/**
	 * 用户类型一览
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		try {
			if(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'sub_group', 8)) {
				//当前登陆用户不具备查看用户类型的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['success_message'] = '';
				$data['error_message'] = '';
				
				//获取自身所持有的用户类型
				$data['user_type_self'] = Model_User::SelectUserTypeById($_SESSION['login_user']['id']);
				//用户类型特殊等级列表
				$data['special_level_list'] = array('一般类型', '特殊类型', '系统类型');
				//是否具备用户类型编辑权限
				$data['edit_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 3);
				//是否具备用户类型删除权限
				$data['delete_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 4);
				//是否具备操作特殊用户类型权限
				$data['special_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 5);
				
				//获取权限数列
				if($data['special_able_flag']) {
					$special_level_select = array(0, 1);
				} else {
					$special_level_select = array(0);
				}
				$data['user_type_list'] = Model_UserType::SelectUserTypeListWithUserNum(array('special_level' => $special_level_select));
				
				//输出提示信息
				if(isset($_SESSION['delete_user_type_success'])) {
					$data['success_message'] = '用户类型削除成功';
					unset($_SESSION['delete_user_type_success']);
				}
				if(isset($_SESSION['delete_user_type_error'])) {
					$data['error_message'] = '用户类型削除失敗';
					unset($_SESSION['delete_user_type_error']);
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/user/user_type/user_type_list', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}
	
	/**
	 * 削除用户类型
	 * @access  public
	 * @return  Response
	 */
	public function action_deleteusertype($param = null)
	{
		try {
			//检测当前登陆用户是否具备删除用户类型的权限及删除指令是否具备删除用户类型所必须的参数信息
			if(Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 4) && isset($_POST['page'], $_POST['delete_id'])) {
				//检测删除指令的数据来源及要删除用户类型的ID是否为数字
				if($_POST['page'] == 'user_type_list' && is_numeric($_POST['delete_id'])) {
					//获取自身用户类型
					$user_type_self = Model_User::SelectUserTypeById($_SESSION['login_user']['id']);
					//获取要删除的用户类型信息
					$user_type = Model_UserType::SelectUserType(array('user_type_id' => $_POST['delete_id']));
					
					//不能删除自身所持有的用户类型且要删除的用户类型必须存在
					if($_POST['delete_id'] != $user_type_self && $user_type) {
						//不能删除系统用户类型
						if(intval($user_type['special_level']) < 2) {
							//在不具备操作特殊用户类型权限时只能删除一般用户类型
							if(Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 5) || $user_type['special_level'] == '0') {
								//数据删除
								$result_delete = Model_UserType::DeleteUserType($_POST['delete_id']);
								if($result_delete) {
									//删除成功
									$_SESSION['delete_user_type_success'] = true;
									header('Location: ' . $_SERVER['HTTP_REFERER']);
									exit;
								}
							}
						}
					}
				}
			}
			
			//删除失败
			$_SESSION['delete_user_type_error'] = true;
			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/user_type_list/');
			exit;
		} catch (Exception $e) {
			//发生系统异常
			$_SESSION['delete_user_type_error'] = true;
			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/user_type_list/');
			exit;
		}
	}

}