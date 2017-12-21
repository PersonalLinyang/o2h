<?php
/* 
 * 修改功能名称页
 */

class Controller_Admin_User_Modifyfunction extends Controller_Admin_App
{

	/**
	 * 修改功能名称
	 * @access  public
	 * @return  Response
	 */
	public function action_index($function_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		if(Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 1)) {
			$data['input_function_name'] = '';
			$data['error_message'] = '';
			
			//页面参数检查
			$function = Model_Function::SelectFunctionById($function_id);
			if(!$function) {
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
				exit;
			}
			
			$data['master_group_name'] = $function['master_group_name'];
			$data['sub_group_name'] = $function['sub_group_name'];
			$data['function_name'] = $function['function_name'];
			
			if(isset($_POST['page'])) {
				$error_message_list = array();
				
				$data['input_function_name'] = isset($_POST['function_name']) ? trim($_POST['function_name']) : '';
				
				if($_POST['page'] == 'modify_function') {
					if($data['input_function_name'] == $function['function_name']) {
						$error_message_list[] = '请输入与原名称不同的功能名称';
					} else {
						$params_update = array(
							'function_id' => $function_id,
							'function_name' => $data['input_function_name'],
							'function_group_id' => $function['sub_group_id'],
						);
						//输入内容检查
						$result_check = Model_Function::CheckUpdateFunctionName($params_update);
						
						if($result_check['result']) {
							//数据更新
							$result_update = Model_Function::UpdateFunctionName($params_update);
							
							if($result_update) {
								$_SESSION['update_function_success'] = true;
								header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
								exit;
							} else {
								$error_message_list[] = '数据库错误：数据添加失败';
							}
						} else {
							foreach($result_check['error'] as $update_error) {
								switch($update_error) {
									case 'empty_name':
										$error_message_list[] = '请输入功能名称';
										break;
									case 'long_name':
										$error_message_list[] = '功能名称不能超过30字';
										break;
									case 'dup_name':
										$error_message_list[] = '该副功能组中已存在该名称的功能，无法重复设定';
										break;
									default:
										$error_message_list[] = '发生系统错误,请重新尝试更新';
										break;
								}
							}
						}
					}
					
					$error_message_list = array_unique($error_message_list);
					
					//输出错误信息
					if(count($error_message_list)) {
						$data['error_message'] = implode('<br/>', $error_message_list);
					}
				} else {
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
			}
			
			//调用View
			return Response::forge(View::forge($this->template . '/admin/user/modify_function', $data, false));
		} else {
			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
		}
	}

}