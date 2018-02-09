<?php
/* 
 * 旅游路线一览页
 */

class Controller_Admin_Service_Route_Routelist extends Controller_Admin_App
{

	/**
	 * 旅游路线一览
	 * @access  public
	 * @return  Response
	 */
	public function action_index($page = 1)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		try {
			if(!is_numeric($page)) {
				//页数不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'sub_group', 12)) {
				//当前登陆用户不具备查看旅游路线的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['success_message'] = '';
				$data['error_message'] = '';
				
				//获取自身用户ID
				$data['user_id_self'] = $_SESSION['login_user']['id'];
				//是否具备旅游路线编辑权限
				$data['edit_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 22);
				//是否具备其他用户所登陆的旅游路线编辑权限
				$data['edit_other_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 8);
				//是否具备旅游路线删除权限
				$data['delete_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 23);
				//是否具备其他用户所登陆的旅游路线删除权限
				$data['delete_other_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 9);
				//是否具备批量导入旅游路线信息权限
				$data['import_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 24);
				//是否具备导出旅游路线信息权限
				$data['export_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 25);
				
				//每页显示旅游路线数
				$num_per_page = 20;
				//本页前后最大可链接页数
				$data['page_link_max'] = 3;
				
				//检索条件
				$data['select_name'] = isset($_GET['select_name']) ? preg_replace('/( |　)/', ' ', $_GET['select_name']) : '';
				$data['select_status'] = isset($_GET['select_status']) && is_array($_GET['select_status']) ? $_GET['select_status'] : array();
				$data['select_price_min'] = isset($_GET['select_price_min']) ? $_GET['select_price_min'] : '';
				$data['select_price_max'] = isset($_GET['select_price_max']) ? $_GET['select_price_max'] : '';
				$data['select_base_cost_min'] = isset($_GET['select_base_cost_min']) ? $_GET['select_base_cost_min'] : '';
				$data['select_base_cost_max'] = isset($_GET['select_base_cost_max']) ? $_GET['select_base_cost_max'] : '';
				$data['select_traffic_cost_min'] = isset($_GET['select_traffic_cost_min']) ? $_GET['select_traffic_cost_min'] : '';
				$data['select_traffic_cost_max'] = isset($_GET['select_traffic_cost_max']) ? $_GET['select_traffic_cost_max'] : '';
				$data['select_parking_cost_min'] = isset($_GET['select_parking_cost_min']) ? $_GET['select_parking_cost_min'] : '';
				$data['select_parking_cost_max'] = isset($_GET['select_parking_cost_max']) ? $_GET['select_parking_cost_max'] : '';
				$data['select_total_cost_min'] = isset($_GET['select_total_cost_min']) ? $_GET['select_total_cost_min'] : '';
				$data['select_total_cost_max'] = isset($_GET['select_total_cost_max']) ? $_GET['select_total_cost_max'] : '';
				$data['select_self_flag'] = isset($_GET['select_self_flag']) ? $_GET['select_self_flag'] : false;
				$data['sort_column'] = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'created_at';
				$data['sort_method'] = isset($_GET['sort_method']) ? $_GET['sort_method'] : 'desc';
				$data['get_params'] = count($_GET) ? '?' . http_build_query($_GET) : '';
				
				//显示结果默认值
				$data['route_list'] = array();
				$data['route_count'] = 0;
				$data['start_number'] = 0;
				$data['end_number'] = 0;
				$data['page_number'] = 1;
				$data['page'] = $page;
				
				//获取旅游路线信息
				$params_select = array(
					'route_name' => $data['select_name'] ? explode(' ', $data['select_name']) : array(),
					'route_status' => $data['select_status'],
					'price_min' => $data['select_price_min'],
					'price_max' => $data['select_price_max'],
					'base_cost_min' => $data['select_base_cost_min'],
					'base_cost_max' => $data['select_base_cost_max'],
					'traffic_cost_min' => $data['select_traffic_cost_min'],
					'traffic_cost_max' => $data['select_traffic_cost_max'],
					'parking_cost_min' => $data['select_parking_cost_min'],
					'parking_cost_max' => $data['select_parking_cost_max'],
					'total_cost_min' => $data['select_total_cost_min'],
					'total_cost_max' => $data['select_total_cost_max'],
					'sort_column' => $data['sort_column'],
					'sort_method' => $data['sort_method'],
					'page' => $page,
					'num_per_page' => $num_per_page,
					'active_only' => true,
				);
				if($data['select_self_flag']) {
					$params_select['created_by'] = $_SESSION['login_user']['id'];
				}
				
				$result_select = Model_Route::SelectRouteList($params_select);
				
				//整理显示内容
				if($result_select) {
					$route_count = $result_select['route_count'];
					$data['route_count'] = $route_count;
					$data['route_list'] = $result_select['route_list'];
					$data['start_number'] = $result_select['start_number'];
					$data['end_number'] = $result_select['end_number'];
					if($route_count > $num_per_page) {
						$data['page_number'] = ceil($route_count/$num_per_page);
					}
				}
				
				//旅游路线削除处理
				if(isset($_SESSION['delete_route_success'])) {
					$data['success_message'] = '旅游路线削除成功';
					unset($_SESSION['delete_route_success']);
				}
				if(isset($_SESSION['delete_route_error'])) {
					switch($_SESSION['delete_route_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备删除旅游路线的权限';
							break;
						case 'error_route_id':
							$data['error_message'] = '您要删除的旅游路线不存在,请确认该旅游路线是否已经被删除';
							break;
						case 'error_creator':
							$data['error_message'] = '您不具备删除其他用户所登陆旅游路线的权限';
							break;
						case 'error_db':
							$data['error_message'] = '发生数据库错误,请重新尝试删除';
							break;
						case 'error_image':
							$data['error_message'] = '旅游路线数据删除成功,但未能成功删除关联的图片文件,请联系系统开发人员进行手动删除';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新删除';
							break;
					}
					unset($_SESSION['delete_route_error']);
				}
				
				//旅游路线削除处理
				if(isset($_SESSION['delete_route_checked_success'])) {
					$data['success_message'] = '旅游路线削除成功';
					unset($_SESSION['delete_route_checked_success']);
				}
				if(isset($_SESSION['delete_route_checked_error'])) {
					switch($_SESSION['delete_route_checked_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备删除旅游路线的权限';
							break;
						case 'empty_route_id':
							$data['error_message'] = '请选择您要删除的旅游路线';
							break;
						case 'error_route_id':
							$data['error_message'] = '您要删除的旅游路线不存在,请确认该旅游路线是否已经被删除';
							break;
						case 'error_creator':
							$data['error_message'] = '您不具备删除其他用户所登陆旅游路线的权限';
							break;
						case 'error_db':
							$data['error_message'] = '发生数据库错误,请重新尝试删除';
							break;
						case 'error_image':
							$data['error_message'] = '旅游路线数据删除成功,但未能成功删除关联的图片文件,请联系系统开发人员进行手动删除';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新删除';
							break;
					}
					unset($_SESSION['delete_route_checked_error']);
				}
				
				//旅游路线批量导入处理
				if(isset($_SESSION['import_route_success'])) {
					$data['success_message'] = '旅游路线批量导入成功';
					unset($_SESSION['import_route_success']);
				}
				if(isset($_SESSION['import_route_error'])) {
					switch($_SESSION['import_route_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备批量导入旅游路线的权限';
							break;
						case 'noexist_file':
							$data['error_message'] = '请上传写入旅游路线信息的Excel文件';
							break;
						case 'noexcel_file':
							$data['error_message'] = '您上传的文件格式不符合要求,请上传Excel文件';
							break;
						case 'empty_route_name':
							$data['error_message'] = '您上传的文件中未写入任何旅游路线名';
							break;
						case 'noexist_sheet':
							$data['error_message'] = '您上传的文件不包含批量导入所必须的表';
							break;
						case 'error_import':
							$data['error_message'] = '部分旅游路线未能成功导入,请<a href="/assets/xls/tmp/' . $_SESSION['login_user']['id'] . '/route/import_route_error.xls" download>点击此处</a>下载异常报告';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新批量导入';
							break;
					}
					unset($_SESSION['import_route_error']);
				}
				
				//旅游路线导出处理
				if(isset($_SESSION['export_route_success'])) {
					$data['success_message'] = '旅游路线导出成功';
					unset($_SESSION['export_route_success']);
				}
				if(isset($_SESSION['export_route_error'])) {
					switch($_SESSION['export_route_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备导出旅游路线的权限';
							break;
						case 'empty_route_list':
							$data['error_message'] = '未能找到符合条件的旅游路线,请调整筛选条件';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新删除';
							break;
					}
					unset($_SESSION['export_route_error']);
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/route/route_list', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}