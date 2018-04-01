<?php
/* 
 * 收入记录修改页
 */

class Controller_Admin_Financial_Income_Modifyincome extends Controller_Admin_App
{

	/**
	 * 收入记录修改页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($income_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		try {
			if(!is_numeric($income_id)) {
				//旅游路线ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 31)) {
				//当前登陆用户不具备收入记录修改的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//获取原本收入记录信息
				$income = Model_Income::SelectIncome(array('income_id' => $income_id, 'active_only' => true));
				
				if(!$income) {
					//不存在该ID的收入记录
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 13) && $income['created_by'] != $_SESSION['login_user']['id']) {
					//该ID的收入记录为其他用户创建且当前登陆用户不具备编辑他人创建收入记录的权限
					return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
					exit;
				}
				
				//页面标题
				$data['page_title'] ='收入记录修改';
				//表单页面索引
				$data['form_page_index'] = 'modify_income';
				//返回页URL
				$data['return_page_url'] = '/admin/income_detail/' . $income_id . '/';
				if(isset($_SERVER['HTTP_REFERER'])) {
					if(strstr($_SERVER['HTTP_REFERER'], 'admin/income_list')) {
						$data['return_page_url'] = $_SERVER['HTTP_REFERER'];
					}
				}
				
				//form控件默认值设定
				$data['input_income_type'] = $income['income_type'];
				$data['input_income_desc'] = $income['income_desc'];
				$data['input_income_price'] = $income['income_price'];
				$data['input_income_at'] = date('Y-m-d', strtotime($income['income_at']));
				$data['input_income_detail_list'] = $income['income_detail_list'];
				
				$data['income_type_list'] = Model_Incometype::SelectIncomeTypeList(array('active_only' => true));
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是收入记录修改页
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_income_type'] = isset($_POST['income_type']) ? trim($_POST['income_type']) : $data['input_income_type'];
						$data['input_income_desc'] = isset($_POST['income_desc']) ? trim($_POST['income_desc']) : $data['input_income_desc'];
						$data['input_income_at'] = isset($_POST['income_at']) ? trim($_POST['income_at']) : $data['input_income_at'];
						
						//form控件值设定 收入明细
						$income_price = 0;
						if(isset($_POST['income_detail_row']) && is_array($_POST['income_detail_row'])) {
							$data['input_income_detail_list'] = array();
							foreach($_POST['income_detail_row'] as $row_num) {
								$income_total = 0;
								if(isset($_POST['income_detail_each_' . $row_num]) && isset($_POST['income_detail_count_' . $row_num])
										&& is_numeric($_POST['income_detail_each_' . $row_num]) && is_numeric($_POST['income_detail_count_' . $row_num])) {
									$income_total = floatval($_POST['income_detail_each_' . $row_num]) * floatval($_POST['income_detail_count_' . $row_num]);
								}
								$data['input_income_detail_list'][] = array(
									'income_detail_desc' => isset($_POST['income_detail_desc_' . $row_num]) ? trim($_POST['income_detail_desc_' . $row_num]) : '',
									'income_detail_each' => isset($_POST['income_detail_each_' . $row_num]) ? trim($_POST['income_detail_each_' . $row_num]) : '',
									'income_detail_count' => isset($_POST['income_detail_count_' . $row_num]) ? trim($_POST['income_detail_count_' . $row_num]) : '',
									'income_detail_total' => $income_total,
								);
								
								$income_price += $income_total;
							}
						}
						$data['input_income_price'] = $income_price;
						
						//更新收入记录用数据生成
						$param_update = array(
							'income_id' => $income_id,
							'income_type' => $data['input_income_type'],
							'income_desc' => $data['input_income_desc'],
							'income_price' => $data['input_income_price'],
							'income_at' => $data['input_income_at'],
							'created_by' => $income['created_by'],
							'modified_by' => $_SESSION['login_user']['id'],
							'income_detail_list' => $data['input_income_detail_list'],
						);
						
						//输入内容检查
						$result_check = Model_Income::CheckEditIncome($param_update);
						if($result_check['result']) {
							//更新收入记录信息
							$result_update = Model_Income::UpdateIncome($param_update);
							
							if($result_update) {
								//更新成功 页面跳转
								$_SESSION['modify_income_success'] = true;
								header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/income_detail/' . $income_id . '/');
								exit;
							} else {
								$error_message_list[] = '数据库错误：数据更新失败';
							}
						} else {
							//获取错误信息
							foreach($result_check['error'] as $insert_error) {
								switch($insert_error) {
									case 'empty_income_type':
										$error_message_list[] = '请选择收入项目';
										break;
									case 'empty_income_desc':
										$error_message_list[] = '请输入收入说明';
										break;
									case 'empty_income_at':
										$error_message_list[] = '请选择收入日期';
										break;
									case 'format_income_at':
										$error_message_list[] = '收入日期格式不符合要求,例:2030/01/01';
										break;
									case 'error_income_at':
										$error_message_list[] = '您选择的收入日期不存在,请重新选择';
										break;
									case 'empty_income_detail_list':
										$error_message_list[] = '请至少添加一项的收入明细';
										break;
									case 'empty_income_detail_desc':
										$error_message_list[] = '请输入收入明细的款项部分';
										break;
									case 'empty_income_detail_each':
										$error_message_list[] = '请输入收入明细的单价部分';
										break;
									case 'error_income_detail_each':
										$error_message_list[] = '请为收入明细的单价部分输入一个金额';
										break;
									case 'empty_income_detail_count':
										$error_message_list[] = '请输入收入明细的数量部分';
										break;
									case 'error_income_detail_count':
										$error_message_list[] = '请为收入明细的单价部分输入一个非负数字';
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
				return Response::forge(View::forge($this->template . '/admin/financial/income/edit_income', $data, false));
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
				if(is_numeric($_POST['modify_id']) && $_POST['page'] == 'income_detail') {
					$income_id = $_POST['modify_id'];
					
					//获取收入记录信息
					$income = Model_Income::SelectIncome(array('income_id' => $income_id, 'active_only' => true));
					
					if($income) {
						$approval_able_flag = false;
						if($income['approval_status'] == '0' || date('Y-m-d', strtotime($income['approval_at'])) == date('Y-m-d', time())) {
							//是否具备收入记录确认权限
							$approval_able_flag = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 33);
						}
						
						if($approval_able_flag) {
							$params_update = array(
								'income_id' => $income_id,
								'approval_status' => $_POST['modify_value'],
								'approval_by' => $_SESSION['login_user']['id'],
							);
							
							$result_check = Model_Income::CheckUpdateApprovalStatus($params_update);
							
							if($result_check['result']) {
								//数据更新
								$result_update = Model_Income::UpdateApprovalStatus($params_update);
								
								if($result_update) {
									//更新成功
									$_SESSION['modify_income_status_success'] = true;
									header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/income_detail/' . $_POST['modify_id'] . '/');
									exit;
								}
							}
						}
					}
				}
			}
			
			//更新失敗
			$_SESSION['modify_income_status_error'] = true;
			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/income_detail/' . $_POST['modify_id'] . '/');
			exit;
		} catch (Exception $e) {
			//发生系统异常
			$_SESSION['modify_income_status_error'] = true;
			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/income_detail/' . $_POST['modify_id'] . '/');
			exit;
		}
	}

}