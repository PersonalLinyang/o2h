<?php
/* 
 * 房型修改页
 */

class Controller_Admin_Service_Roomtype_Modifyroomtype extends Controller_Admin_App
{

	/**
	 * 房型修改页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($room_type_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		try {
			if(!is_numeric($room_type_id)) {
				//房型ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 21)) {
				//当前登陆用户不具备房型管理的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//获取原本房型信息
				$params_select = array(
					'room_type_id' => $room_type_id,
					'active_only' => true,
				);
				$room_type = Model_Roomtype::SelectRoomType($params_select);
				
				if(!$room_type) {
					//不存在该ID的房型
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
				
				//页面标题
				$data['page_title'] ='房型修改';
				//表单页面索引
				$data['form_page_index'] = 'modify_room_type';
				
				//form控件默认值设定
				$data['input_room_type_name'] = $room_type['room_type_name'];
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是房型修改页
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_room_type_name'] = isset($_POST['room_type_name']) ? trim($_POST['room_type_name']) : $data['input_room_name'];
						
						//修改房型用数据生成
						$params_update = array(
							'room_type_id' => $room_type_id,
							'room_type_name' => $data['input_room_type_name'],
						);
						
						//输入内容检查
						$result_check = Model_Roomtype::CheckEditRoomType($params_update);
						
						if($result_check['result']) {
							//更新房型信息
							$result_update = Model_Roomtype::UpdateRoomType($params_update);
							
							if($result_update) {
								//更新批量导入酒店用模板
								$result_excel = Model_Hotel::ModifyHotelModelExcel();
								
								if($result_excel) {
									//修改成功 页面跳转
									$_SESSION['modify_room_type_success'] = true;
									header('Location: //' . $_SERVER['HTTP_HOST'] . '/admin/room_type_list/');
									exit;
								} else {
									//修改成功 但模板更新失败 页面跳转
									$_SESSION['modify_room_type_error'] = 'error_excel';
									header('Location: //' . $_SERVER['HTTP_HOST'] . '/admin/room_type_list/');
									exit;
								}
							} else {
								$error_message_list[] = '数据库错误：数据修改失败';
							}
						} else {
							//获取错误信息
							foreach($result_check['error'] as $update_error) {
								switch($update_error) {
									case 'empty_room_type_name': 
										$error_message_list[] = '请输入房型名';
										break;
									case 'long_room_type_name': 
										$error_message_list[] = '房型名不能超过50字';
										break;
									case 'dup_room_type_name': 
										$error_message_list[] = '该房型名与其他房型重复,请选用其他房型名';
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
				return Response::forge(View::forge($this->template . '/admin/service/room_type/edit_room_type', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}