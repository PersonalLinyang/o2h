<?php
/* 
 * 餐饮店一览页
 */

class Controller_Admin_Service_Restaurant_Restaurantlist extends Controller_Admin_App
{

	/**
	 * 餐饮店一览
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
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'sub_group', 10)) {
				//当前登陆用户不具备查看餐饮店的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['success_message'] = '';
				$data['error_message'] = '';
				
				//获取自身用户ID
				$data['user_id_self'] = $_SESSION['login_user']['id'];
				//获取地区列表
				$data['area_list'] = Model_Area::GetAreaList(array('active_only' => true));
				//获取餐饮店类型列表
				$data['restaurant_type_list'] = Model_RestaurantType::SelectRestaurantTypeList(array('active_only' => true));
				//是否具备餐饮店编辑权限
				$data['edit_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 11);
				//是否具备其他用户所登陆的餐饮店编辑权限
				$data['edit_other_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 4);
				//是否具备餐饮店删除权限
				$data['delete_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 12);
				//是否具备其他用户所登陆的餐饮店删除权限
				$data['delete_other_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 5);
				//是否具备批量导入餐饮店信息权限
				$data['import_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 13);
				//是否具备导出餐饮店信息权限
				$data['export_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 14);
				//是否具备餐饮店类别管理权限
				$data['restaurant_type_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 15);
				
				//每页现实景点数
				$num_per_page = 20;
				//本页前后最大可链接页数
				$data['page_link_max'] = 3;
				
				//检索条件
				$data['select_name'] = isset($_GET['select_name']) ? preg_replace('/( |　)/', ' ', $_GET['select_name']) : '';
				$data['select_status'] = isset($_GET['select_status']) && is_array($_GET['select_status']) ? $_GET['select_status'] : array();
				$data['select_area'] = isset($_GET['select_area']) && is_array($_GET['select_area']) ? $_GET['select_area'] : array();
				$data['select_restaurant_type'] = isset($_GET['select_restaurant_type']) && is_array($_GET['select_restaurant_type']) ? $_GET['select_restaurant_type'] : array();
				$data['select_price_min'] = isset($_GET['select_price_min']) ? $_GET['select_price_min'] : '';
				$data['select_price_max'] = isset($_GET['select_price_max']) ? $_GET['select_price_max'] : '';
				$data['sort_column'] = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'created_at';
				$data['sort_method'] = isset($_GET['sort_method']) ? $_GET['sort_method'] : 'desc';
				$data['get_params'] = count($_GET) ? '?' . http_build_query($_GET) : '';
				
				//显示结果默认值
				$data['restaurant_list'] = array();
				$data['restaurant_count'] = 0;
				$data['start_number'] = 0;
				$data['end_number'] = 0;
				$data['page_number'] = 1;
				$data['page'] = $page;
				
				//获取餐饮店信息
				$params_select = array(
					'restaurant_name' => $data['select_name'] ? explode(' ', $data['select_name']) : array(),
					'restaurant_status' => $data['select_status'],
					'restaurant_area' => $data['select_area'],
					'restaurant_type' => $data['select_restaurant_type'],
					'price_min' => $data['select_price_min'],
					'price_max' => $data['select_price_max'],
					'sort_column' => $data['sort_column'],
					'sort_method' => $data['sort_method'],
					'page' => $page,
					'num_per_page' => $num_per_page,
					'active_only' => true,
				);
				
				$result_select = Model_Restaurant::SelectRestaurantList($params_select);
				
				//整理显示内容
				if($result_select) {
					$restaurant_count = $result_select['restaurant_count'];
					$data['restaurant_count'] = $restaurant_count;
					$data['restaurant_list'] = $result_select['restaurant_list'];
					$data['start_number'] = $result_select['start_number'];
					$data['end_number'] = $result_select['end_number'];
					if($restaurant_count > $num_per_page) {
						$data['page_number'] = ceil($restaurant_count/$num_per_page);
					}
				}
				
				//餐饮店削除处理
				if(isset($_SESSION['delete_restaurant_success'])) {
					$data['success_message'] = '餐饮店削除成功';
					unset($_SESSION['delete_restaurant_success']);
				}
				if(isset($_SESSION['delete_restaurant_error'])) {
					switch($_SESSION['delete_restaurant_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备删除餐饮店的权限';
							break;
						case 'error_restaurant_id':
							$data['error_message'] = '您要删除的餐饮店不存在,请确认该餐饮店是否已经被删除';
							break;
						case 'error_creator':
							$data['error_message'] = '您不具备删除其他用户所登陆餐饮店的权限';
							break;
						case 'error_db':
							$data['error_message'] = '发生数据库错误,请重新尝试删除';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新删除';
							break;
					}
					unset($_SESSION['delete_restaurant_error']);
				}
				
				//餐饮店削除处理
				if(isset($_SESSION['delete_restaurant_checked_success'])) {
					$data['success_message'] = '餐饮店削除成功';
					unset($_SESSION['delete_restaurant_checked_success']);
				}
				if(isset($_SESSION['delete_restaurant_checked_error'])) {
					switch($_SESSION['delete_restaurant_checked_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备删除餐饮店的权限';
							break;
						case 'empty_restaurant_id':
							$data['error_message'] = '请选择您要删除的餐饮店';
							break;
						case 'error_restaurant_id':
							$data['error_message'] = '您要删除的餐饮店不存在,请确认该餐饮店是否已经被删除';
							break;
						case 'error_creator':
							$data['error_message'] = '您不具备删除其他用户所登陆餐饮店的权限';
							break;
						case 'error_db':
							$data['error_message'] = '发生数据库错误,请重新尝试删除';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新删除';
							break;
					}
					unset($_SESSION['delete_restaurant_checked_error']);
				}
				
				//餐饮店批量导入处理
				if(isset($_SESSION['import_restaurant_success'])) {
					$data['success_message'] = '餐饮店批量导入成功';
					unset($_SESSION['import_restaurant_success']);
				}
				if(isset($_SESSION['import_restaurant_error'])) {
					switch($_SESSION['import_restaurant_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备批量导入餐饮店的权限';
							break;
						case 'noexist_file':
							$data['error_message'] = '请上传写入餐饮店信息的Excel文件';
							break;
						case 'noexcel_file':
							$data['error_message'] = '您上传的文件格式不符合要求,请上传Excel文件';
							break;
						case 'empty_restaurant_name':
							$data['error_message'] = '您上传的文件中未写入任何餐饮店名';
							break;
						case 'noexist_sheet':
							$data['error_message'] = '您上传的文件不包含批量导入所必须的表';
							break;
						case 'error_import':
							$data['error_message'] = '部分餐饮店未能成功导入,请<a href="/assets/xls/tmp/' . $_SESSION['login_user']['id'] . '/restaurant/import_restaurant_error.xls" download>点击此处</a>下载异常报告';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新批量导入';
							break;
					}
					unset($_SESSION['import_restaurant_error']);
				}
				
				//餐饮店导出处理
				if(isset($_SESSION['export_restaurant_success'])) {
					$data['success_message'] = '餐饮店导出成功';
					unset($_SESSION['export_restaurant_success']);
				}
				if(isset($_SESSION['export_restaurant_error'])) {
					switch($_SESSION['export_restaurant_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备导出餐饮店的权限';
							break;
						case 'empty_restaurant_list':
							$data['error_message'] = '未能找到符合条件的餐饮店,请调整筛选条件';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新删除';
							break;
					}
					unset($_SESSION['export_restaurant_error']);
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/restaurant/restaurant_list', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}