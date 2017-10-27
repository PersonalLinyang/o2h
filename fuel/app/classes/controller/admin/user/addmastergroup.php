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
			$data['input_name'] = '';
			$data['error_message'] = '';
			
			if(isset($_POST['page'], $_POST['name'])) {
				if($_POST['page'] == 'add_mg') {
					$params_insert = array(
						'function_group_name' => trim($_POST['name']),
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
									$error_message_list[] = '请输入主功能组名称';
									break;
								case 'duplication':
									$error_message_list[] = '已存在该名称的主功能组，无法重复添加';
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
			return Response::forge(View::forge($this->template . '/admin/user/add_master_group', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}

}