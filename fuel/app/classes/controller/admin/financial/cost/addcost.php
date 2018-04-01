<?php
/* 
 * 添加支出记录页
 */

class Controller_Admin_Financial_Cost_Addcost extends Controller_Admin_App
{

	/**
	 * 添加支出记录页
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
				//当前登陆用户不具备添加支出记录的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//页面标题
				$data['page_title'] ='添加支出记录';
				//表单页面索引
				$data['form_page_index'] = 'add_cost';
				//返回页URL
				$data['return_page_url'] = '/admin/cost_list/';
				if(isset($_SERVER['HTTP_REFERER'])) {
					if(strstr($_SERVER['HTTP_REFERER'], 'admin/cost_list')) {
						$data['return_page_url'] = $_SERVER['HTTP_REFERER'];
					}
				}
				
				//form控件默认值设定
				$data['input_cost_type'] = '';
				$data['input_cost_desc'] = '';
				$data['input_cost_price'] = '';
				$data['input_cost_at'] = '';
				$data['input_cost_detail_list'] = array();
				
				$data['cost_type_list'] = Model_Costtype::SelectCostTypeList(array('active_only' => true));
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是添加支出记录页
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
									$cost_total = floatval(number_format(floatval($_POST['cost_detail_each_' . $row_num]) * floatval($_POST['cost_detail_count_' . $row_num]), 2, '.', ''));
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
						
						//添加支出记录用数据生成
						$param_insert = array(
							'cost_id' => '',
							'cost_type' => $data['input_cost_type'],
							'cost_desc' => $data['input_cost_desc'],
							'cost_price' => $data['input_cost_price'],
							'cost_at' => $data['input_cost_at'],
							'created_by' => $_SESSION['login_user']['id'],
							'modified_by' => $_SESSION['login_user']['id'],
							'cost_detail_list' => $data['input_cost_detail_list'],
						);
						
						//输入内容检查
						$result_check = Model_Cost::CheckEditCost($param_insert);
						
						if($result_check['result']) {
							//添加支出记录
							$result_insert = Model_Cost::InsertCost($param_insert);
							
							if($result_insert) {
								$_SESSION['add_cost_success'] = true;
								header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/cost_detail/' . $result_insert . '/');
								exit;
							} else {
								$error_message_list[] = '数据库错误：数据添加失败';
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

}