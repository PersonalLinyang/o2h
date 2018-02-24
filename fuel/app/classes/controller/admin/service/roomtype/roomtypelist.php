<?php
/* 
 * 房型一览页
 */

class Controller_Admin_Service_Roomtype_Roomtypelist extends Controller_Admin_App
{

	/**
	 * 房型一览
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		try {
			if(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 21)) {
				//当前登陆用户不具备房型管理的权限
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
					} elseif(strstr($_SERVER['HTTP_REFERER'], 'admin/add_room_type') || strstr($_SERVER['HTTP_REFERER'], 'admin/modify_room_type')) {
						if(isset($_SESSION['url_return_hotel_list'])) {
							$data['hotel_list_url'] = $_SESSION['url_return_hotel_list'];
						}
					}
				}
				//暂时保留一览页URL
				$_SESSION['url_return_hotel_list'] = $data['hotel_list_url'];
				
				//获取房型信息
				$params_select = array(
					'active_only' => true,
					'hotel_count_flag' => true,
				);
				
				$data['room_type_list'] = Model_RoomType::SelectRoomTypeList($params_select);
				
				//房型添加结果处理
				if(isset($_SESSION['add_room_type_success'])) {
					$data['success_message'] = '房型添加成功';
					unset($_SESSION['add_room_type_success']);
				}
				if(isset($_SESSION['add_room_type_error'])) {
					switch($_SESSION['add_room_type_error']) {
						case 'error_excel':
							$data['error_message'] = '房型添加成功,但批量导入酒店用模板未能成功更新,请联系系统开发人员进行手动修复';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新添加';
							break;
					}
					unset($_SESSION['add_room_type_error']);
				}
				
				//房型信息修改结果处理
				if(isset($_SESSION['modify_room_type_success'])) {
					$data['success_message'] = '房型修改成功';
					unset($_SESSION['modify_room_type_success']);
				}
				if(isset($_SESSION['modify_room_type_error'])) {
					switch($_SESSION['modify_room_type_error']) {
						case 'error_excel':
							$data['error_message'] = '房型修改成功,但批量导入酒店用模板未能成功更新,请联系系统开发人员进行手动修复';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新修改';
							break;
					}
					unset($_SESSION['modify_room_type_error']);
				}
				
				//房型削除结果处理
				if(isset($_SESSION['delete_room_type_success'])) {
					$data['success_message'] = '房型削除成功';
					unset($_SESSION['delete_room_type_success']);
				}
				if(isset($_SESSION['delete_room_type_error'])) {
					switch($_SESSION['delete_room_type_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备删除房型的权限';
							break;
						case 'error_room_type_id':
							$data['error_message'] = '您要删除的房型不存在,请确认该房型是否已经被删除';
							break;
						case 'error_hotel_list':
							$data['error_message'] = '尚存在可选择该房型的酒店,请在修改这些酒店的可选房型后重新尝试删除';
							break;
						case 'error_excel':
							$data['error_message'] = '房型削除成功,但批量导入酒店用模板未能成功更新,请联系系统开发人员进行手动修复';
							break;
						case 'error_db':
							$data['error_message'] = '发生数据库错误,请重新尝试删除';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新删除';
							break;
					}
					unset($_SESSION['delete_room_type_error']);
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/room_type/room_type_list', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

	/**
	 * 获取房型列表
	 * @access  public
	 * @return  Response
	 */
	public function action_apiroomtypelist($page = null)
	{
		$result = array('result' => false, 'room_type_list' => array());
		try {
			$allow_page_list = array('edit_customer');
			
			if(isset($_POST['page'])) {
				if(in_array($_POST['page'], $allow_page_list)) {
					$room_type_list = Model_Roomtype::SelectRoomTypeList(array('active_only' => true));
					$result = array('result' => true, 'room_type_list' => $room_type_list);
				}
			}
		} catch (Exception $e) {
		}
		return json_encode($result);
	}

}