<?php
/* 
 * 用户类型详情页
 */

class Controller_Admin_User_Usertype_Usertypedetail extends Controller_Admin_App
{

	/**
	 * 用户类型详情
	 * @access  public
	 * @return  Response
	 */
	public function action_index($user_type_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		try {
			//获取自身所持有的用户类型
			$data['user_type_self'] = Model_User::SelectUserTypeById($_SESSION['login_user']['id']);
			
			if(!is_numeric($user_type_id)) {
				//用户类型ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'sub_group', 8)) {
				//当前登陆用户不具备查看用户类型的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['success_message'] = '';
				
				//获取用户类型信息
				$user_type = Model_UserType::SelectUserType(array('user_type_id' => $user_type_id));
				//是否允许操作特殊用户类型
				$special_able_flag = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 5);
				
				if(!$user_type) {
					//不存在该ID的用户类型
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				} elseif(intval($user_type['special_level']) > 1) {
					//该ID的用户类型为系统用户类型
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				} elseif(!$special_able_flag && intval($user_type['special_level']) > 0) {
					//该ID的用户类型为特殊用户类型且当前登陆用户不具备操作特殊用户类型的权限
					return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
					exit;
				}
				
				//用户类型信息
				$data['user_type'] = $user_type;
				//获取全部权限
				$data['permission_list'] = Model_Permission::SelectSystemPermissionList();
				//用户类型特殊等级列表
				$data['special_level_list'] = array('一般类型', '特殊类型', '系统类型');
				//是否具备用户类型编辑权限
				$data['edit_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 3);
				//是否允许操作特殊用户类型
				$data['special_able_flag'] = $special_able_flag;
				//获取当前该用户类型所持有的权限列表
				$permission_list = Model_Permission::SelectPermissionList(array('user_type' => $user_type_id));
				$data['master_group'] = $permission_list['master_group'];
				$data['sub_group'] = $permission_list['sub_group'];
				$data['function'] = $permission_list['function'];
				$data['authority'] = $permission_list['authority'];
				
				//输出提示信息
				if(isset($_SESSION['add_user_type_success'])) {
					$data['success_message'] = '用户类型添加成功';
					unset($_SESSION['add_user_type_success']);
				}
				if(isset($_SESSION['modify_user_type_success'])) {
					$data['error_message'] = '用户类型编辑失敗';
					unset($_SESSION['modify_user_type_success']);
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/user/user_type/user_type_detail', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}