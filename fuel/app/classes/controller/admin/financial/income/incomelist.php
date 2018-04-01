<?php
/* 
 * 收入记录一览页
 */

class Controller_Admin_Financial_Income_Incomelist extends Controller_Admin_App
{

	/**
	 * 收入记录一览
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
				//当前登陆用户不具备查看收入记录的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['success_message'] = '';
				$data['error_message'] = '';
				
				//获取自身用户ID
				$data['user_id_self'] = $login_user_id;
				//获取收入类型列表
				$data['income_type_list'] = Model_Incometype::SelectIncomeTypeList(array('active_only' => true));
				
				//是否具备收入记录编辑权限
				$data['edit_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'function', 31);
				//是否具备其他用户所登陆的收入记录编辑权限
				$data['edit_other_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'authority', 13);
				//是否具备收入记录删除权限
				$data['delete_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'function', 32);
				//是否具备其他用户所登陆的收入记录删除权限
				$data['delete_other_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'authority', 14);
				//是否具备导出收入记录信息权限
				$data['export_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'function', 34);
				//是否具备收入项目管理权限
				$data['income_type_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'function', 35);
				
				//每页显示收入记录数
				$num_per_page = 20;
				//本页前后最大可链接页数
				$data['page_link_max'] = 3;
				
				//检索条件
				$data['select_income_desc'] = isset($_GET['select_income_desc']) ? preg_replace('/( |　)/', ' ', $_GET['select_income_desc']) : '';
				$data['select_income_type'] = isset($_GET['select_income_type']) && is_array($_GET['select_income_type']) ? $_GET['select_income_type'] : array();
				$data['select_price_min'] = isset($_GET['select_price_min']) ? $_GET['select_price_min'] : '';
				$data['select_price_max'] = isset($_GET['select_price_max']) ? $_GET['select_price_max'] : '';
				$data['select_income_at_min'] = isset($_GET['select_income_at_min']) ? $_GET['select_income_at_min'] : '';
				$data['select_income_at_max'] = isset($_GET['select_income_at_max']) ? $_GET['select_income_at_max'] : '';
				$data['select_self_flag'] = isset($_GET['select_self_flag']) ? $_GET['select_self_flag'] : false;
				$data['sort_column'] = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'created_at';
				$data['sort_method'] = isset($_GET['sort_method']) ? $_GET['sort_method'] : 'desc';
				$data['get_params'] = count($_GET) ? '?' . http_build_query($_GET) : '';
				
				//显示结果默认值
				$data['income_list'] = array();
				$data['income_count'] = 0;
				$data['start_number'] = 0;
				$data['end_number'] = 0;
				$data['page_number'] = 1;
				$data['page'] = $page;
				
				//获取收入记录信息
				$params_select = array(
					'income_desc' => $data['select_income_desc'] ? explode(' ', $data['select_income_desc']) : array(),
					'income_type' => $data['select_income_type'],
					'price_min' => $data['select_price_min'],
					'price_max' => $data['select_price_max'],
					'income_at_min' => $data['select_income_at_min'],
					'income_at_max' => $data['select_income_at_max'],
					'sort_column' => $data['sort_column'],
					'sort_method' => $data['sort_method'],
					'page' => $page,
					'num_per_page' => $num_per_page,
					'active_only' => true,
				);
				if($data['select_self_flag']) {
					$params_select['created_by'] = $login_user_id;
				}
				
				$result_select = Model_Income::SelectIncomeList($params_select);
				
				//整理显示内容
				if($result_select) {
					$income_count = $result_select['income_count'];
					$data['income_count'] = $income_count;
					$data['income_list'] = $result_select['income_list'];
					$data['start_number'] = $result_select['start_number'];
					$data['end_number'] = $result_select['end_number'];
					if($income_count > $num_per_page) {
						$data['page_number'] = ceil($income_count/$num_per_page);
					}
				}
				
				//收入记录削除处理
				if(isset($_SESSION['delete_income_success'])) {
					$data['success_message'] = '收入记录削除成功';
					unset($_SESSION['delete_income_success']);
				}
				if(isset($_SESSION['delete_income_error'])) {
					switch($_SESSION['delete_income_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备删除收入记录的权限';
							break;
						case 'error_income_id':
							$data['error_message'] = '您要删除的收入记录不存在,请确认该记录是否已经被删除';
							break;
						case 'error_creator':
							$data['error_message'] = '您不具备删除其他用户所登陆收入记录的权限';
							break;
						case 'error_db':
							$data['error_message'] = '发生数据库错误,请重新尝试删除';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新删除';
							break;
					}
					unset($_SESSION['delete_income_error']);
				}
				
				//收入记录批量削除处理
				if(isset($_SESSION['delete_income_checked_success'])) {
					$data['success_message'] = '收入记录削除成功';
					unset($_SESSION['delete_income_checked_success']);
				}
				if(isset($_SESSION['delete_income_checked_error'])) {
					switch($_SESSION['delete_income_checked_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备删除收入记录的权限';
							break;
						case 'empty_income_id':
							$data['error_message'] = '请选择您要删除的收入记录';
							break;
						case 'error_income_id':
							$data['error_message'] = '您要删除的收入记录不存在,请确认该记录是否已经被删除';
							break;
						case 'error_creator':
							$data['error_message'] = '您不具备删除其他用户所登陆收入记录的权限';
							break;
						case 'error_db':
							$data['error_message'] = '发生数据库错误,请重新尝试删除';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新删除';
							break;
					}
					unset($_SESSION['delete_income_checked_error']);
				}
				
				//收入记录导出处理
				if(isset($_SESSION['export_income_success'])) {
					$data['success_message'] = '收入记录导出成功';
					unset($_SESSION['export_income_success']);
				}
				if(isset($_SESSION['export_income_error'])) {
					switch($_SESSION['export_income_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备导出收入记录的权限';
							break;
						case 'empty_income_list':
							$data['error_message'] = '未能找到符合条件的收入记录,请调整筛选条件';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新删除';
							break;
					}
					unset($_SESSION['export_income_error']);
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/financial/income/income_list', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}


}