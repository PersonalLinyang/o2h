<?php
/* 
 * 酒店类别名称修改页
 */

class Controller_Admin_Service_Modifyhoteltype extends Controller_Admin_App
{

	/**
	 * 酒店类别名称修改页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($hotel_type_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['error_message'] = '';
			
			$hotel_type_info = Model_HotelType::SelectHotelTypeById($hotel_type_id);
			
			if($hotel_type_info) {
				$data['hotel_type_name'] = $hotel_type_info['hotel_type_name'];
				$data['input_hotel_type_name'] = '';
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					//数据来源检验
					if($_POST['page'] == 'modify_hotel_type') {
						if(isset($_POST['name'])) {
							
							//修改酒店用数据生成
							$param_update = array(
								'hotel_type_id' => $hotel_type_id,
								'hotel_type_name' => $_POST['name'],
							);
							
							//修改内容检查
							$result_check = Model_HotelType::CheckUpdateHotelType($param_update);
							
							if($result_check['result']) {
								//数据修改
								$result_update = Model_HotelType::UpdateHotelType($param_update);
								
								if($result_update) {
									//修改成功 页面跳转
									$_SESSION['modify_hotel_type_success'] = true;
									header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/hotel_type_list/');
									exit;
								} else {
									$error_message_list[] = '数据库错误：数据添加失败';
								}
							} else {
								foreach($result_check['error'] as $insert_error) {
									switch($insert_error) {
										case 'nonum_id':
											$error_message_list[] = '酒店类别编号不是数字';
											break;
										case 'empty_name':
											$error_message_list[] = '请输入修改后酒店类别名称';
											break;
										case 'nomodify':
											$error_message_list[] = '请输入与原名称不同的酒店类别名称';
											break;
										case 'duplication':
											$error_message_list[] = '已存在该名称的酒店类别，无法重复设定';
											break;
										default:
											break;
									}
								}
							}
						} else {
							$error_message_list[] = '系统错误：请勿修改表单中的控件名称';
						}
						
						//检查发生错误时将之前输入的信息反映至表单中
						$data['input_hotel_type_name'] = isset($_POST['name']) ? $_POST['name'] : '';
					
						//输出错误信息
						if(count($error_message_list)) {
							$data['error_message'] = implode('<br/>', $error_message_list);
						}
					} else {
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
						exit;
					}
				} else {
					$data['input_hotel_type_name'] = '';
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/modify_hotel_type', $data, false));
			} else {
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			}
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}

}