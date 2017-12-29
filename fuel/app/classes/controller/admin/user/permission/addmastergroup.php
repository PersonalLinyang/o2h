<?php
/* 
 * 添加主功能组页
 */

class Controller_Admin_User_Permission_Addmastergroup extends Controller_Admin_App
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
		
		try {
			if(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 1)) {
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//页面标题
				$data['page_title'] ='添加主功能组';
				//表单页面索引
				$data['form_page_index'] = 'add_master_group';
				
				//form控件默认值设定
				$data['input_master_group_name'] = '';
				$data['input_special_flag'] = '0';
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是添加主功能组页
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_master_group_name'] = isset($_POST['master_group_name']) ? trim($_POST['master_group_name']) : $data['input_master_group_name'];
						$data['input_special_flag'] = isset($_POST['special_flag']) ? $_POST['special_flag'] : $data['input_special_flag'];
						
						//添加主功能组用数据生成
						$params_insert = array(
							'function_group_id' => '',
							'function_group_name' => $data['input_master_group_name'],
							'function_group_type' => 1,
							'special_flag' => $data['input_special_flag'],
							'function_group_parent' => '',
						);
						
						//添加内容检查
						$result_check = Model_Functiongroup::CheckEditFunctionGroup($params_insert);
						
						if($result_check['result']) {
							//数据添加
							$result_insert = Model_Functiongroup::InsertFunctionGroup($params_insert);
							
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
									case 'empty_function_group_name':
										$error_message_list[] = '请输入主功能组名称';
										break;
									case 'long_function_group_name':
										$error_message_list[] = '主功能组名称不能超过30字';
										break;
									case 'dup_function_group_name':
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
					}
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/user/permission/edit_master_group', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}