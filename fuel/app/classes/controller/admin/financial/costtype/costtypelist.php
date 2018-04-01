<?php
/* 
 * 支出项目一览页
 */

class Controller_Admin_Financial_Costtype_Costtypelist extends Controller_Admin_App
{

	/**
	 * 支出项目一览
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
				$data['success_message'] = '';
				$data['error_message'] = '';
				
				//获取返回一览页时的一览页URL
				$data['cost_list_url'] = '/admin/cost_list/';
				if(isset($_SERVER['HTTP_REFERER'])) {
					if(strstr($_SERVER['HTTP_REFERER'], 'admin/cost_list')) {
						//通过一览页链接进入
						$data['cost_list_url'] = $_SERVER['HTTP_REFERER'];
					} elseif(strstr($_SERVER['HTTP_REFERER'], 'admin/add_cost_type') || strstr($_SERVER['HTTP_REFERER'], 'admin/modify_cost_type')) {
						if(isset($_SESSION['url_return_cost_list'])) {
							$data['cost_list_url'] = $_SESSION['url_return_cost_list'];
						}
					}
				}
				//暂时保留一览页URL
				$_SESSION['url_return_cost_list'] = $data['cost_list_url'];
				
				//获取支出项目信息
				$params_select = array(
					'active_only' => true,
					'cost_count_flag' => true,
				);
				
				$data['cost_type_list'] = Model_CostType::SelectCostTypeList($params_select);
				
				//支出项目添加结果处理
				if(isset($_SESSION['add_cost_type_success'])) {
					$data['success_message'] = '支出项目添加成功';
					unset($_SESSION['add_cost_type_success']);
				}
				if(isset($_SESSION['add_cost_type_error'])) {
					$data['error_message'] = '发生系统错误,请尝试重新添加';
					unset($_SESSION['add_cost_type_error']);
				}
				
				//支出项目信息修改结果处理
				if(isset($_SESSION['modify_cost_type_success'])) {
					$data['success_message'] = '支出项目修改成功';
					unset($_SESSION['modify_cost_type_success']);
				}
				if(isset($_SESSION['modify_cost_type_error'])) {
					$data['error_message'] = '发生系统错误,请尝试重新修改';
					unset($_SESSION['modify_cost_type_error']);
				}
				
				//支出项目削除结果处理
				if(isset($_SESSION['delete_cost_type_success'])) {
					$data['success_message'] = '支出项目削除成功';
					unset($_SESSION['delete_cost_type_success']);
				}
				if(isset($_SESSION['delete_cost_type_error'])) {
					switch($_SESSION['delete_cost_type_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备删除支出项目的权限';
							break;
						case 'error_cost_type_id':
							$data['error_message'] = '您要删除的支出项目不存在,请确认该支出项目是否已经被删除';
							break;
						case 'error_db':
							$data['error_message'] = '发生数据库错误,请重新尝试删除';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新删除';
							break;
					}
					unset($_SESSION['delete_cost_type_error']);
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/financial/cost_type/cost_type_list', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}