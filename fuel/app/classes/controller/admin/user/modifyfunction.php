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
	public function action_index($param = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['input_name'] = '';
			$data['master_group_name'] = '';
			$data['sub_group_name'] = '';
			$data['function_name'] = '';
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
			
			if(isset($_POST['page'], $_POST['name'])) {
				if($_POST['page'] == 'modify_function') {
					$params_update = array(
						'function_id' => $_GET['function_id'],
						'function_name' => trim($_POST['name']),
					);
					//输入内容检查
					$result_check = Model_Function::CheckUpdateFunction($params_update);
					
					if($result_check['result']) {
						//数据更新
						$result_update = Model_Function::UpdateFunction($params_update);
						
						if($result_update) {
							$_SESSION['update_function_success'] = true;
							header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
							exit;
						} else {
							$data['error_message'] = '数据库错误：数据更新失败';
						}
					} else {
						foreach($result_check['error'] as $update_error) {
							$error_message_list = array();
							switch($update_error) {
								case 'noset_id':
								case 'noset_name':
									$error_message_list[] = '系统错误：请勿修改表单中的控件名称';
									break;
								case 'nonum_id':
									$error_message_list[] = '功能编号不是数字';
									break;
								case 'empty_name':
									$error_message_list[] = '请输入修改后功能名称';
									break;
								case 'nomodify':
									$error_message_list[] = '请输入与原名称不同的功能名称';
									break;
								case 'duplication':
									$error_message_list[] = '该副功能组中已存在该名称的功能，无法重复设定';
									break;
								default:
									break;
							}
							$data['error_message'] = implode('<br/>', $error_message_list);
						}
					}
					
					$data['input_name'] = $_POST['name'];
				} else {
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
			}
			
			//调用View
			return Response::forge(View::forge($this->template . '/admin/user/modify_function', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}

}