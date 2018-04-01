<?php
/* 
 * 支出记录一览页
 */

class Controller_Admin_Financial_Cost_Costlist extends Controller_Admin_App
{

	/**
	 * 支出记录一览
	 * @access  public
	 * @return  Response
	 */
	public function action_index($page = 1)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		//当前登陆用户
		$login_user_id = $_SESSION['login_user']['id'];
		
		try {
			if(!is_numeric($page)) {
				//页数不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($login_user_id, 'sub_group', 15)) {
				//当前登陆用户不具备查看支出记录的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['success_message'] = '';
				$data['error_message'] = '';
				
				//获取自身用户ID
				$data['user_id_self'] = $login_user_id;
				//获取支出类型列表
				$data['cost_type_list'] = Model_Costtype::SelectCostTypeList(array('active_only' => true));
				
				//是否具备支出记录编辑权限
				$data['edit_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'function', 31);
				//是否具备其他用户所登陆的支出记录编辑权限
				$data['edit_other_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'authority', 13);
				//是否具备支出记录删除权限
				$data['delete_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'function', 32);
				//是否具备其他用户所登陆的支出记录删除权限
				$data['delete_other_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'authority', 14);
				//是否具备导出支出记录信息权限
				$data['export_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'function', 34);
				//是否具备支出项目管理权限
				$data['cost_type_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'function', 35);
				
				//每页显示支出记录数
				$num_per_page = 20;
				//本页前后最大可链接页数
				$data['page_link_max'] = 3;
				
				//检索条件
				$data['select_cost_desc'] = isset($_GET['select_cost_desc']) ? preg_replace('/( |　)/', ' ', $_GET['select_cost_desc']) : '';
				$data['select_cost_type'] = isset($_GET['select_cost_type']) && is_array($_GET['select_cost_type']) ? $_GET['select_cost_type'] : array();
				$data['select_price_min'] = isset($_GET['select_price_min']) ? $_GET['select_price_min'] : '';
				$data['select_price_max'] = isset($_GET['select_price_max']) ? $_GET['select_price_max'] : '';
				$data['select_cost_at_min'] = isset($_GET['select_cost_at_min']) ? $_GET['select_cost_at_min'] : '';
				$data['select_cost_at_max'] = isset($_GET['select_cost_at_max']) ? $_GET['select_cost_at_max'] : '';
				$data['select_self_flag'] = isset($_GET['select_self_flag']) ? $_GET['select_self_flag'] : false;
				$data['sort_column'] = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'created_at';
				$data['sort_method'] = isset($_GET['sort_method']) ? $_GET['sort_method'] : 'desc';
				$data['get_params'] = count($_GET) ? '?' . http_build_query($_GET) : '';
				
				//显示结果默认值
				$data['cost_list'] = array();
				$data['cost_count'] = 0;
				$data['start_number'] = 0;
				$data['end_number'] = 0;
				$data['page_number'] = 1;
				$data['page'] = $page;
				
				//获取支出记录信息
				$params_select = array(
					'cost_desc' => $data['select_cost_desc'] ? explode(' ', $data['select_cost_desc']) : array(),
					'cost_type' => $data['select_cost_type'],
					'price_min' => $data['select_price_min'],
					'price_max' => $data['select_price_max'],
					'cost_at_min' => $data['select_cost_at_min'],
					'cost_at_max' => $data['select_cost_at_max'],
					'sort_column' => $data['sort_column'],
					'sort_method' => $data['sort_method'],
					'page' => $page,
					'num_per_page' => $num_per_page,
					'active_only' => true,
				);
				if($data['select_self_flag']) {
					$params_select['created_by'] = $login_user_id;
				}
				
				$result_select = Model_Cost::SelectCostList($params_select);
				
				//整理显示内容
				if($result_select) {
					$cost_count = $result_select['cost_count'];
					$data['cost_count'] = $cost_count;
					$data['cost_list'] = $result_select['cost_list'];
					$data['start_number'] = $result_select['start_number'];
					$data['end_number'] = $result_select['end_number'];
					if($cost_count > $num_per_page) {
						$data['page_number'] = ceil($cost_count/$num_per_page);
					}
				}
				
				//支出记录削除处理
				if(isset($_SESSION['delete_cost_success'])) {
					$data['success_message'] = '支出记录削除成功';
					unset($_SESSION['delete_cost_success']);
				}
				if(isset($_SESSION['delete_cost_error'])) {
					switch($_SESSION['delete_cost_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备删除支出记录的权限';
							break;
						case 'error_cost_id':
							$data['error_message'] = '您要删除的支出记录不存在,请确认该记录是否已经被删除';
							break;
						case 'error_creator':
							$data['error_message'] = '您不具备删除其他用户所登陆支出记录的权限';
							break;
						case 'error_db':
							$data['error_message'] = '发生数据库错误,请重新尝试删除';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新删除';
							break;
					}
					unset($_SESSION['delete_cost_error']);
				}
				
				//支出记录批量削除处理
				if(isset($_SESSION['delete_cost_checked_success'])) {
					$data['success_message'] = '支出记录削除成功';
					unset($_SESSION['delete_cost_checked_success']);
				}
				if(isset($_SESSION['delete_cost_checked_error'])) {
					switch($_SESSION['delete_cost_checked_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备删除支出记录的权限';
							break;
						case 'empty_cost_id':
							$data['error_message'] = '请选择您要删除的支出记录';
							break;
						case 'error_cost_id':
							$data['error_message'] = '您要删除的支出记录不存在,请确认该记录是否已经被删除';
							break;
						case 'error_creator':
							$data['error_message'] = '您不具备删除其他用户所登陆支出记录的权限';
							break;
						case 'error_db':
							$data['error_message'] = '发生数据库错误,请重新尝试删除';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新删除';
							break;
					}
					unset($_SESSION['delete_cost_checked_error']);
				}
				
				//支出记录导出处理
				if(isset($_SESSION['export_cost_success'])) {
					$data['success_message'] = '支出记录导出成功';
					unset($_SESSION['export_cost_success']);
				}
				if(isset($_SESSION['export_cost_error'])) {
					switch($_SESSION['export_cost_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备导出支出记录的权限';
							break;
						case 'empty_cost_list':
							$data['error_message'] = '未能找到符合条件的支出记录,请调整筛选条件';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新删除';
							break;
					}
					unset($_SESSION['export_cost_error']);
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/financial/cost/cost_list', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}


}