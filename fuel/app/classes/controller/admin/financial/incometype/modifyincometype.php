<?php
/* 
 * 收入项目修改页
 */

class Controller_Admin_Financial_Incometype_Modifyincometype extends Controller_Admin_App
{

	/**
	 * 收入项目修改页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($income_type_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		try {
			if(!is_numeric($income_type_id)) {
				//收入项目ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 20)) {
				//当前登陆用户不具备收入项目管理的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//获取原本收入项目信息
				$params_select = array(
					'income_type_id' => $income_type_id,
					'active_only' => true,
				);
				$income_type = Model_Incometype::SelectIncomeType($params_select);
				
				if(!$income_type) {
					//不存在该ID的收入项目
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
				
				//页面标题
				$data['page_title'] ='收入项目修改';
				//表单页面索引
				$data['form_page_index'] = 'modify_income_type';
				
				//form控件默认值设定
				$data['input_income_type_name'] = $income_type['income_type_name'];
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是收入项目信息修改页
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_income_type_name'] = isset($_POST['income_type_name']) ? trim($_POST['income_type_name']) : $data['input_income_name'];
						
						//修改收入项目用数据生成
						$params_update = array(
							'income_type_id' => $income_type_id,
							'income_type_name' => $data['input_income_type_name'],
						);
						
						//输入内容检查
						$result_check = Model_Incometype::CheckEditIncomeType($params_update);
						
						if($result_check['result']) {
							//更新收入项目信息
							$result_update = Model_Incometype::UpdateIncomeType($params_update);
							
							if($result_update) {
								//修改成功 页面跳转
								$_SESSION['modify_income_type_success'] = true;
								header('Location: //' . $_SERVER['HTTP_HOST'] . '/admin/income_type_list/');
								exit;
							} else {
								$error_message_list[] = '数据库错误：数据修改失败';
							}
						} else {
							//获取错误信息
							foreach($result_check['error'] as $update_error) {
								switch($update_error) {
									case 'empty_income_type_name': 
										$error_message_list[] = '请输入收入项目名';
										break;
									case 'long_income_type_name': 
										$error_message_list[] = '收入项目名不能超过50字';
										break;
									case 'dup_income_type_name': 
										$error_message_list[] = '该收入项目名与其他收入项目重复,请选用其他收入项目名';
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
				return Response::forge(View::forge($this->template . '/admin/financial/income_type/edit_income_type', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}