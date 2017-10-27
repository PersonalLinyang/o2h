<?php
/* 
 * 修改权限名称页
 */

class Controller_Admin_User_Modifyauthority extends Controller_Admin_App
{

	/**
	 * 修改权限名称
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
			$data['authority_name'] = '';
			$data['error_message'] = '';
			
			//页面参数检查
			if(!isset($_GET['authority_id'])) {
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
				exit;
			} else {
				$authority = Model_Authority::SelectAuthorityById($_GET['authority_id']);
				if(!$authority) {
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
			}
			
			$data['master_group_name'] = $authority['master_group_name'];
			$data['sub_group_name'] = $authority['sub_group_name'];
			$data['function_name'] = $authority['function_name'];
			$data['authority_name'] = $authority['authority_name'];
			
			if(isset($_POST['page'], $_POST['name'])) {
				if($_POST['page'] == 'modify_authority') {
					$params_update = array(
						'authority_id' => $_GET['authority_id'],
						'authority_name' => trim($_POST['name']),
					);
					//输入内容检查
					$result_check = Model_Authority::CheckUpdateAuthority($params_update);
					
					if($result_check['result']) {
						//数据更新
						$result_update = Model_Authority::UpdateAuthority($params_update);
						
						if($result_update) {
							$_SESSION['update_authority_success'] = true;
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
									$error_message_list[] = '权限编号不是数字';
									break;
								case 'empty_name':
									$error_message_list[] = '请输入修改后权限名称';
									break;
								case 'nomodify':
									$error_message_list[] = '请输入与原名称不同的权限名称';
									break;
								case 'duplication':
									$error_message_list[] = '该功能组已存在该名称的权限，无法重复设定';
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
			return Response::forge(View::forge($this->template . '/admin/user/modify_authority', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}

}