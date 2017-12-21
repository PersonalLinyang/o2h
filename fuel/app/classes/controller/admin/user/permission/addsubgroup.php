<?php
/* 
 * 添加副功能组页
 */

class Controller_Admin_User_Permission_Addsubgroup extends Controller_Admin_App
{

	/**
	 * 添加副功能组
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		if(Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 1)) {
			$data['input_sub_group_name'] = '';
			$data['input_special_flag'] = '';
			$data['error_message'] = '';
			
			//页面参数检查
			if(!isset($_GET['master_group_id'])) {
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
				exit;
			} else {
				$master_group = Model_Functiongroup::SelectMasterGroupById($_GET['master_group_id']);
				if(!$master_group) {
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
			}
			
			$data['master_group_name'] = $master_group['function_group_name'];
			$data['master_special_flag'] = $master_group['special_flag'];
			
			if(isset($_POST['page'])) {
				$error_message_list = array();
				
				$data['input_sub_group_name'] = isset($_POST['sub_group_name']) ? trim($_POST['sub_group_name']) : '';
				$data['input_special_flag'] = isset($_POST['special_flag']) ? $_POST['special_flag'] : '';
				
				if($_POST['page'] == 'add_sub_group') {
					$params_insert = array(
						'function_group_name' => $data['input_sub_group_name'],
						'function_group_parent' => $_GET['master_group_id'],
						'special_flag' => $master_group['special_flag'] ? '1' : $data['input_special_flag'],
					);
					//输入内容检查
					$result_check = Model_Functiongroup::CheckInsertSubGroup($params_insert);
					
					if($result_check['result']) {
						//数据添加
						$result_insert = Model_Functiongroup::InsertSubGroup($params_insert);
						
						if($result_insert) {
							$_SESSION['add_sub_group_success'] = true;
							header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
							exit;
						} else {
							$error_message_list[] = '数据库错误：数据添加失败';
						}
					} else {
						foreach($result_check['error'] as $insert_error) {
							switch($insert_error) {
								case 'empty_name':
									$error_message_list[] = '请输入副功能组名称';
									break;
								case 'long_name':
									$error_message_list[] = '副功能组名称不能超过30字';
									break;
								case 'dup_name':
									$error_message_list[] = '该主功能组中已存在该名称的副功能组，无法重复添加';
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
			return Response::forge(View::forge($this->template . '/admin/user/permission/add_sub_group', $data, false));
		} else {
			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
		}
	}

}