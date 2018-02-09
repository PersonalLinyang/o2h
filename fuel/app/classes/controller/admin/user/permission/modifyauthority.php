<?php
/* 
 * 修改权限名称页
 */

class Controller_Admin_User_Permission_Modifyauthority extends Controller_Admin_App
{

	/**
	 * 修改权限名称
	 * @access  public
	 * @return  Response
	 */
	public function action_index($authority_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		try {
			if(!is_numeric($authority_id)) {
				//权限ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 1)) {
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//获取原本权限信息
				$authority = Model_Authority::SelectAuthority(array('authority_id' => $authority_id));
				
				if(!$authority) {
					//不存在该ID的权限
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
				
				//页面标题
				$data['page_title'] ='编辑权限';
				//表单页面索引
				$data['form_page_index'] = 'modify_authority';
				
				//form控件默认值设定
				$data['master_group_name'] = $authority['master_group_name'];
				$data['sub_group_name'] = $authority['sub_group_name'];
				$data['function_name'] = $authority['function_name'];
				$data['function_special_flag'] = $authority['function_special_flag'];
				$data['input_authority_name'] = $authority['authority_name'];
				$data['input_special_flag'] = $authority['authority_special_flag'];
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是修改权限页
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_authority_name'] = isset($_POST['authority_name']) ? trim($_POST['authority_name']) : $data['input_authority_name'];
						$data['input_special_flag'] = isset($_POST['special_flag']) ? $_POST['special_flag'] : $data['input_special_flag'];
						
						//更新权限用数据生成
						$params_update = array(
							'authority_id' => $authority_id,
							'authority_name' => $data['input_authority_name'],
							'function_id' => $authority['function_id'],
							'special_flag' => $authority['sub_special_flag'] ? '1' : $data['input_special_flag'],
						);
						
						//更新内容检查
						$result_check = Model_Authority::CheckEditAuthority($params_update);
						
						if($result_check['result']) {
							//数据更新
							$result_update = Model_Authority::UpdateAuthority($params_update);
							
							if($result_update) {
								$_SESSION['update_authority_success'] = true;
								header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
								exit;
							} else {
								$error_message_list[] = '数据库错误：数据添加失败';
							}
						} else {
							foreach($result_check['error'] as $update_error) {
								switch($update_error) {
									case 'empty_authority_name':
										$error_message_list[] = '请输入权限名称';
										break;
									case 'long_authority_name':
										$error_message_list[] = '权限名称不能超过30字';
										break;
									case 'dup_authority_name':
										$error_message_list[] = '该功能中已存在该名称的权限，无法重复设定';
										break;
									default:
										$error_message_list[] = '发生系统错误,请重新尝试更新';
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