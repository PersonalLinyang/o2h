<?php
/* 
 * 支出记录修改页
 */

class Controller_Admin_Financial_Cost_Modifycost extends Controller_Admin_App
{

	/**
	 * 支出记录修改页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($cost_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		try {
			if(!is_numeric($cost_id)) {
				//旅游路线ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 31)) {
				//当前登陆用户不具备支出记录修改的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//获取原本支出记录信息
				$cost = Model_Cost::SelectCost(array('cost_id' => $cost_id, 'active_only' => true));
				
				if(!$cost) {
					//不存在该ID的支出记录
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 13) && $cost['created_by'] != $_SESSION['login_user']['id']) {
					//该ID的支出记录为其他用户创建且当前登陆用户不具备编辑他人创建支出记录的权限
					return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
					exit;
				}
				
				//页面标题
				$data['page_title'] ='支出记录修改';
				//表单页面索引
				$data['form_page_index'] = 'modify_cost';
				//返回页URL
				$data['return_page_url'] = '/admin/cost_detail/' . $cost_id . '/';
				if(isset($_SERVER['HTTP_REFERER'])) {
					if(strstr($_SERVER['HTTP_REFERER'], 'admin/cost_list')) {
						$data['return_page_url'] = $_SERVER['HTTP_REFERER'];
					}
				}
				
				//form控件默认值设定
				$data['input_cost_type'] = $cost['cost_type'];
				$data['input_cost_desc'] = $cost['cost_desc'];
				$data['input_cost_price'] = $cost['cost_price'];
				$data['input_cost_at'] = date('Y-m-d', strtotime($cost['cost_at']));
				$data['input_cost_detail_list'] = $cost['cost_detail_list'];
				
				$data['cost_type_list'] = Model_Costtype::SelectCostTypeList(array('active_only' => true));
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是支出记录修改页
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_cost_type'] = isset($_POST['cost_type']) ? trim($_POST['cost_type']) : $data['input_cost_type'];
						$data['input_cost_desc'] = isset($_POST['cost_desc']) ? trim($_POST['cost_desc']) : $data['input_cost_desc'];
						$data['input_cost_at'] = isset($_POST['cost_at']) ? trim($_POST['cost_at']) : $data['input_cost_at'];
						
						//form控件值设定 支出明细
						$cost_price = 0;
						if(isset($_POST['cost_detail_row']) && is_array($_POST['cost_detail_row'])) {
							$data['input_cost_detail_list'] = array();
							foreach($_POST['cost_detail_row'] as $row_num) {
								$cost_total = 0;
								if(isset($_POST['cost_detail_each_' . $row_num]) && isset($_POST['cost_detail_count_' . $row_num])
										&& is_numeric($_POST['cost_detail_each_' . $row_num]) && is_numeric($_POST['cost_detail_count_' . $row_num])) {
									$cost_total = floatval($_POST['cost_detail_each_' . $row_num]) * floatval($_POST['cost_detail_count_' . $row_num]);
								}
								$data['input_cost_detail_list'][] = array(
									'cost_detail_desc' => isset($_POST['cost_detail_desc_' . $row_num]) ? trim($_POST['cost_detail_desc_' . $row_num]) : '',
									'cost_detail_each' => isset($_POST['cost_detail_each_' . $row_num]) ? trim($_POST['cost_detail_each_' . $row_num]) : '',
									'cost_detail_count' => isset($_POST['cost_detail_count_' . $row_num]) ? trim($_POST['cost_detail_count_' . $row_num]) : '',
									'cost_detail_total' => $cost_total,
								);
								
								$cost_price += $cost_total;
							}
						}
						$data['input_cost_price'] = $cost_price;
						
						//更新支出记录用数据生成
						$param_update = array(
							'cost_id' => $cost_id,
							'cost_type' => $data['input_cost_type'],
							'cost_desc' => $data['input_cost_desc'],
							'cost_price' => $data['input_cost_price'],
							'cost_at' => $data['input_cost_at'],
							'created_by' => $cost['created_by'],
							'modified_by' => $_SESSION['login_user']['id'],
							'cost_detail_list' => $data['input_cost_detail_list'],
						);
						
						//输入内容检查
						$result_check = Model_Cost::CheckEditCost($param_update);
						if($result_check['result']) {
							//更新支出记录信息
							$result_update = Model_Cost::UpdateCost($param_update);
							
							if($result_update) {
								//更新成功 页面跳转
								$_SESSION['modify_cost_success'] = true;
								header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/cost_detail/' . $cost_id . '/');
								exit;
							} else {
								$error_message_list[] = '数据库错误：数据更新失败';
							}
						} else {
							//获取错误信息
							foreach($result_check['error'] as $insert_error) {
								switch($insert_error) {
									case 'empty_cost_type':
										$error_message_list[] = '请选择支出项目';
										break;
									case 'empty_cost_desc':
										$error_message_list[] = '请输入支出说明';
										break;
									case 'empty_cost_at':
										$error_message_list[] = '请选择支出日期';
										break;
									case 'format_cost_at':
										$error_message_list[] = '支出日期格式不符合要求,例:2030/01/01';
										break;
									case 'error_cost_at':
										$error_message_list[] = '您选择的支出日期不存在,请重新选择';
										break;
									case 'empty_cost_detail_list':
										$error_message_list[] = '请至少添加一项的支出明细';
										break;
									case 'empty_cost_detail_desc':
										$error_message_list[] = '请输入支出明细的款项部分';
										break;
									case 'empty_cost_detail_each':
										$error_message_list[] = '请输入支出明细的单价部分';
										break;
									case 'error_cost_detail_each':
										$error_message_list[] = '请为支出明细的单价部分输入一个金额';
										break;
									case 'empty_cost_detail_count':
										$error_message_list[] = '请输入支出明细的数量部分';
										break;
									case 'error_cost_detail_count':
										$error_message_list[] = '请为支出明细的单价部分输入一个非负数字';
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
				return Response::forge(View::forge($this->template . '/admin/financial/cost/edit_cost', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}
	
	/**
	 * 确认状态更新
	 * @access  public
	 * @return  Response
	 */
	public function action_modifyapprovalstatus($param = null)
	{
		try {
			if(isset($_POST['page'], $_POST['modify_id'], $_POST['modify_value'])) {
				if(is_numeric($_POST['modify_id']) && $_POST['page'] == 'cost_detail') {
					$cost_id = $_POST['modify_id'];
					
					//获取支出记录信息
					$cost = Model_Cost::SelectCost(array('cost_id' => $cost_id, 'active_only' => true));
					
					if($cost) {
						$approval_able_flag = false;
						if($cost['approval_status'] == '0' || date('Y-m-d', strtotime($cost['approval_at'])) == date('Y-m-d', time())) {
							//是否具备支出记录确认权限
							$approval_able_flag = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 33);
						}
						
						if($approval_able_flag) {
							$params_update = array(
								'cost_id' => $cost_id,
								'approval_status' => $_POST['modify_value'],
								'approval_by' => $_SESSION['login_user']['id'],
							);
							
							$result_check = Model_Cost::CheckUpdateApprovalStatus($params_update);
							
							if($result_check['result']) {
								//数据更新
								$result_update = Model_Cost::UpdateApprovalStatus($params_update);
								
								if($result_update) {
									//更新成功
									$_SESSION['modify_cost_status_success'] = true;
									header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/cost_detail/' . $_POST['modify_id'] . '/');
									exit;
								}
							}
						}
					}
				}
			}
			
			//更新失敗
			$_SESSION['modify_cost_status_error'] = true;
			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/cost_detail/' . $_POST['modify_id'] . '/');
			exit;
		} catch (Exception $e) {
			//发生系统异常
			$_SESSION['modify_cost_status_error'] = true;
			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/cost_detail/' . $_POST['modify_id'] . '/');
			exit;
		}
	}

}