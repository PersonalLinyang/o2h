<?php
/* 
 * 酒店一览页
 */

class Controller_Admin_Service_Hotel_Hotellist extends Controller_Admin_App
{

	/**
	 * 酒店一览
	 * @access  public
	 * @return  Response
	 */
	public function action_index($page = 1)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		try {
			if(!is_numeric($page)) {
				//页数不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'sub_group', 11)) {
				//当前登陆用户不具备查看酒店的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['success_message'] = '';
				$data['error_message'] = '';
				
				//获取自身用户ID
				$data['user_id_self'] = $_SESSION['login_user']['id'];
				//获取地区列表
				$data['area_list'] = Model_Area::GetAreaList(array('active_only' => true));
				//获取酒店类型列表
				$data['hotel_type_list'] = Model_HotelType::SelectHotelTypeList(array('active_only' => true));
				//是否具备酒店编辑权限
				$data['edit_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 16);
				//是否具备其他用户所登陆的酒店编辑权限
				$data['edit_other_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 6);
				//是否具备酒店删除权限
				$data['delete_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 17);
				//是否具备其他用户所登陆的酒店删除权限
				$data['delete_other_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 7);
				//是否具备批量导入酒店信息权限
				$data['import_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 18);
				//是否具备导出酒店信息权限
				$data['export_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 19);
				//是否具备酒店类别管理权限
				$data['spot_type_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 20);
				
				//每页现实景点数
				$num_per_page = 20;
				//本页前后最大可链接页数
				$data['page_link_max'] = 3;
				
				//检索条件
				$data['select_name'] = isset($_GET['select_name']) ? preg_replace('/( |　)/', ' ', $_GET['select_name']) : '';
				$data['select_status'] = isset($_GET['select_status']) ? $_GET['select_status'] : array();
				$data['select_area'] = isset($_GET['select_area']) ? $_GET['select_area'] : array();
				$data['select_hotel_type'] = isset($_GET['select_hotel_type']) ? $_GET['select_hotel_type'] : array();
				$data['select_price_min'] = isset($_GET['select_price_min']) ? $_GET['select_price_min'] : '';
				$data['select_price_max'] = isset($_GET['select_price_max']) ? $_GET['select_price_max'] : '';
				$data['sort_column'] = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'created_at';
				$data['sort_method'] = isset($_GET['sort_method']) ? $_GET['sort_method'] : 'desc';
				$data['get_params'] = count($_GET) ? '?' . http_build_query($_GET) : '';
				
				//显示结果默认值
				$data['hotel_list'] = array();
				$data['hotel_count'] = 0;
				$data['start_number'] = 0;
				$data['end_number'] = 0;
				$data['page_number'] = 1;
				$data['page'] = $page;
				
				//获取景点信息
				$params_select = array(
					'hotel_name' => $data['select_name'] ? explode(' ', $data['select_name']) : array(),
					'hotel_status' => $data['select_status'],
					'hotel_area' => $data['select_area'],
					'hotel_type' => $data['select_hotel_type'],
					'price_min' => $data['select_price_min'],
					'price_max' => $data['select_price_max'],
					'sort_column' => $data['sort_column'],
					'sort_method' => $data['sort_method'],
					'page' => $page,
					'num_per_page' => $num_per_page,
					'active_only' => true,
				);
				
				$result_select = Model_Hotel::SelectHotelList($params_select);
				
				//整理显示内容
				if($result_select) {
					$hotel_count = $result_select['hotel_count'];
					$data['hotel_count'] = $hotel_count;
					$data['hotel_list'] = $result_select['hotel_list'];
					$data['start_number'] = $result_select['start_number'];
					$data['end_number'] = $result_select['end_number'];
					if($hotel_count > $num_per_page) {
						$data['page_number'] = ceil($hotel_count/$num_per_page);
					}
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/hotel/hotel_list', $data, false));
			}
//		} catch (Exception $e) {
//			//发生系统异常
//			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
//		}
	}


}