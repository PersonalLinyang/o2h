<?php
/* 
 * 酒店信息修改页
 */

class Controller_Admin_Service_Hotel_Modifyhotel extends Controller_Admin_App
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
		
//		try {
			if(!is_numeric($hotel_id)) {
				//酒店ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 6)) {
				//当前登陆用户不具备修改酒店的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//获取原本酒店信息
				$hotel = Model_Hotel::SelectHotel(array('hotel_id' => $hotel_id, 'active_only' => true));
				
				if(!$hotel) {
					//不存在该ID的酒店
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 6) && $hotel['created_by'] != $_SESSION['login_user']['id']) {
					//该ID的酒店为其他用户创建且当前登陆用户不具备编辑他人创建酒店的权限
					return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
					exit;
				}
				
				//页面标题
				$data['page_title'] ='酒店信息修改';
				//表单页面索引
				$data['form_page_index'] = 'modify_hotel';
				//返回页URL
				$data['return_page_url'] = '/admin/hotel_detail/' . $hotel_id . '/';
				if(isset($_SERVER['HTTP_REFERER'])) {
					if(strstr($_SERVER['HTTP_REFERER'], 'admin/hotel_list')) {
						$data['return_page_url'] = $_SERVER['HTTP_REFERER'];
					}
				}
				
				//获取地区列表
				$data['area_list'] = Model_Area::GetAreaList(array('active_only' => true));
				//获取酒店类型列表
				$data['hotel_type_list'] = Model_HotelType::SelectHotelTypeList(array('active_only' => true));
				
				//form控件默认值设定
				$data['input_hotel_name'] = $hotel['hotel_name'];
				$data['input_hotel_area'] = $hotel['hotel_area'];
				$data['input_hotel_type'] = $hotel['hotel_type'];
				$data['input_hotel_price'] = $hotel['hotel_price'];
				$data['input_hotel_status'] = $hotel['hotel_status'];
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是酒店信息修改页
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_hotel_name'] = isset($_POST['hotel_name']) ? trim($_POST['hotel_name']) : $data['input_hotel_name'];
						$data['input_hotel_area'] = isset($_POST['hotel_area']) ? $_POST['hotel_area'] : $data['input_hotel_area'];
						$data['input_hotel_type'] = isset($_POST['hotel_type']) ? $_POST['hotel_type'] : $data['input_hotel_type'];
						$data['input_hotel_price'] = isset($_POST['hotel_price']) ? trim($_POST['hotel_price']) : $data['input_hotel_price'];
						$data['input_hotel_status'] = isset($_POST['hotel_status']) ? $_POST['hotel_status'] : $data['input_hotel_status'];
						
						//修改酒店用数据生成
						$params_update = array(
							'hotel_id' => $hotel_id,
							'hotel_name' => $data['input_hotel_name'],
							'hotel_area' => $data['input_hotel_area'],
							'hotel_type' => $data['input_hotel_type'],
							'hotel_price' => $data['input_hotel_price'],
							'hotel_status' => $data['input_hotel_status'],
							'created_by' => $hotel['created_by'],
							'modified_by' => $_SESSION['login_user']['id'],
						);
						
						//更新内容检查
						$result_check = Model_Hotel::CheckEditHotel($params_update);
						
						if($result_check['result']) {
							//更新景点信息
							$result_update = Model_Hotel::UpdateHotel($params_update);
							
							if($result_update) {
								//更新成功 页面跳转
								$_SESSION['modify_hotel_success'] = true;
								header('Location: //' . $_SERVER['HTTP_HOST'] . '/admin/hotel_detail/' . $hotel_id . '/');
								exit;
							} else {
								$error_message_list[] = '数据库错误：数据添加失败';
							}
						} else {
							//获取错误信息
							foreach($result_check['error'] as $update_error) {
								switch($update_error) {
									case 'empty_hotel_name': 
										$error_message_list[] = '请输入酒店名';
										break;
									case 'long_hotel_name': 
										$error_message_list[] = '酒店名不能超过100字';
										break;
									case 'dup_hotel_name': 
										$error_message_list[] = '该酒店名与其他酒店重复,请选用其他酒店名';
										break;
									case 'empty_hotel_area': 
										$error_message_list[] = '请选择酒店所属地区';
										break;
									case 'empty_hotel_type': 
										$error_message_list[] = '请选择酒店类别';
										break;
									case 'noint_hotel_price': 
									case 'minus_hotel_price': 
										$error_message_list[] = '请在价格部分输入非负整数';
										break;
									default:
										$error_message_list[] = '发生系统错误,请重新尝试添加';
										break;
								}
							}
						}
						
						//输出错误信息
						$error_message_list = array_unique($error_message_list);
						if(count($error_message_list)) {
							$data['error_message'] = implode('<br/>', $error_message_list);
						}
					}
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/hotel/edit_hotel', $data, false));
			}
//		} catch (Exception $e) {
//			//发生系统异常
//			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
//		}
	}
	
	/**
	 * 酒店公开状态更新
	 * @access  public
	 * @return  Response
	 */
	public function action_modifyhotelstatus($param = null)
	{
//		try {
			if(isset($_POST['page'], $_POST['modify_id'], $_POST['modify_value'])) {
				if(is_numeric($_POST['modify_id']) && $_POST['page'] == 'hotel_detail') {
					$hotel_id = $_POST['modify_id'];
					
					//获取酒店信息
					$hotel = Model_Hotel::SelectHotel(array('hotel_id' => $hotel_id, 'active_only' => true));
					
					if($hotel) {
						if($hotel['created_by'] == $_SESSION['login_user']['id']) {
							//是否具备酒店编辑权限
							$edit_able_flag = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 16);
						} else {
							//是否具备修改其他用户所登陆的酒店信息权限
							$edit_able_flag = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 6);
						}
						
						if($edit_able_flag) {
							$params_update = array(
								'hotel_id' => $hotel_id,
								'hotel_status' => $_POST['modify_value'],
							);
							
							$result_check = Model_Hotel::CheckUpdateHotelStatus($params_update);
							
							if($result_check['result']) {
								//数据更新
								$result_update = Model_Hotel::UpdateHotelStatus($params_update);
								
								if($result_update) {
									//更新成功
									$_SESSION['modify_hotel_status_success'] = true;
									header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/hotel_detail/' . $_POST['modify_id'] . '/');
									exit;
								}
							}
						}
					}
				}
			}
			
			//更新失敗
			$_SESSION['modify_hotel_status_error'] = true;
			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/hotel_detail/' . $_POST['modify_id'] . '/');
			exit;
//		} catch (Exception $e) {
//			//发生系统异常
//			$_SESSION['modify_hotel_status_error'] = true;
//			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/hotel_detail/' . $_POST['modify_id'] . '/');
//			exit;
//		}
	}

}