<?php
/* 
 * 添加酒店页
 */

class Controller_Admin_Service_Hotel_Addhotel extends Controller_Admin_App
{

	/**
	 * 添加酒店页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		try {
			if(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 16)) {
				//当前登陆用户不具备添加酒店的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//页面标题
				$data['page_title'] ='添加酒店';
				//表单页面索引
				$data['form_page_index'] = 'add_hotel';
				//返回页URL
				$data['return_page_url'] = '/admin/hotel_list/';
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
				$data['input_hotel_name'] = '';
				$data['input_hotel_area'] = '';
				$data['input_hotel_type'] = '';
				$data['input_hotel_price'] = '';
				$data['input_hotel_status'] = '';
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是添加酒店页
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_hotel_name'] = isset($_POST['hotel_name']) ? trim($_POST['hotel_name']) : $data['input_hotel_name'];
						$data['input_hotel_area'] = isset($_POST['hotel_area']) ? $_POST['hotel_area'] : $data['input_hotel_area'];
						$data['input_hotel_type'] = isset($_POST['hotel_type']) ? $_POST['hotel_type'] : $data['input_hotel_type'];
						$data['input_hotel_price'] = isset($_POST['hotel_price']) ? trim($_POST['hotel_price']) : $data['input_hotel_price'];
						$data['input_hotel_status'] = isset($_POST['hotel_status']) ? $_POST['hotel_status'] : $data['input_hotel_status'];
						
						//添加酒店用数据生成
						$params_insert = array(
							'hotel_id' => '',
							'hotel_name' => $data['input_hotel_name'],
							'hotel_area' => $data['input_hotel_area'],
							'hotel_type' => $data['input_hotel_type'],
							'hotel_price' => $data['input_hotel_price'],
							'hotel_status' => $data['input_hotel_status'],
							'created_by' => $_SESSION['login_user']['id'],
							'modified_by' => $_SESSION['login_user']['id'],
						);
						
						//输入内容检查
						$result_check = Model_Hotel::CheckEditHotel($params_insert);
						
						if($result_check['result']) {
							//添加酒店
							$result_insert = Model_Hotel::InsertHotel($params_insert);
							
							if($result_insert) {
								//添加成功 页面跳转
								$_SESSION['add_hotel_success'] = true;
								header('Location: //' . $_SERVER['HTTP_HOST'] . '/admin/hotel_detail/' . $result_insert . '/');
								exit;
							}
						} else {
							//获取错误信息
							foreach($result_check['error'] as $insert_error) {
								switch($insert_error) {
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
						
						$error_message_list = array_unique($error_message_list);
						
						//输出错误信息
						if(count($error_message_list)) {
							$data['error_message'] = implode('<br/>', $error_message_list);
						}
					}
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/hotel/edit_hotel', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}