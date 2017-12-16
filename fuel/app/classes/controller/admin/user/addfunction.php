<?php
/* 
 * 添加功能页
 */

class Controller_Admin_User_Addfunction extends Controller_Admin_App
{

	/**
	 * 添加功能
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['input_function_name'] = '';
			$data['input_special_flag'] = '';
			$data['error_message'] = '';
			
			//页面参数检查
			if(!isset($_GET['sub_group_id'])) {
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
				exit;
			} else {
				$sub_group = Model_Functiongroup::SelectSubGroupById($_GET['sub_group_id']);
				if(!$sub_group) {
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
			}
			
			$data['master_group_name'] = $sub_group['master_group_name'];
			$data['sub_group_name'] = $sub_group['sub_group_name'];
			$data['sub_special_flag'] = $sub_group['sub_special_flag'];
			
			if(isset($_POST['page'])) {
				$error_message_list = array();
				
				$data['input_function_name'] = isset($_POST['function_name']) ? trim($_POST['function_name']) : '';
				$data['input_special_flag'] = isset($_POST['special_flag']) ? $_POST['special_flag'] : '';
				
				if($_POST['page'] == 'add_function') {
					$params_insert = array(
						'function_name' => $data['input_function_name'],
						'function_group_id' => $_GET['sub_group_id'],
						'special_flag' => $sub_group['sub_special_flag'] ? '1' : $data['input_special_flag'],
					);
					//输入内容检查
					$result_check = Model_Function::CheckInsertFunction($params_insert);
					
					if($result_check['result']) {
						//数据添加
						$result_insert = Model_Function::InsertFunction($params_insert);
						
						if($result_insert) {
							$_SESSION['add_function_success'] = true;
							header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
							exit;
						} else {
							$data['error_message'] = '数据库错误：数据添加失败';
						}
					} else {
						foreach($result_check['error'] as $insert_error) {
							$error_message_list = array();
							switch($insert_error) {
								case 'empty_name':
									$error_message_list[] = '请输入功能名称';
									break;
								case 'long_name':
									$error_message_list[] = '功能名称不能超过50字';
									break;
								case 'dup_name':
									$error_message_list[] = '该副功能组中已存在该名称的功能，无法重复添加';
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
			return Response::forge(View::forge($this->template . '/admin/user/add_function', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}

}