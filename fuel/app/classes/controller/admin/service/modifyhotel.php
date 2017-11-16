<?php
/* 
 * 酒店信息修改页
 */

class Controller_Admin_Service_Modifyhotel extends Controller_Admin_App
{

	/**
	 * 酒店信息修改页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($hotel_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['error_message'] = '';
			
			$hotel_info = Model_Hotel::SelectHotelInfoByHotelId($hotel_id);
			
			if($hotel_info) {
				$data['hotel_id'] = $hotel_id;
				$data['area_list'] = Model_Area::GetAreaListAll();
				$data['hotel_type_list'] = Model_HotelType::GetHotelTypeListAll();
				$data['input_hotel_name'] = '';
				$data['input_hotel_area'] = '';
				$data['input_hotel_type'] = '';
				$data['input_hotel_price'] = '';
				$data['input_hotel_status'] = '';
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					//数据来源检验
					if($_POST['page'] == 'modify_hotel') {
						if(isset($_POST['hotel_name']) && isset($_POST['hotel_area']) && isset($_POST['hotel_type']) 
								&& isset($_POST['hotel_price']) && isset($_POST['hotel_status'])) {
							//修改酒店用数据生成
							$param_update_hotel = array(
								'hotel_id' => $hotel_id,
								'hotel_name' => $_POST['hotel_name'],
								'hotel_area' => $_POST['hotel_area'],
								'hotel_type' => $_POST['hotel_type'],
								'hotel_price' => $_POST['hotel_price'],
								'hotel_status' => $_POST['hotel_status'],
							);
							
							//修改内容检查
							$result_check = Model_Hotel::CheckUpdateHotel($param_update_hotel);
							
							if($result_check['result']) {
								//数据修改
								$result_update = Model_Hotel::UpdateHotel($param_update_hotel);
								
								if($result_update) {
									//修改成功 页面跳转
									$_SESSION['modify_hotel_success'] = true;
									header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/hotel_detail/' . $hotel_id . '/');
									exit;
								} else {
									$error_message_list[] = '数据库错误：数据添加失败';
								}
							} else {
								foreach($result_check['error'] as $insert_error) {
									switch($insert_error) {
										case 'empty_name':
											$error_message_list[] = '酒店名不能为空';
											break;
										case 'nonum_price':
											$error_message_list[] = '酒店的价格不能为空';
											break;
										case 'nonum_area':
											$error_message_list[] = '请选择酒店所属地区';
											break;
										case 'nonum_type':
											$error_message_list[] = '请选择酒店类型';
											break;
										case 'nobool_status':
											$error_message_list[] = '请选择公开状态';
											break;
										case 'minus_price':
											$error_message_list[] = '价格不能为负';
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
						$data['input_hotel_name'] = isset($_POST['hotel_name']) ? $_POST['hotel_name'] : '';
						$data['input_hotel_area'] = isset($_POST['hotel_area']) ? $_POST['hotel_area'] : '';
						$data['input_hotel_type'] = isset($_POST['hotel_type']) ? $_POST['hotel_type'] : '';
						$data['input_hotel_status'] = isset($_POST['hotel_status']) ? $_POST['hotel_status'] : '';
						$data['input_hotel_price'] = isset($_POST['hotel_price']) ? $_POST['hotel_price'] : '';
					
						//输出错误信息
						if(count($error_message_list)) {
							$data['error_message'] = implode('<br/>', $error_message_list);
						}
					} else {
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
						exit;
					}
				} else {
					$data['input_hotel_name'] = $hotel_info['hotel_name'];
					$data['input_hotel_area'] = $hotel_info['hotel_area_id'];
					$data['input_hotel_type'] = $hotel_info['hotel_type_id'];
					$data['input_hotel_price'] = $hotel_info['hotel_price'];
					$data['input_hotel_status'] = $hotel_info['hotel_status'];
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/modify_hotel', $data, false));
			} else {
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			}
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}

}