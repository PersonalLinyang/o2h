<?php
/* 
 * 添加酒店类别页
 */

class Controller_Admin_Service_AddHotelType extends Controller_Admin_App
{

	/**
	 * 添加酒店类别
	 * @access  public
	 * @return  Response
	 */
	public function action_index($page = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['input_name'] = '';
			$data['error_message'] = '';
			
			if(isset($_POST['page'])) {
				$error_message_list = array();
				
				if($_POST['page'] == 'add_hotel_type') {
					if(isset($_POST['name'])) {
						$params_insert = array(
							'hotel_type_name' => trim($_POST['name']),
						);
						
						//输入内容检查
						$result_check = Model_Hoteltype::CheckInsertHotelType($params_insert);
						
						if($result_check['result']) {
							//数据添加
							$result_insert = Model_Hoteltype::InsertHotelType($params_insert);
							
							if($result_insert) {
								$_SESSION['add_hotel_type_success'] = true;
								header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/hotel_type_list/');
								exit;
							} else {
								$error_message_list[] = '数据库错误：数据添加失败';
							}
						} else {
							foreach($result_check['error'] as $insert_error) {
								switch($insert_error) {
									case 'noset_name':
										$error_message_list[] = '系统错误：请勿修改表单中的控件名称';
										break;
									case 'empty_name':
										$error_message_list[] = '请输入酒店类别名称';
										break;
									case 'duplication':
										$error_message_list[] = '已存在该名称的酒店类别，无法重复添加';
										break;
									default:
										break;
								}
							}
						}
					} else {
						$error_message_list[] = '系统错误：请勿修改表单中的控件名称';
					}
					
					$data['input_name'] = isset($_POST['name']) ? $_POST['name'] : '';
					
					//输出错误信息
					if(count($error_message_list)) {
						$data['error_message'] = implode('<br/>', $error_message_list);
					}
				} else {
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
			}
			
			//调用View
			return Response::forge(View::forge($this->template . '/admin/service/add_hotel_type', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}

}