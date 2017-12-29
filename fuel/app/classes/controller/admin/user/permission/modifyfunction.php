<?php
/* 
 * 修改功能名称页
 */

class Controller_Admin_User_Permission_Modifyfunction extends Controller_Admin_App
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
		
		try {
			if(!is_numeric($function_id)) {
				//功能ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 1)) {
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//获取原本功能信息
				$function = Model_Function::SelectFunction(array('function_id' => $function_id));
				
				if(!$function) {
					//不存在该ID的功能
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
				
				//页面标题
				$data['page_title'] ='编辑功能';
				//表单页面索引
				$data['form_page_index'] = 'modify_function';
				
				//form控件默认值设定
				$data['master_group_name'] = $function['master_group_name'];
				$data['sub_group_name'] = $function['sub_group_name'];
				$data['sub_special_flag'] = $function['sub_special_flag'];
				$data['input_function_name'] = $function['function_name'];
				$data['input_special_flag'] = $function['function_special_flag'];
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是修改功能页
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_function_name'] = isset($_POST['function_name']) ? trim($_POST['function_name']) : $data['input_function_name'];
						$data['input_special_flag'] = isset($_POST['special_flag']) ? $_POST['special_flag'] : $data['input_special_flag'];
						
						//更新功能用数据生成
						$params_update = array(
							'function_id' => $function_id,
							'function_name' => $data['input_function_name'],
							'function_group_id' => $function['sub_group_id'],
							'special_flag' => $function['sub_special_flag'] ? '1' : $data['input_special_flag'],
						);
						
						//更新内容检查
						$result_check = Model_Function::CheckEditFunction($params_update);
						
						if($result_check['result']) {
							//数据更新
							$result_update = Model_Function::UpdateFunction($params_update);
							
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
									case 'empty_function_name':
										$error_message_list[] = '请输入功能名称';
										break;
									case 'long_function_name':
										$error_message_list[] = '功能名称不能超过30字';
										break;
									case 'dup_function_name':
										$error_message_list[] = '该副功能组中已存在该名称的功能，无法重复设定';
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
				return Response::forge(View::forge($this->template . '/admin/user/permission/edit_function', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}