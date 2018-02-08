<?php
/* 
 * 添加旅游路线页
 */

class Controller_Admin_Service_Route_AddRoute extends Controller_Admin_App
{

	/**
	 * 添加旅游路线页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($page = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		try {
			if(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 22)) {
				//当前登陆用户不具备添加旅游路线的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//页面标题
				$data['page_title'] ='添加旅游路线';
				//表单页面索引
				$data['form_page_index'] = 'add_route';
				//返回页URL
				$data['return_page_url'] = '/admin/route_list/';
				if(isset($_SERVER['HTTP_REFERER'])) {
					if(strstr($_SERVER['HTTP_REFERER'], 'admin/route_list')) {
						$data['return_page_url'] = $_SERVER['HTTP_REFERER'];
					}
				}
				
				//form控件默认值设定
				$data['input_route_name'] = '';
				$data['input_route_description'] = '';
				$data['input_route_price_min'] = '';
				$data['input_route_price_max'] = '';
				$data['input_route_base_cost'] = '';
				$data['input_route_traffic_cost'] = '';
				$data['input_route_parking_cost'] = '';
				$data['input_route_total_cost'] = 0;
				$data['input_route_status'] = '';
				$data['input_detail_list'] = array();
				$data['input_main_image'] = '';
				$data['main_image_url'] = '';
				$data['spot_list'] = Model_Spot::SelectSpotSimpleList(array('active_only' => true, 'spot_status' => array(1)));
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是添加旅游路线页
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_route_name'] = isset($_POST['route_name']) ? trim($_POST['route_name']) : $data['input_route_name'];
						$data['input_route_description'] = isset($_POST['route_description']) ? trim($_POST['route_description']) : $data['input_route_description'];
						$data['input_route_price_min'] = isset($_POST['route_price_min']) ? trim($_POST['route_price_min']) : $data['input_route_price_min'];
						$data['input_route_price_max'] = isset($_POST['route_price_min']) ? trim($_POST['route_price_min']) : $data['input_route_price_min'];
						$data['input_route_base_cost'] = isset($_POST['route_base_cost']) ? trim($_POST['route_base_cost']) : $data['input_route_base_cost'];
						$data['input_route_traffic_cost'] = isset($_POST['route_traffic_cost']) ? trim($_POST['route_traffic_cost']) : $data['input_route_traffic_cost'];
						$data['input_route_parking_cost'] = isset($_POST['route_parking_cost']) ? trim($_POST['route_parking_cost']) : $data['input_route_parking_cost'];
						$input_route_base_cost = empty($data['input_route_base_cost']) ? 0 : $data['input_route_base_cost'];
						$input_route_traffic_cost = empty($data['input_route_traffic_cost']) ? 0 : $data['input_route_traffic_cost'];
						$input_route_parking_cost = empty($data['input_route_parking_cost']) ? 0 : $data['input_route_parking_cost'];
						if(is_numeric($input_route_base_cost) && is_numeric($input_route_traffic_cost) && is_numeric($input_route_traffic_cost)) {
							$data['input_route_total_cost'] = intval($input_route_base_cost) + intval($input_route_traffic_cost) + intval($input_route_parking_cost);
						} else {
							$data['input_route_total_cost'] = '请在基本成本,交通费,停车费输入数字';
						}
						$data['input_route_status'] = isset($_POST['route_status']) ? $_POST['route_status'] : $data['input_route_status'];
						
						if(isset($_POST['route_detail_num'])) {
							if(is_array($_POST['route_detail_num'])) {
								foreach($_POST['route_detail_num'] as $detail_num) {
									$data['input_detail_list'][] = array(
										'route_detail_day' => isset($_POST['route_detail_day_' . $detail_num]) ? trim($_POST['route_detail_day_' . $detail_num]) : '',
										'route_detail_title' => isset($_POST['route_detail_title_' . $detail_num]) ? trim($_POST['route_detail_title_' . $detail_num]) : '',
										'route_detail_content' => isset($_POST['route_detail_content_' . $detail_num]) ? trim($_POST['route_detail_content_' . $detail_num]) : '',
										'route_spot_list' => isset($_POST['route_spot_list_' . $detail_num]) ? $_POST['route_spot_list_' . $detail_num] : array(),
										'route_breakfast' => isset($_POST['route_breakfast_' . $detail_num]) ? trim($_POST['route_breakfast_' . $detail_num]) : '',
										'route_lunch' => isset($_POST['route_lunch_' . $detail_num]) ? trim($_POST['route_lunch_' . $detail_num]) : '',
										'route_dinner' => isset($_POST['route_dinner_' . $detail_num]) ? trim($_POST['route_dinner_' . $detail_num]) : '',
										'route_hotel' => isset($_POST['route_hotel_' . $detail_num]) ? trim($_POST['route_hotel_' . $detail_num]) : '',
									);
								}
							}
						}
						
						//本次上传图片处理
						if(isset($_FILES['main_image'])) {
							$files_upload = $_FILES['main_image'];
							//图片暂时保存
							$extension = '';
							switch($files_upload['type']) {
								case 'image/jpeg': 
									$extension = 'jpg';
									break;
								case 'image/png': 
									$extension = 'png';
									break;
								default:
									break;
							}
							if($extension) {
								$file_directory_tmp = DOCROOT . 'assets/img/tmp/' . $_SESSION['login_user']['id'] . '/route/';
								if(!file_exists($file_directory_tmp)) {
									mkdir($file_directory_tmp, 0777, TRUE);
								}
								$file_name_tmp = $file_directory_tmp . 'main_image.' . $extension;
								move_uploaded_file($files_upload["tmp_name"], $file_name_tmp);
								$data['input_main_image'] = '/assets/img/tmp/' . $_SESSION['login_user']['id'] . '/route/main_image.' . $extension;
							}
						}
						if($data['input_main_image'] == '') {
							if(isset($_POST['main_image_tmp'])) {
								if(file_exists(DOCROOT . $_POST['main_image_tmp'])) {
									$data['input_main_image'] = $_POST['main_image_tmp'];
								}
							}
						}
						
						//添加旅游路线用数据生成
						$param_insert = array(
							'route_id' => '',
							'route_name' => $data['input_route_name'],
							'route_description' => $data['input_route_description'],
							'route_price_min' => $data['input_route_price_min'],
							'route_price_max' => $data['input_route_price_max'],
							'route_base_cost' => $data['input_route_base_cost'],
							'route_traffic_cost' => $data['input_route_traffic_cost'],
							'route_parking_cost' => $data['input_route_parking_cost'],
							'route_status' => $data['input_route_status'],
							'created_by' => $_SESSION['login_user']['id'],
							'modified_by' => $_SESSION['login_user']['id'],
							'detail_list' => $data['input_detail_list'],
						);
						
						//输入内容检查
						$result_check = Model_Route::CheckEditRoute($param_insert);
						var_dump($result_check);
						if($result_check['result'] && $data['input_main_image'] && file_exists(DOCROOT . $data['input_main_image'])) {
							//添加旅游路线
							$result_insert = Model_Route::InsertRoute($param_insert);
							
							if($result_insert) {
								$file_name_tmp = DOCROOT . $data['input_main_image'];
								
								//调整PC用图片尺寸
								$file_directory_pc = DOCROOT . 'assets/img/pc/upload/route/' . $result_insert . '/';
								if(!file_exists($file_directory_pc)) {
									mkdir($file_directory_pc, 0777, TRUE);
								}
								$image_option_list_pc = Model_Imageoptimize::SelectImageOptionList(array('image_type' => 'route_main_image', 'image_device' => 'pc'));
								if(count($image_option_list_pc)) {
									foreach($image_option_list_pc as $image_option_pc) {
										Model_Imageoptimize::ImageResizeToJpg($file_name_tmp, $image_option_pc['max_width'], $image_option_pc['max_height'], 
												$file_directory_pc . $image_option_pc['image_option_slug'] . '.jpg');
									}
								}
								
								//调整SP用图片尺寸
								$file_directory_sp = DOCROOT . 'assets/img/sp/upload/route/' . $result_insert . '/';
								if(!file_exists($file_directory_sp)) {
									mkdir($file_directory_sp, 0777, TRUE);
								}
								$image_option_list_sp = Model_Imageoptimize::SelectImageOptionList(array('image_type' => 'route_main_image', 'image_device' => 'sp'));
								if(count($image_option_list_sp)) {
									foreach($image_option_list_sp as $image_option_sp) {
										Model_Imageoptimize::ImageResizeToJpg($file_name_tmp, $image_option_sp['max_width'], $image_option_sp['max_height'], 
												$file_directory_sp . $image_option_sp['image_option_slug'] . '.jpg');
									}
								}
								
								//删除图片临时文件
								unlink($file_name_tmp);
								
								$_SESSION['add_route_success'] = true;
								header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/route_detail/' . $result_insert . '/');
								exit;
							} else {
								$error_message_list[] = '数据库错误：数据添加失败';
							}
						} else {
							//获取错误信息
							foreach($result_check['error'] as $insert_error) {
								switch($insert_error) {
									case 'empty_route_name':
										$error_message_list[] = '请输入旅游路线名';
										break;
									case 'long_route_name':
										$error_message_list[] = '旅游路线名不能超过100字';
										break;
									case 'dup_route_name':
										$error_message_list[] = '该旅游路线名与其他旅游路线重复,请选用其他旅游路线名';
										break;
									case 'empty_route_description':
										$error_message_list[] = '请输入旅游路线简介';
										break;
									case 'empty_route_price':
										$error_message_list[] = '请输入价格';
										break;
									case 'nonum_route_price':
									case 'minus_route_price':
										$error_message_list[] = '请在价格部分输入非负数字';
										break;
									case 'error_route_price':
										$error_message_list[] = '最低价不能高于最高价';
										break;
									case 'empty_route_base_cost':
										$error_message_list[] = '请输入基本成本';
										break;
									case 'nonum_route_base_cost':
									case 'minus_route_base_cost':
										$error_message_list[] = '请在基本成本部分输入非负数字';
										break;
									case 'empty_route_traffic_cost':
										$error_message_list[] = '请输入交通费';
										break;
									case 'nonum_route_traffic_cost':
									case 'minus_route_traffic_cost':
										$error_message_list[] = '请在交通费部分输入非负数字';
										break;
									case 'empty_route_parking_cost':
										$error_message_list[] = '请输入停车费';
										break;
									case 'nonum_route_parking_cost':
									case 'minus_route_parking_cost':
										$error_message_list[] = '请在停车费部分输入非负数字';
										break;
									case 'empty_detail_list':
										$error_message_list[] = '请至少添加一天的详细日程';
										break;
									case 'empty_route_detail_title':
										$error_message_list[] = '请输入详细日程标题';
										break;
									case 'long_route_detail_title':
										$error_message_list[] = '详细日程标题不能超过100字';
										break;
									case 'empty_route_detail_content':
										$error_message_list[] = '请输入详细日程简介';
										break;
									default:
										$error_message_list[] = '发生系统错误,请重新尝试添加';
										break;
								}
							}
							if(empty($data['input_main_image'])) {
								$error_message_list[] = '请上传主图';
							} elseif(!file_exists(DOCROOT . $data['input_main_image'])) {
								$error_message_list[] = '发生系统错误,请重新尝试添加';
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
				return Response::forge(View::forge($this->template . '/admin/service/route/edit_route', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}