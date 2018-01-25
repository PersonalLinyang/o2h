<?php
/* 
 * 景点一览页
 */

class Controller_Admin_Service_Spot_Spotlist extends Controller_Admin_App
{

	/**
	 * 景点一览
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
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'sub_group', 9)) {
				//当前登陆用户不具备查看景点的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['success_message'] = '';
				$data['error_message'] = '';
				
				//每页现实景点数
				$num_per_page = 20;
				
				//获取自身用户ID
				$data['user_id_self'] = $_SESSION['login_user']['id'];
				//获取地区列表
				$data['area_list'] = Model_Area::GetAreaList(array('active_only' => true));
				//获取景点类型列表
				$data['spot_type_list'] = Model_SpotType::GetSpotTypeList(array('active_only' => true));
				//是否具备景点编辑权限
				$data['edit_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 6);
				//是否具备其他用户所登陆的景点编辑权限
				$data['edit_other_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 2);
				//是否具备景点删除权限
				$data['delete_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 7);
				//是否具备其他用户所登陆的景点删除权限
				$data['delete_other_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 3);
				//是否具备景点类别管理权限
				$data['spot_type_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 10);
				//是否具备批量导入景点信息权限
				$data['import_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 8);
				//是否具备导出景点信息权限
				$data['export_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 9);
				
				//本页前后最大可链接页数
				$data['page_link_max'] = 3;
				
				//检索条件
				$data['select_name'] = isset($_GET['select_name']) ? preg_replace('/( |　)/', ' ', $_GET['select_name']) : '';
				$data['select_status'] = isset($_GET['select_status']) ? $_GET['select_status'] : array();
				$data['select_area'] = isset($_GET['select_area']) ? $_GET['select_area'] : array();
				$data['select_spot_type'] = isset($_GET['select_spot_type']) ? $_GET['select_spot_type'] : array();
				$data['select_free_flag'] = isset($_GET['select_free_flag']) ? $_GET['select_free_flag'] : array();
				$data['select_price_min'] = isset($_GET['select_price_min']) ? $_GET['select_price_min'] : '';
				$data['select_price_max'] = isset($_GET['select_price_max']) ? $_GET['select_price_max'] : '';
				$data['sort_column'] = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'created_at';
				$data['sort_method'] = isset($_GET['sort_method']) ? $_GET['sort_method'] : 'desc';
				$data['get_params'] = count($_GET) ? '?' . http_build_query($_GET) : '';
				
				//显示结果默认值
				$data['spot_list'] = array();
				$data['spot_count'] = 0;
				$data['start_number'] = 0;
				$data['end_number'] = 0;
				$data['page_number'] = 1;
				$data['page'] = $page;
				
				//获取景点信息
				$params_select = array(
					'spot_name' => $data['select_name'] ? explode(' ', $data['select_name']) : array(),
					'spot_status' => $data['select_status'],
					'spot_area' => $data['select_area'],
					'spot_type' => $data['select_spot_type'],
					'free_flag' => $data['select_free_flag'],
					'price_min' => $data['select_price_min'],
					'price_max' => $data['select_price_max'],
					'sort_column' => $data['sort_column'],
					'sort_method' => $data['sort_method'],
					'page' => $page,
					'num_per_page' => $num_per_page,
					'active_only' => 1,
				);
				
				$result_select = Model_Spot::SelectSpotList($params_select);
				
				//整理显示内容
				if($result_select) {
					$spot_count = $result_select['spot_count'];
					$data['spot_count'] = $spot_count;
					$data['spot_list'] = $result_select['spot_list'];
					$data['start_number'] = $result_select['start_number'];
					$data['end_number'] = $result_select['end_number'];
					if($spot_count > $num_per_page) {
						$data['page_number'] = ceil($spot_count/$num_per_page);
					}
				}
				
				//景点批量导入处理
				if(isset($_SESSION['import_spot_success'])) {
					$data['success_message'] = '景点批量导入成功';
					unset($_SESSION['import_spot_success']);
				}
				if(isset($_SESSION['import_spot_error'])) {
					switch($_SESSION['import_spot_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备批量导入景点的权限';
							break;
						case 'noexist_file':
							$data['error_message'] = '请上传写入景点信息的excel文件';
							break;
						case 'noexcel_file':
							$data['error_message'] = '您上传的文件格式不符合要求,请上传Excel文件';
							break;
						case 'empty_spot_name':
							$data['error_message'] = '您上传的文件中未写入任何景点名';
							break;
						case 'noexist_sheet':
							$data['error_message'] = '您上传的文件不包含批量导入所必须的工作表';
							break;
						case 'error_upload':
							$data['error_message'] = '部分景点未能成功导入,请<a href="/assets/xls/tmp/' . $_SESSION['login_user']['id'] . '/spot/import_spot_error.xls" download>点击此处</a>下载异常报告';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新批量导入';
							break;
					}
					unset($_SESSION['import_spot_error']);
				}
				
				//景点削除处理
				if(isset($_SESSION['delete_spot_success'])) {
					$data['success_message'] = '景点削除成功';
					unset($_SESSION['delete_spot_success']);
				}
				if(isset($_SESSION['delete_spot_error'])) {
					switch($_SESSION['delete_spot_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备删除景点的权限';
							break;
						case 'error_spot_id':
							$data['error_message'] = '您要删除的景点不存在,请确认该景点是否已经被删除';
							break;
						case 'error_creator':
							$data['error_message'] = '您不具备删除其他用户所登陆景点的权限';
							break;
						case 'error_db':
							$data['error_message'] = '发生数据库错误,请重新尝试删除';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新删除';
							break;
					}
					unset($_SESSION['delete_spot_error']);
				}
				
				//景点削除处理
				if(isset($_SESSION['delete_spot_checked_success'])) {
					$data['success_message'] = '景点削除成功';
					unset($_SESSION['delete_spot_checked_success']);
				}
				if(isset($_SESSION['delete_spot_checked_error'])) {
					switch($_SESSION['delete_spot_checked_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备删除景点的权限';
							break;
						case 'empty_spot_id':
							$data['error_message'] = '请选择您要删除的景点';
							break;
						case 'error_spot_id':
							$data['error_message'] = '您要删除的景点不存在,请确认该景点是否已经被删除';
							break;
						case 'error_creator':
							$data['error_message'] = '您不具备删除其他用户所登陆景点的权限';
							break;
						case 'error_db':
							$data['error_message'] = '发生数据库错误,请重新尝试删除';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新删除';
							break;
					}
					unset($_SESSION['delete_spot_checked_error']);
				}
				
				//景点导出处理
				if(isset($_SESSION['export_spot_success'])) {
					$data['success_message'] = '景点导出成功';
					unset($_SESSION['export_spot_success']);
				}
				if(isset($_SESSION['export_spot_error'])) {
					switch($_SESSION['export_spot_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备导出景点的权限';
							break;
						case 'empty_spot_list':
							$data['error_message'] = '未能找到符合条件的景点,请调整筛选条件';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新删除';
							break;
					}
					unset($_SESSION['export_spot_error']);
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/spot/spot_list', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}