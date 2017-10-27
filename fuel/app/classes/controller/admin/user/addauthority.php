<?php
/* 
 * 添加权限页
 */

class Controller_Admin_User_Addauthority extends Controller_Admin_App
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
				if($_POST['page'] == 'add_authority') {
					$params_insert = array(
						'authority_name' => trim($_POST['name']),
						'function_id' => $_GET['function_id'],
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
							$data['error_message'] = '数据库错误：数据添加失败';
						}
					} else {
						foreach($result_check['error'] as $insert_error) {
							$error_message_list = array();
							switch($insert_error) {
								case 'noset_name':
									$error_message_list[] = '系统错误：请勿修改表单中的控件名称';
									break;
								case 'empty_name':
									$error_message_list[] = '请输入权限名称';
									break;
								case 'noset_function':
									$error_message_list[] = '请设定所属功能';
									break;
								case 'nonum_function':
									$error_message_list[] = '功能编号不是数字';
									break;
								case 'duplication':
									$error_message_list[] = '该功能中已存在该名称的权限，无法重复添加';
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
			return Response::forge(View::forge($this->template . '/admin/user/add_authority', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}

}