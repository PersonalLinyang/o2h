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
	public function action_index($param = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		if(Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 1)) {
			$data['input_authority_name'] = '';
			$data['input_special_flag'] = '';
			$data['error_message'] = '';
			
			//页面参数检查
			if(!isset($_GET['function_id'])) {
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
				exit;
			} else {
				$function = Model_Function::SelectFunctionById($_GET['function_id']);
				if(!$function) {
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
			}
			
			$data['master_group_name'] = $function['master_group_name'];
			$data['sub_group_name'] = $function['sub_group_name'];
			$data['function_name'] = $function['function_name'];
			$data['function_special_flag'] = $function['function_special_flag'];
			
			if(isset($_POST['page'])) {
				$error_message_list = array();
				
				$data['input_authority_name'] = isset($_POST['authority_name']) ? trim($_POST['authority_name']) : '';
				$data['input_special_flag'] = isset($_POST['special_flag']) ? $_POST['special_flag'] : '';
				
				if($_POST['page'] == 'add_authority') {
					$params_insert = array(
						'authority_name' => $data['input_authority_name'],
						'function_id' => $_GET['function_id'],
						'special_flag' => $function['function_special_flag'] ? '1' : $data['input_special_flag'],
					);
					//输入内容检查
					$result_check = Model_Authority::CheckInsertAuthority($params_insert);
					
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
								case 'empty_name':
									$error_message_list[] = '请输入权限名称';
									break;
								case 'long_name':
									$error_message_list[] = '权限名称不能超过30字';
									break;
								case 'dup_name':
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
				} else {
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
			}
			
			//调用View
			return Response::forge(View::forge($this->template . '/admin/user/permission/add_authority', $data, false));
		} else {
			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
		}
	}

}