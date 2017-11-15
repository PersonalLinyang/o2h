<?php
/* 
 * 酒店详细信息页
 */

class Controller_Admin_Service_Hoteldetail extends Controller_Admin_App
{

	/**
	 * 酒店详细信息页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($hotel_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['success_message'] = '';
			$data['error_message'] = '';
			
			$hotel_info = Model_Hotel::SelectHotelInfoByHotelId($hotel_id);
			
			if($hotel_info) {
				$data['hotel_info'] = $hotel_info;
				
				if(isset($_SESSION['modify_hotel_status_success'])) {
					$data['success_message'] = '酒店公开状态更新成功';
					unset($_SESSION['modify_hotel_status_success']);
				}
				if(isset($_SESSION['modify_hotel_status_error'])) {
					$data['error_message'] = '酒店公开状态更新失敗 请重新尝试';
					unset($_SESSION['modify_hotel_status_error']);
				}
				if(isset($_SESSION['add_hotel_success'])) {
					$data['success_message'] = '酒店添加成功';
					unset($_SESSION['add_hotel_success']);
				}
				if(isset($_SESSION['modify_hotel_success'])) {
					$data['success_message'] = '酒店信息修改成功';
					unset($_SESSION['modify_hotel_success']);
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/hotel_detail', $data, false));
			} else {
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			}
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}
	
	/**
	 * 酒店公开状态更新
	 * @access  public
	 * @return  Response
	 */
	public function action_modifyhotelstatus($param = null)
	{
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['page'], $_POST['modify_id'], $_POST['modify_value'])) {
		if(isset($_POST['page'], $_POST['modify_id'], $_POST['modify_value'])) {
			if($_POST['page'] == 'hotel_detail') {
				//删除信息检查
				switch($_POST['modify_value']) {
					case 'publish':
						$hotel_status = '1';
						break;
					case 'protected':
						$hotel_status = '0';
						break;
					default:
						$hotel_status = '';
						break;
				}
				$params_update = array(
					'hotel_id' => $_POST['modify_id'],
					'hotel_status' => $hotel_status,
				);
				$result_check = Model_Hotel::CheckUpdateHotelStatusById($params_update);
				if($result_check['result']) {
					//数据删除
					$result_update = Model_Hotel::UpdateHotelStatusById($params_update);
					
					if($result_update) {
						$_SESSION['modify_hotel_status_success'] = true;
						header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/hotel_detail/' . $_POST['modify_id'] . '/');
						exit;
					}
				}
			}
		}
		$_SESSION['modify_hotel_status_error'] = true;
		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/hotel_detail/' . $_POST['modify_id'] . '/');
		exit;
	}

}