<?php
/* 
 * 修改副功能组页
 */

class Controller_Admin_User_Permission_Modifysubgroup extends Controller_Admin_App
{

	/**
	 * 修改副功能组
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
				//副功能组ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 1)) {
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//获取原本副功能组信息
				$sub_group = Model_Functiongroup::SelectSubGroup(array('sub_group_id' => $group_id));
				
				if(!$sub_group) {
					//不存在该ID的副功能组
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
				
				//页面标题
				$data['page_title'] ='编辑副功能组';
				//表单页面索引
				$data['form_page_index'] = 'modify_sub_group';
				
				//form控件默认值设定
				$data['master_group_name'] = $sub_group['master_group_name'];
				$data['master_special_flag'] = $sub_group['master_special_flag'];
				$data['input_sub_group_name'] = $sub_group['sub_group_name'];
				$data['input_special_flag'] = $sub_group['sub_special_flag'];
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是修改副功能组页
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_sub_group_name'] = isset($_POST['sub_group_name']) ? trim($_POST['sub_group_name']) : $data['input_sub_group_name'];
						$data['input_special_flag'] = isset($_POST['special_flag']) ? $_POST['special_flag'] : $data['input_special_flag'];
						
						//更新副功能组用数据生成
						$params_update = array(
							'function_group_id' => $group_id,
							'function_group_name' => $data['input_sub_group_name'],
							'function_group_type' => 2,
							'special_flag' => $sub_group['master_special_flag'] ? '1' : $data['input_special_flag'],
							'function_group_parent' => $sub_group['master_group_id'],
						);
						
						//更新内容检查
						$result_check = Model_Functiongroup::CheckEditFunctionGroup($params_update);
						
						if($result_check['result']) {
							//数据更新
							$result_update = Model_Functiongroup::UpdateFunctionGroup($params_update);
							
							if($result_update) {
								$_SESSION['update_sub_group_success'] = true;
								header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
								exit;
							} else {
								$error_message_list[] = '数据库错误：数据添加失败';
							}
						} else {
							foreach($result_check['error'] as $update_error) {
								switch($update_error) {
									case 'empty_function_group_name':
										$error_message_list[] = '请输入副功能组名称';
										break;
									case 'long_function_group_name':
										$error_message_list[] = '副功能组名称不能超过30字';
										break;
									case 'dup_function_group_name':
										$error_message_list[] = '该主功能组中已存在该名称的副功能组，无法重复设定';
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
				return Response::forge(View::forge($this->template . '/admin/user/permission/edit_sub_group', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}