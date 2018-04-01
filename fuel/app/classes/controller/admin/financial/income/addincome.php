<?php
/* 
 * 添加收入记录页
 */

class Controller_Admin_Financial_Income_Addincome extends Controller_Admin_App
{

	/**
	 * 添加收入记录页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($page = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		try {
			if(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 31)) {
				//当前登陆用户不具备添加收入记录的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//页面标题
				$data['page_title'] ='添加收入记录';
				//表单页面索引
				$data['form_page_index'] = 'add_income';
				//返回页URL
				$data['return_page_url'] = '/admin/income_list/';
				if(isset($_SERVER['HTTP_REFERER'])) {
					if(strstr($_SERVER['HTTP_REFERER'], 'admin/income_list')) {
						$data['return_page_url'] = $_SERVER['HTTP_REFERER'];
					}
				}
				
				//form控件默认值设定
				$data['input_income_type'] = '';
				$data['input_income_desc'] = '';
				$data['input_income_price'] = '';
				$data['input_income_at'] = '';
				$data['input_income_detail_list'] = array();
				
				$data['income_type_list'] = Model_Incometype::SelectIncomeTypeList(array('active_only' => true));
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是添加收入记录页
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
									$income_total = floatval(number_format(floatval($_POST['income_detail_each_' . $row_num]) * floatval($_POST['income_detail_count_' . $row_num]), 2, '.', ''));
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
						
						//添加收入记录用数据生成
						$param_insert = array(
							'income_id' => '',
							'income_type' => $data['input_income_type'],
							'income_desc' => $data['input_income_desc'],
							'income_price' => $data['input_income_price'],
							'income_at' => $data['input_income_at'],
							'created_by' => $_SESSION['login_user']['id'],
							'modified_by' => $_SESSION['login_user']['id'],
							'income_detail_list' => $data['input_income_detail_list'],
						);
						
						//输入内容检查
						$result_check = Model_Income::CheckEditIncome($param_insert);
						
						if($result_check['result']) {
							//添加收入记录
							$result_insert = Model_Income::InsertIncome($param_insert);
							
							if($result_insert) {
								$_SESSION['add_income_success'] = true;
								header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/income_detail/' . $result_insert . '/');
								exit;
							} else {
								$error_message_list[] = '数据库错误：数据添加失败';
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

}