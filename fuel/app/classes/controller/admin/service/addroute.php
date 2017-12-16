<?php
/* 
 * 添加路线页
 */

class Controller_Admin_Service_AddRoute extends Controller_Admin_App
{

	/**
	 * 添加路线
	 * @access  public
	 * @return  Response
	 */
	public function action_index($page = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['error_message'] = '';
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
			$data['spot_list'] = array();
			
			if(isset($_POST['page'])) {
				$error_message_list = array();
				
				if($_POST['page'] == 'add_route') {
					$data['input_route_name'] = isset($_POST['route_name']) ? trim($_POST['route_name']) : '';
					$data['input_route_description'] = isset($_POST['route_description']) ? trim($_POST['route_description']) : '';
					$data['input_route_price_min'] = isset($_POST['route_price_min']) ? trim($_POST['route_price_min']) : '';
					$data['input_route_price_max'] = isset($_POST['route_price_max']) ? trim($_POST['route_price_max']) : '';
					$data['input_route_base_cost'] = isset($_POST['route_base_cost']) ? trim($_POST['route_base_cost']) : '';
					$data['input_route_traffic_cost'] = isset($_POST['route_traffic_cost']) ? trim($_POST['route_traffic_cost']) : '';
					$data['input_route_parking_cost'] = isset($_POST['route_parking_cost']) ? trim($_POST['route_parking_cost']) : '';
					$data['input_route_status'] = isset($_POST['route_status']) ? trim($_POST['route_status']) : '';
					$data['spot_list'] = Model_Spot::SelectSpotSimpleListActive();
					
					if(isset($data['input_route_base_cost']) && isset($data['input_route_traffic_cost']) && isset($data['input_route_parking_cost'])) {
						$input_route_base_cost = 0;
						$input_route_traffic_cost = 0;
						$input_route_parking_cost = 0;
						if($data['input_route_base_cost'] != '') {
							$input_route_base_cost = $data['input_route_base_cost'];
						}
						if($data['input_route_traffic_cost'] != '') {
							$input_route_traffic_cost = $data['input_route_traffic_cost'];
						}
						if($data['input_route_parking_cost'] != '') {
							$input_route_parking_cost = $data['input_route_parking_cost'];
						}
						if(is_numeric($input_route_base_cost) && is_numeric($input_route_traffic_cost) && is_numeric($input_route_parking_cost)) {
							$data['input_route_total_cost'] = intval($input_route_base_cost) + intval($input_route_traffic_cost) + intval($input_route_parking_cost);
						} else {
							$data['input_route_total_cost'] = '请在基本成本,交通费,停车费输入数字';
						}
					}
					
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
					
					if(isset($_POST['route_name']) && isset($_POST['route_description']) && isset($_POST['route_price_min']) && isset($_POST['route_price_max']) 
							&& isset($_POST['route_base_cost']) && isset($_POST['route_traffic_cost']) && isset($_POST['route_parking_cost']) && isset($_POST['route_status'])) {
						
						//添加路线用数据生成
						$param_insert = array(
							'route_name' => $data['input_route_name'],
							'route_description' => $data['input_route_description'],
							'route_price_min' => $data['input_route_price_min'],
							'route_price_max' => $data['input_route_price_max'],
							'route_base_cost' => $data['input_route_base_cost'],
							'route_traffic_cost' => $data['input_route_traffic_cost'],
							'route_parking_cost' => $data['input_route_parking_cost'],
							'route_status' => $data['input_route_status'],
							'detail_list' => $data['input_detail_list'],
						);
						
						//输入内容检查
						$result_check = Model_Route::CheckInsertRoute($param_insert);
						
						if($result_check['result'] && $data['input_main_image'] && file_exists(DOCROOT . $data['input_main_image'])) {
							//数据添加
							$result_insert = Model_Route::InsertRoute($param_insert);
							$route_id = $result_insert[0];
							
							if($route_id) {
								$file_name_tmp = DOCROOT . $data['input_main_image'];
								
								//调整PC用图片尺寸
								$file_directory_pc = DOCROOT . 'assets/img/pc/upload/route/' . $route_id . '/';
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
								$file_directory_sp = DOCROOT . 'assets/img/sp/upload/route/' . $route_id . '/';
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
								header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/route_detail/' . $route_id . '/');
								exit;
							} else {
								$error_message_list[] = '数据库错误：数据添加失败';
							}
						} else {
							foreach($result_check['error'] as $insert_error) {
								switch($insert_error) {
									case 'empty_name':
										$error_message_list[] = '路线名不能为空';
										break;
									case 'long_name':
										$error_message_list[] = '路线名不能超过100字';
										break;
									case 'empty_description':
										$error_message_list[] = '路线简介不能为空';
										break;
									case 'nonum_price':
										$error_message_list[] = '价格必须为数字';
										break;
									case 'minus_price':
										$error_message_list[] = '价格不能为负';
										break;
									case 'reverse_price':
										$error_message_list[] = '底价不能高于顶价';
										break;
									case 'nonum_base_cost':
										$error_message_list[] = '基本成本必须为数字';
										break;
									case 'minus_base_cost':
										$error_message_list[] = '基本成本不能为负';
										break;
									case 'nonum_traffic_cost':
										$error_message_list[] = '交通费必须为数字';
										break;
									case 'minus_traffic_cost':
										$error_message_list[] = '交通费不能为负';
										break;
									case 'nonum_parking_cost':
										$error_message_list[] = '停车费必须为数字';
										break;
									case 'minus_parking_cost':
										$error_message_list[] = '停车费不能为负';
										break;
									case 'nobool_status':
										$error_message_list[] = '请选择公开状态';
										break;
									case 'noarray_detail':
										$error_message_list[] = '请至少添加一日的日程';
										break;
									case 'nonum_detail_day':
										$error_message_list[] = '第*天必须为数字';
										break;
									case 'minus_detail_day':
										$error_message_list[] = '第*天不能为负';
										break;
									case 'over_detail_day':
										$error_message_list[] = '第*天不能超过总天数';
										break;
									case 'duplication_detail_day':
										$error_message_list[] = '第*天不能与其他日程重复';
										break;
									case 'empty_detail_title':
										$error_message_list[] = '日程标题不能为空';
										break;
									case 'long_detail_title':
										$error_message_list[] = '日程标题不能超过100字';
										break;
									case 'empty_detail_content':
										$error_message_list[] = '日程简介不能为空';
										break;
									case 'noarray_spot_list':
										$error_message_list[] = '日程景点必须为序列';
										break;
									case 'noexist_spot_id':
										$error_message_list[] = '选中的景点必须已添加至本系统';
										break;
									default:
										break;
								}
							}
							if($data['input_main_image'] == '') {
								$error_message_list[] = '请上传主图';
							}
						}
					} else {
						$error_message_list[] = '系统错误：请勿修改表单中的控件名称';
					}
					
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
			return Response::forge(View::forge($this->template . '/admin/service/add_route', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}

	/**
	 * 获取景点列表
	 * @access  public
	 * @return  Response
	 */
	public function action_spotlist($page = null)
	{
		$result = '';
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['delete_id'], $_POST['page'])) {
			if(isset($_POST['page'])) {
				if($_POST['page'] == 'add_route') {
					$spot_list = Model_Spot::SelectSpotSimpleListActive();
					$result = json_encode($spot_list);
				}
			}
//		}
		return $result;
	}

}