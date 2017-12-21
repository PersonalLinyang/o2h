<?php
/* 
 * 修改主功能组名称页
 */

class Controller_Admin_User_Permission_Modifymastergroup extends Controller_Admin_App
{

	/**
	 * 修改主功能组名称
	 * @access  public
	 * @return  Response
	 */
	public function action_index($group_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		if(Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 1)) {
			$data['input_master_group_name'] = '';
			$data['error_message'] = '';
			
			//页面参数检查
			$master_group = Model_Functiongroup::SelectMasterGroupById($group_id);
			if(!$master_group) {
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
				exit;
			}
			
			$data['master_group_name'] = $master_group['function_group_name'];
			
			if(isset($_POST['page'])) {
				$error_message_list = array();
				
				$data['input_master_group_name'] = isset($_POST['master_group_name']) ? trim($_POST['master_group_name']) : '';
				
				if($_POST['page'] == 'modify_master_group') {
					if($data['input_master_group_name'] == $master_group['function_group_name']) {
						$error_message_list[] = '请输入与原名称不同的主功能组名称';
					} else {
						$params_update = array(
							'function_group_id' => $group_id,
							'function_group_name' => $data['input_master_group_name'],
						);
						//输入内容检查
						$result_check = Model_Functiongroup::CheckUpdateMasterGroupName($params_update);
						
						if($result_check['result']) {
							//数据更新
							$result_update = Model_Functiongroup::UpdateFunctionGroupName($params_update);
							
							if($result_update) {
								$_SESSION['update_master_group_success'] = true;
								header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
								exit;
							} else {
								$error_message_list[] = '数据库错误：数据添加失败';
							}
						} else {
							foreach($result_check['error'] as $update_error) {
								switch($update_error) {
									case 'empty_name':
										$error_message_list[] = '请输入主功能组名称';
										break;
									case 'long_name':
										$error_message_list[] = '主功能组名称不能超过30字';
										break;
									case 'dup_name':
										$error_message_list[] = '已存在该名称的主功能组，无法重复设定';
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
			return Response::forge(View::forge($this->template . '/admin/user/permission/modify_master_group', $data, false));
		} else {
			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
		}
	}

}