<?php
/* 
 * 酒店类别一览页
 */

class Controller_Admin_Service_Hoteltype_Hoteltypelist extends Controller_Admin_App
{

	/**
	 * 酒店类别一览
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
				//当前登陆用户不具备酒店类别管理的权限
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
					} elseif(strstr($_SERVER['HTTP_REFERER'], 'admin/add_hotel_type') || strstr($_SERVER['HTTP_REFERER'], 'admin/modify_hotel_type')) {
						if(isset($_SESSION['url_return_hotel_list'])) {
							$data['hotel_list_url'] = $_SESSION['url_return_hotel_list'];
						}
					}
				}
				//暂时保留一览页URL
				$_SESSION['url_return_hotel_list'] = $data['hotel_list_url'];
				
				//获取酒店类别信息
				$params_select = array(
					'active_only' => true,
					'hotel_count_flag' => true,
				);
				
				$data['hotel_type_list'] = Model_HotelType::SelectHotelTypeList($params_select);
				
				//酒店类别添加结果处理
				if(isset($_SESSION['add_hotel_type_success'])) {
					$data['success_message'] = '酒店类别添加成功';
					unset($_SESSION['add_hotel_type_success']);
				}
				if(isset($_SESSION['add_hotel_type_error'])) {
					switch($_SESSION['add_hotel_type_error']) {
						case 'error_excel':
							$data['error_message'] = '酒店类别添加成功,但批量导入酒店用模板未能成功更新,请联系系统开发人员进行手动修复';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新添加';
							break;
					}
					unset($_SESSION['add_hotel_type_error']);
				}
				
				//酒店类别信息修改结果处理
				if(isset($_SESSION['modify_hotel_type_success'])) {
					$data['success_message'] = '酒店类别修改成功';
					unset($_SESSION['modify_hotel_type_success']);
				}
				if(isset($_SESSION['modify_hotel_type_error'])) {
					switch($_SESSION['modify_hotel_type_error']) {
						case 'error_excel':
							$data['error_message'] = '酒店类别修改成功,但批量导入酒店用模板未能成功更新,请联系系统开发人员进行手动修复';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新修改';
							break;
					}
					unset($_SESSION['modify_hotel_type_error']);
				}
				
				//酒店类别削除结果处理
				if(isset($_SESSION['delete_hotel_type_success'])) {
					$data['success_message'] = '酒店类别削除成功';
					unset($_SESSION['delete_hotel_type_success']);
				}
				if(isset($_SESSION['delete_hotel_type_error'])) {
					switch($_SESSION['delete_hotel_type_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备删除酒店类别的权限';
							break;
						case 'error_hotel_type_id':
							$data['error_message'] = '您要删除的酒店类别不存在,请确认该酒店类别是否已经被删除';
							break;
						case 'error_hotel_list':
							$data['error_message'] = '尚存在属于该类别的酒店,请在修改这些酒店的类别后重新尝试删除';
							break;
						case 'error_excel':
							$data['error_message'] = '酒店类别削除成功,但批量导入酒店用模板未能成功更新,请联系系统开发人员进行手动修复';
							break;
						case 'error_db':
							$data['error_message'] = '发生数据库错误,请重新尝试删除';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新删除';
							break;
					}
					unset($_SESSION['delete_hotel_type_error']);
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/hotel_type/hotel_type_list', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}