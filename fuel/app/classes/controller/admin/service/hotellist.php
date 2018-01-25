<?php
/* 
 * 酒店一览页
 */

class Controller_Admin_Service_Hotellist extends Controller_Admin_App
{

	/**
	 * 酒店一览
	 * @access  public
	 * @return  Response
	 */
	public function action_index($page = null)
	{
		$data = array();
		
		if(!is_numeric($page)) {
			$page = 1;
		}
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['success_message'] = '';
			$data['error_message'] = '';
			$data['area_list'] = Model_Area::GetAreaList(array('active_only' => true));
			$data['hotel_type_list'] = Model_HotelType::GetHotelTypeListActive();
			$data['hotel_list'] = array();
			$data['hotel_count'] = 0;
			$data['start_number'] = 0;
			$data['end_number'] = 0;
			$data['page'] = $page;
			$data['page_number'] = 1;
			//本页前后最大可链接页数
			$data['page_link_max'] = 3;
			$data['select_name'] = '';
			$data['select_status'] = isset($_GET['select_status']) ? $_GET['select_status'] : array();
			$data['select_area'] = isset($_GET['select_area']) ? $_GET['select_area'] : array();
			$data['select_hotel_type'] = isset($_GET['select_hotel_type']) ? $_GET['select_hotel_type'] : array();
			$data['select_price_min'] = isset($_GET['select_price_min']) ? $_GET['select_price_min'] : '';
			$data['select_price_max'] = isset($_GET['select_price_max']) ? $_GET['select_price_max'] : '';
			$data['sort_column'] = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'created_at';
			$data['sort_method'] = isset($_GET['sort_method']) ? $_GET['sort_method'] : 'desc';
			$data['get_params'] = isset($_GET) ? '?' . http_build_query($_GET) : '';
			$hotel_count = 0;
			$num_per_page = 20;

			$hotel_name_list = array();
			if(isset($_GET['select_name'])) {
				$hotel_name_list_tmp = explode(' ', $_GET['select_name']);
				foreach($hotel_name_list_tmp as $hotel_name_tmp) {
					$hotel_name_list = array_merge($hotel_name_list, explode('　', $hotel_name_tmp));
				}
			}
			$data['select_name'] = implode(' ', $hotel_name_list);
			
			$params = array(
				'hotel_name' => $hotel_name_list,
				'hotel_status' => isset($_GET['select_status']) ? $_GET['select_status'] : array(),
				'hotel_area' => isset($_GET['select_area']) ? $_GET['select_area'] : array(),
				'hotel_type' => isset($_GET['select_hotel_type']) ? $_GET['select_hotel_type'] : array(),
				'price_min' => isset($_GET['select_price_min']) ? $_GET['select_price_min'] : '',
				'price_max' => isset($_GET['select_price_max']) ? $_GET['select_price_max'] : '',
				'sort_column' => isset($_GET['sort_column']) ? $_GET['sort_column'] : 'created_at',
				'sort_method' => isset($_GET['sort_method']) ? $_GET['sort_method'] : 'desc',
				'page' => $page,
				'num_per_page' => $num_per_page,
			);
			$result_select = Model_Hotel::SelectHotelList($params);
			if($result_select) {
				$hotel_count = $result_select['hotel_count'];
				$data['hotel_count'] = $hotel_count;
				$data['hotel_list'] = $result_select['hotel_list'];
				$data['start_number'] = $result_select['start_number'];
				$data['end_number'] = $result_select['end_number'];
			}
			
			if($hotel_count > $num_per_page) {
				$data['page_number'] = ceil($hotel_count/$num_per_page);
			}
			
			if(isset($_SESSION['delete_hotel_success'])) {
				$data['success_message'] = '酒店削除成功';
				unset($_SESSION['delete_hotel_success']);
			}
			if(isset($_SESSION['delete_hotel_error'])) {
				$data['error_message'] = '酒店削除失敗';
				unset($_SESSION['delete_hotel_error']);
			}
			
			if(isset($_SESSION['delete_checked_hotel_success'])) {
				$data['success_message'] = '选中酒店削除成功';
				unset($_SESSION['delete_checked_hotel_success']);
			}
			if(isset($_SESSION['delete_checked_hotel_error'])) {
				$data['error_message'] = '选中酒店削除失敗';
				unset($_SESSION['delete_checked_hotel_error']);
			}

			//调用View
			return Response::forge(View::forge($this->template . '/admin/service/hotel_list', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}
	
	/**
	 * 削除酒店
	 * @access  public
	 * @return  Response
	 */
	public function action_deletehotel($param = null)
	{
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['delete_id'], $_POST['page'])) {
			if($_POST['page'] == 'hotel_list') {
				//删除信息检查
				$result_check = Model_Hotel::CheckDeleteHotelById($_POST['delete_id']);
				if($result_check['result']) {
					//数据删除
					$result_delete = Model_Hotel::DeleteHotelById($_POST['delete_id']);
					
					if($result_delete) {
						$_SESSION['delete_hotel_success'] = true;
						header('Location: ' . $_SERVER['HTTP_REFERER']);
						exit;
					}
				}
			}
//		}
		$_SESSION['delete_hotel_error'] = true;
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		exit;
	}

	/**
	 * 削除所有选中酒店
	 * @access  public
	 * @return  Response
	 */
	public function action_deletecheckedhotel($param = null)
	{
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['delete_id'], $_POST['page'])) {
			if($_POST['page'] == 'hotel_list') {
				//删除信息检查
				$result_check = Model_Hotel::CheckDeleteHotelByIdList($_POST['delete_id_checked']);
				if($result_check['result']) {
					//数据删除
					$result_delete = Model_Hotel::DeleteHotelByIdList($_POST['delete_id_checked']);
					
					if($result_delete) {
						$_SESSION['delete_checked_hotel_success'] = true;
						header('Location: ' . $_SERVER['HTTP_REFERER']);
						exit;
					}
				}
			}
//		}
		$_SESSION['delete_checked_hotel_error'] = true;
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		exit;
	}


}