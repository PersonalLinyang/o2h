<?php
/* 
 * 添加主功能组页
 */

class Controller_Admin_User_Addmastergroup extends Controller_Admin_App
{

	/**
	 * 添加主功能组
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['input_master_group_name'] = '';
			$data['input_special_flag'] = '';
			$data['error_message'] = '';
			
			if(isset($_POST['page'])) {
				$error_message_list = array();
				
				$data['input_master_group_name'] = isset($_POST['master_group_name']) ? trim($_POST['master_group_name']) : '';
				$data['input_special_flag'] = isset($_POST['special_flag']) ? $_POST['special_flag'] : '';
				
				if($_POST['page'] == 'add_master_group') {
					$params_insert = array(
						'function_group_name' => $data['input_master_group_name'],
						'special_flag' => $data['input_special_flag'],
					);
					//输入内容检查
					$result_check = Model_Functiongroup::CheckInsertMasterGroup($params_insert);
					
					if($result_check['result']) {
						//数据添加
						$result_insert = Model_Functiongroup::InsertMasterGroup($params_insert);
						
						if($result_insert) {
							$_SESSION['add_master_group_success'] = true;
							header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
							exit;
						} else {
							$error_message_list[] = '数据库错误：数据添加失败';
						}
					} else {
						foreach($result_check['error'] as $insert_error) {
							switch($insert_error) {
								case 'empty_name':
									$error_message_list[] = '请输入主功能组名称';
									break;
								case 'long_name':
									$error_message_list[] = '主功能组名称不能超过50字';
									break;
								case 'dup_name':
									$error_message_list[] = '已存在该名称的主功能组，无法重复添加';
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
			return Response::forge(View::forge($this->template . '/admin/user/add_master_group', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}

}