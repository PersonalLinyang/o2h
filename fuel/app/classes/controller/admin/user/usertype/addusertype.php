<?php
/* 
 * 添加用户类型页
 */

class Controller_Admin_User_Usertype_Addusertype extends Controller_Admin_App
{

	/**
	 * 添加用户类型
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		try {
			if(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 3)) {
				//当前登陆用户不具备编辑用户类型的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//页面标题
				$data['page_title'] ='添加用户类型';
				//表单页面索引
				$data['form_page_index'] = 'add_user_type';
				//获取权限列表
				if(Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 1)) {
					//获取全部权限
					$data['permission_list'] = Model_Permission::SelectSystemPermissionList();
				} else {
					//只获取普通权限
					$data['permission_list'] = Model_Permission::SelectSystemPermissionList(array('normal_only' => true));
				}
				
				//是否允许操作特殊用户类型
				$data['special_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 5);
				
				//form控件默认值设定
				$data['input_user_type_name'] = '';
				$data['input_special_level'] = '0';
				$data['input_master_group'] = array();
				$data['input_sub_group'] = array();
				$data['input_function'] = array();
				$data['input_authority'] = array();
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					//数据来源检验
					if($_POST['page'] != $data['form_page_index']) {
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_user_type_name'] = isset($_POST['user_type_name']) ? trim($_POST['user_type_name']) : '';
						$data['input_special_level'] = (isset($_POST['special_level']) && $data['special_able_flag']) ? trim($_POST['special_level']) : '0';
						$data['input_master_group'] = isset($_POST['master_group']) ? (is_array($_POST['master_group']) ? $_POST['master_group'] : array()) : array();
						$data['input_sub_group'] = isset($_POST['sub_group']) ? (is_array($_POST['sub_group']) ? $_POST['sub_group'] : array()) : array();
						$data['input_function'] = isset($_POST['function']) ? (is_array($_POST['function']) ? $_POST['function'] : array()) : array();
						$data['input_authority'] = isset($_POST['authority']) ? (is_array($_POST['authority']) ? $_POST['authority'] : array()) : array();
						
						//添加用户类型用数据生成
						$param_insert = array(
							'user_type_id' => '',
							'user_type_name' => $data['input_user_type_name'],
							'special_level' => $data['input_special_level'],
							'permission' => array(
								'1' => $data['input_master_group'],
								'2' => $data['input_sub_group'],
								'3' => $data['input_function'],
								'4' => $data['input_authority'],
							),
						);
						
						//添加内容检查
						$result_check = Model_Usertype::CheckEditUserType($param_insert);
						
						if($result_check['result']) {
							//添加用户类型
							$result_insert = Model_Usertype::InsertUserType($param_insert);
							
							if($result_insert) {
								//添加成功 页面跳转
								$_SESSION['add_user_type_success'] = true;
								header('Location: //' . $_SERVER['HTTP_HOST'] . '/admin/user_type_detail/' . $result_insert . '/');
								exit;
							} else {
								$error_message_list[] = '数据库错误：数据添加失败';
							}
						} else {
							//获取错误信息
							foreach($result_check['error'] as $insert_error) {
								switch($insert_error) {
									case 'empty_user_type_name':
										$error_message_list[] = '请输入用户类型名称';
										break;
									case 'long_user_type_name':
										$error_message_list[] = '用户类型名称不能超过30字';
										break;
									case 'dup_user_type_name':
										$error_message_list[] = '该用户类型名称于其他用户类型重复,请选用其他名称';
										break;
									default:
										$error_message_list[] = '发生系统错误,请重新尝试添加';
										break;
								}
							}
						}
						
						$error_message_list = array_unique($error_message_list);
						
						//输出错误信息
						if(count($error_message_list)) {
							$data['error_message'] = implode('<br/>', $error_message_list);
						}
					}
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/user/user_type/edit_user_type', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}