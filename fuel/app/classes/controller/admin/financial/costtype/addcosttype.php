<?php
/* 
 * 添加支出项目页
 */

class Controller_Admin_Financial_Costtype_AddCostType extends Controller_Admin_App
{

	/**
	 * 添加支出项目
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		try {
			if(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 20)) {
				//当前登陆用户不具备支出项目管理的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//页面标题
				$data['page_title'] ='添加支出项目';
				//表单页面索引
				$data['form_page_index'] = 'add_cost_type';
				
				//form控件默认值设定
				$data['input_cost_type_name'] = '';
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是添加支出项目页
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_cost_type_name'] = isset($_POST['cost_type_name']) ? trim($_POST['cost_type_name']) : $data['input_cost_name'];
						
						//添加支出项目用数据生成
						$params_insert = array(
							'cost_type_id' => '',
							'cost_type_name' => $data['input_cost_type_name'],
						);
						
						//输入内容检查
						$result_check = Model_Costtype::CheckEditCostType($params_insert);
						
						if($result_check['result']) {
							//添加支出项目
							$result_insert = Model_Costtype::InsertCostType($params_insert);
							
							if($result_insert) {
								//添加成功 页面跳转
								$_SESSION['add_cost_type_success'] = true;
								header('Location: //' . $_SERVER['HTTP_HOST'] . '/admin/cost_type_list/');
								exit;
							} else {
								$error_message_list[] = '数据库错误：数据添加失败';
							}
						} else {
							//获取错误信息
							foreach($result_check['error'] as $insert_error) {
								switch($insert_error) {
									case 'empty_cost_type_name': 
										$error_message_list[] = '请输入支出项目名';
										break;
									case 'long_cost_type_name': 
										$error_message_list[] = '支出项目名不能超过50字';
										break;
									case 'dup_cost_type_name': 
										$error_message_list[] = '该支出项目名与其他支出项目重复,请选用其他支出项目名';
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
				return Response::forge(View::forge($this->template . '/admin/financial/cost_type/edit_cost_type', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}