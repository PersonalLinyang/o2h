<?php
/* 
 * 修改主功能组页
 */

class Controller_Admin_User_Permission_Modifymastergroup extends Controller_Admin_App
{

	/**
	 * 修改主功能组
	 * @access  public
	 * @return  Response
	 */
	public function action_index($group_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		try {
			if(!is_numeric($group_id)) {
				//主功能组ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 1)) {
				//当前登陆用户不具备编辑权限信息的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//获取原本主功能组信息
				$master_group = Model_Functiongroup::SelectMasterGroup(array('function_group_id' =>$group_id));
				
				if(!$master_group) {
					//不存在该ID的主功能组
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
				
				//页面标题
				$data['page_title'] ='编辑主功能组';
				//表单页面索引
				$data['form_page_index'] = 'modify_master_group';
				
				//form控件默认值设定
				$data['input_master_group_name'] = $master_group['function_group_name'];
				$data['input_special_flag'] = $master_group['special_flag'];
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是编辑主功能组页
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_master_group_name'] = isset($_POST['master_group_name']) ? trim($_POST['master_group_name']) : $data['input_master_group_name'];
						$data['input_special_flag'] = isset($_POST['special_flag']) ? $_POST['special_flag'] : $data['input_special_flag'];
						
						//更新主功能组用数据生成
						$params_update = array(
							'function_group_id' => $group_id,
							'function_group_name' => $data['input_master_group_name'],
							'function_group_type' => 1,
							'special_flag' => $data['input_special_flag'],
							'function_group_parent' => '',
						);
						
						//更新内容检查
						$result_check = Model_Functiongroup::CheckEditFunctionGroup($params_update);
						
						if($result_check['result']) {
							//数据更新
							$result_update = Model_Functiongroup::UpdateFunctionGroup($params_update);
							
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
									case 'empty_function_group_name':
										$error_message_list[] = '请输入主功能组名称';
										break;
									case 'long_function_group_name':
										$error_message_list[] = '主功能组名称不能超过30字';
										break;
									case 'dup_function_group_name':
										$error_message_list[] = '已存在该名称的主功能组，无法重复设定';
										break;
									default:
										$error_message_list[] = '发生系统错误,请重新尝试更新';
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
				return Response::forge(View::forge($this->template . '/admin/user/permission/edit_master_group', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}