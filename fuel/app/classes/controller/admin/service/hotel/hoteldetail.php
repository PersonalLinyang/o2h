<?php
/* 
 * 酒店详细信息页
 */

class Controller_Admin_Service_Hotel_Hoteldetail extends Controller_Admin_App
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
		
		try {
			if(!is_numeric($hotel_id)) {
				//酒店ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'sub_group', 11)) {
				//当前登陆用户不具备查看酒店的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['success_message'] = '';
				$data['error_message'] = '';
				
				//获取返回一览页时的一览页URL
				$data['hotel_list_url'] = '/admin/hotel_list/';
				if(isset($_SERVER['HTTP_REFERER'])) {
					if(strstr($_SERVER['HTTP_REFERER'], 'admin/hotel_list')) {
						//通过一览页链接进入
						$data['hotel_list_url'] = $_SERVER['HTTP_REFERER'];
					} elseif(strstr($_SERVER['HTTP_REFERER'], 'admin/modify_hotel/' . $hotel_id) || strstr($_SERVER['HTTP_REFERER'], 'admin/hotel_detail/' . $hotel_id)) {
						if(isset($_SESSION['hotel_list_url_detail'])) {
							$data['hotel_list_url'] = $_SESSION['hotel_list_url_detail'];
						}
					}
				}
				//暂时保留一览页URL
				$_SESSION['hotel_list_url_detail'] = $data['hotel_list_url'];
				
				//获取酒店信息
				$hotel = Model_Hotel::SelectHotel(array('hotel_id' => $hotel_id, 'active_only' => true));
				
				if(!$hotel) {
					//不存在该ID的酒店
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
				
				//酒店信息
				$data['hotel_info'] = $hotel;
				$room_type_name_list = array();
				foreach($hotel['room_type_list'] as $room_type) {
					$room_type_name_list[] = $room_type['room_type_name'];
				}
				$data['room_type_name_list'] = $room_type_name_list;
				
				if($hotel['created_by'] == $_SESSION['login_user']['id']) {
					//是否具备酒店编辑权限
					$data['edit_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 16);
				} else {
					//是否具备修改其他用户所登陆的酒店信息权限
					$data['edit_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 6);
				}
				
				//输出提示信息
				if(isset($_SESSION['add_hotel_success'])) {
					$data['success_message'] = '酒店添加成功';
					unset($_SESSION['add_hotel_success']);
				}
				if(isset($_SESSION['modify_hotel_success'])) {
					$data['success_message'] = '酒店信息修改成功';
					unset($_SESSION['modify_hotel_success']);
				}
				if(isset($_SESSION['modify_hotel_status_success'])) {
					$data['success_message'] = '酒店公开状态更新成功';
					unset($_SESSION['modify_hotel_status_success']);
				}
				if(isset($_SESSION['modify_hotel_status_error'])) {
					$data['error_message'] = '酒店公开状态更新失敗 请重新尝试';
					unset($_SESSION['modify_hotel_status_error']);
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/hotel/hotel_detail', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}