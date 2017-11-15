<?php
/* 
 * 添加景点页
 */

class Controller_Admin_Service_Addspot extends Controller_Admin_App
{

	/**
	 * 添加景点页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			//设定View所需变量
			$data['error_message'] = '';
			$data['area_list'] = Model_Area::GetAreaListAll();
			$data['spot_type_list'] = Model_SpotType::GetSpotTypeListAll();
			$data['input_spot_name'] = '';
			$data['input_spot_area'] = '';
			$data['input_spot_type'] = '';
			$data['input_free_flag'] = '';
			$data['input_spot_price'] = '';
			$data['input_spot_status'] = '';
			$data['input_detail_list'] = array();
			
			if(isset($_POST['page'])) {
				$error_message_list = array();
				$file_tmp_list = array();
				
				//数据来源检验
				if($_POST['page'] == 'add_spot') {
					//获取添加的景点详情数量
					$detail_num_list = array();
					foreach($_POST as $key => $value) {
						if(preg_match('/^spot_detail_name_[0-9]+$/', $key)) {
							$detail_num_list[] = str_replace('spot_detail_name_', '', $key);
						}
					}
					
					if(isset($_POST['spot_name']) && isset($_POST['spot_area']) && isset($_POST['spot_type']) 
							&& isset($_POST['free_flag']) && isset($_POST['spot_price']) && isset($_POST['spot_status'])) {
						//上传图片暂时保存
						foreach($detail_num_list as $detail_num) {
							$file_tmp_count = 0;
							$file_tmp_list[$detail_num] = array();
							
							//截至本次发送表单为止上传的图片
							if(isset($_POST['spot_image_tmp_' . $detail_num])) {
								if(is_array($_POST['spot_image_tmp_' . $detail_num])) {
									for($i = 0; $i < count($_POST['spot_image_tmp_' . $detail_num]); $i++) {
										$file_tmp_list[$detail_num][] = $_POST['spot_image_tmp_' . $detail_num][$i];
										$file_tmp_count++;
									}
								}
							}
							
							//本次上传图片处理
							if(isset($_FILES['spot_images_' . $detail_num])) {
								$files_upload = $_FILES['spot_images_' . $detail_num];
								for($i = 0; $i < count($files_upload['type']); $i++) {
									//图片暂时保存
									$extension = '';
									switch($files_upload['type'][$i]) {
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
										$file_directory_tmp = DOCROOT . 'assets/img/tmp/' . $_SESSION['login_user']['id'] . '/spot/';
										if(!file_exists($file_directory_tmp)) {
											mkdir($file_directory_tmp, 0777, TRUE);
										}
										$file_name_tmp = $file_directory_tmp . $detail_num . '_' . ($i + $file_tmp_count) . '.' . $extension;
										move_uploaded_file($files_upload["tmp_name"][$i], $file_name_tmp);
										$file_tmp_list[$detail_num][] = $detail_num . '_' . ($i + $file_tmp_count) . '.' . $extension;
									}
								}
							}
						}
						
						//添加景点用数据生成
						$param_insert_spot = array(
							'spot_name' => $_POST['spot_name'],
							'spot_area' => $_POST['spot_area'],
							'spot_type' => $_POST['spot_type'],
							'free_flag' => $_POST['free_flag'],
							'spot_price' => $_POST['free_flag'] == '1' ? 0 : $_POST['spot_price'],
							'spot_status' => $_POST['spot_status'],
							'detail_list' => array(),
						);
						
						//添加景点详情用数据生成
						foreach($detail_num_list as $detail_num) {
							if(isset($_POST['spot_detail_name_' . $detail_num]) && isset($_POST['spot_description_text_' . $detail_num]) 
									&& isset($_POST['spot_start_month_' . $detail_num]) && isset($_POST['spot_end_month_' . $detail_num])) {
								$two_year_flag = 0;
								if(isset($_POST['two_year_flag_' . $detail_num])) {
									if($_POST['two_year_flag_' . $detail_num] == 'on') {
										$two_year_flag = 1;
									}
								}
								$param_insert_spot['detail_list'][] = array(
									'spot_sort_id' => intval($detail_num),
									'spot_detail_name' => $_POST['spot_detail_name_' . $detail_num],
									'spot_description_text' => $_POST['spot_description_text_' . $detail_num],
									'image_number' => count($file_tmp_list[$detail_num]),
									'two_year_flag' => $two_year_flag,
									'spot_start_month' => $_POST['spot_start_month_' . $detail_num],
									'spot_end_month' => $_POST['spot_end_month_' . $detail_num],
								);
							}
						}
						
						//输入内容检查
						$result_check = Model_Spot::CheckInsertSpot($param_insert_spot);
						
						if($result_check['result']) {
							//数据添加
							$result_insert = Model_Spot::InsertSpot($param_insert_spot);
							$spot_id = $result_insert[0];
							
							if($result_insert) {
								
								//将图片临时文件转存至景点图片文件夹
								foreach($detail_num_list as $detail_num) {
									foreach($file_tmp_list[$detail_num] as $key_tmp => $file_tmp) {
										$file_name_tmp = DOCROOT . 'assets/img/tmp/' . $_SESSION['login_user']['id'] . '/spot/' . $file_tmp;
										
										//调整PC用图片尺寸
										$file_directory_pc = DOCROOT . 'assets/img/pc/upload/spot/' . $spot_id . '/' . $detail_num . '/';
										if(!file_exists($file_directory_pc)) {
											mkdir($file_directory_pc, 0777, TRUE);
										}
										$image_option_list_pc = Model_Imageoptimize::SelectImageOptionList(array('image_type' => 'spot_detail_image', 'image_device' => 'pc'));
										foreach($image_option_list_pc as $image_option_pc) {
											Model_Imageoptimize::ImageResizeToJpg($file_name_tmp, $image_option_pc['max_width'], $image_option_pc['max_height'], 
													$file_directory_pc . $key_tmp . '_' . $image_option_pc['image_option_slug'] . '.jpg');
										}
										
										//调整SP用图片尺寸
										$file_directory_sp = DOCROOT . 'assets/img/sp/upload/spot/' . $spot_id . '/' . $detail_num . '/';
										if(!file_exists($file_directory_sp)) {
											mkdir($file_directory_sp, 0777, TRUE);
										}
										$image_option_list_sp = Model_Imageoptimize::SelectImageOptionList(array('image_type' => 'spot_detail_image', 'image_device' => 'sp'));
										foreach($image_option_list_sp as $image_option_sp) {
											Model_Imageoptimize::ImageResizeToJpg($file_name_tmp, $image_option_sp['max_width'], $image_option_sp['max_height'], 
													$file_directory_sp . $key_tmp . '_' . $image_option_sp['image_option_slug'] . '.jpg');
										}
										
										//删除图片临时文件
										unlink($file_name_tmp);
									}
								}
								
								//添加成功 页面跳转
								$_SESSION['add_spot_success'] = true;
								header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/spot_detail/' . $spot_id . '/');
								exit;
							} else {
								$error_message_list[] = '数据库错误：数据添加失败';
							}
						} else {
							foreach($result_check['error'] as $insert_error) {
								switch($insert_error) {
									case 'empty_name':
										$error_message_list[] = '景点名不能为空';
										break;
									case 'nonum_price':
										$error_message_list[] = '收费景点的票价不能为空';
										break;
									case 'empty_detail_name':
										$error_message_list[] = '景点详情名不能为空';
										break;
									case 'empty_description_text':
										$error_message_list[] = '景点介绍不能为空';
										break;
									case 'nonum_area':
										$error_message_list[] = '请选择景点所属地区';
										break;
									case 'nonum_type':
										$error_message_list[] = '请选择景点类型';
										break;
									case 'nobool_status':
										$error_message_list[] = '请选择公开状态';
										break;
									case 'nobool_freeflag':
										$error_message_list[] = '请选择收费/免费';
										break;
									case 'nonum_se_time':
									case 'noexist_se_time':
										$error_message_list[] = '请选择详情公开期';
										break;
									case 'minus_price':
										$error_message_list[] = '票价不能为负';
										break;
									case 'noarray_detail':
										$error_message_list[] = '请至少为景点添加一个景点详情';
										break;
									case 'zero_image':
										$error_message_list[] = '请至少为景点详情添加一张图片';
										break;
									case 'overyear_se_time':
										$error_message_list[] = '在跨年情况下开始月份需要比结束月份的数字大';
										break;
									case 'minus_se_time':
										$error_message_list[] = '在不跨年情况下开始月份不能比结束月份的数字大';
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
					$data['input_spot_name'] = isset($_POST['spot_name']) ? $_POST['spot_name'] : '';
					$data['input_spot_area'] = isset($_POST['spot_area']) ? $_POST['spot_area'] : '';
					$data['input_spot_type'] = isset($_POST['spot_type']) ? $_POST['spot_type'] : '';
					$data['input_free_flag'] = isset($_POST['free_flag']) ? $_POST['free_flag'] : '';
					$data['input_spot_status'] = isset($_POST['spot_status']) ? $_POST['spot_status'] : '';
					$spot_price = isset($_POST['spot_price']) ? $_POST['spot_price'] : '';
					$data['input_spot_price'] = isset($_POST['free_flag']) ? ($_POST['free_flag'] == '1' ? '' : $spot_price) : $spot_price;
					//反映景点详情
					foreach($detail_num_list as $detail_num) {
						$two_year_flag = 0;
						if(isset($_POST['two_year_flag_' . $detail_num])) {
							if($_POST['two_year_flag_' . $detail_num] == 'on') {
								$two_year_flag = 1;
							}
						}
						$image_number = 0;
						if(isset($_FILES['spot_images_' . $detail_num]['type'])) {
							if(is_array($_FILES['spot_images_' . $detail_num]['type'])) {
								foreach($_FILES['spot_images_' . $detail_num]['type'] as $type) {
									if(in_array($type, array('image/jpeg', 'image/png'))) {
										$image_number++;
									}
								}
							}
						}
						$data['input_detail_list'][] = array(
							'spot_sort_id' => $detail_num,
							'spot_detail_name' => isset($_POST['spot_detail_name_' . $detail_num]) ? $_POST['spot_detail_name_' . $detail_num] : '',
							'spot_description_text' => isset($_POST['spot_description_text_' . $detail_num]) ? $_POST['spot_description_text_' . $detail_num] : '',
							'image_list' => $file_tmp_list[$detail_num],
							'two_year_flag' => $two_year_flag,
							'spot_start_month' => isset($_POST['spot_start_month_' . $detail_num]) ? $_POST['spot_start_month_' . $detail_num] : '',
							'spot_end_month' => isset($_POST['spot_end_month_' . $detail_num]) ? $_POST['spot_end_month_' . $detail_num] : '',
						);
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
			return Response::forge(View::forge($this->template . '/admin/service/add_spot', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}

}