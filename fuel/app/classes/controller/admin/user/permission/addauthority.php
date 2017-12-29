<?php
/* 
 * 添加权限页
 */

class Controller_Admin_User_Permission_Addauthority extends Controller_Admin_App
{

	/**
	 * 添加权限
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
				//所属功能ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 1)) {
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//获取所属功能信息
				$function = Model_Function::SelectFunction(array('function_id' => $function_id));
				
				if(!$function) {
					//不存在该ID的功能
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
				
				//页面标题
				$data['page_title'] ='添加权限';
				//表单页面索引
				$data['form_page_index'] = 'add_authority';
				
				//form控件默认值设定
				$data['master_group_name'] = $function['master_group_name'];
				$data['sub_group_name'] = $function['sub_group_name'];
				$data['function_name'] = $function['function_name'];
				$data['function_special_flag'] = $function['function_special_flag'];
				$data['input_authority_name'] = '';
				$data['input_special_flag'] = '0';
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是添加添加权限页
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_authority_name'] = isset($_POST['authority_name']) ? trim($_POST['authority_name']) : $data['input_authority_name'];
						$data['input_special_flag'] = isset($_POST['special_flag']) ? $_POST['special_flag'] : $data['input_special_flag'];
						
						//添加权限用数据生成
						$params_insert = array(
							'authority_id' => '',
							'authority_name' => $data['input_authority_name'],
							'function_id' => $function_id,
							'special_flag' => $function['function_special_flag'] ? '1' : $data['input_special_flag'],
						);
						
						//添加内容检查
						$result_check = Model_Authority::CheckEditAuthority($params_insert);
						
						if($result_check['result']) {
							//数据添加
							$result_insert = Model_Authority::InsertAuthority($params_insert);
							
							if($result_insert) {
								$_SESSION['add_authority_success'] = true;
								header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
								exit;
							} else {
								$error_message_list[] = '数据库错误：数据添加失败';
							}
						} else {
							foreach($result_check['error'] as $insert_error) {
								switch($insert_error) {
									case 'empty_authority_name':
										$error_message_list[] = '请输入权限名称';
										break;
									case 'long_authority_name':
										$error_message_list[] = '权限名称不能超过30字';
										break;
									case 'dup_authority_name':
										$error_message_list[] = '该功能中已存在该名称的权限，无法重复添加';
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
				return Response::forge(View::forge($this->template . '/admin/user/permission/edit_authority', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}