<?php
/* 
 * 景点修改页
 */

class Controller_Admin_Service_Spot_Modifyspot extends Controller_Admin_App
{

	/**
	 * 景点修改页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($spot_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		try {
			if(!is_numeric($spot_id)) {
				//景点ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 6)) {
				//当前登陆用户不具备修改景点的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//获取原本景点信息
				$spot = Model_Spot::SelectSpot(array('spot_id' => $spot_id, 'active_only' => true));
				
				if(!$spot) {
					//不存在该ID的景点
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 2) && $spot['created_by'] != $_SESSION['login_user']['id']) {
					//该ID的景点为其他用户创建且当前登陆用户不具备编辑他人创建景点的权限
					return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
					exit;
				}
				
				//页面标题
				$data['page_title'] ='景点修改';
				//表单页面索引
				$data['form_page_index'] = 'modify_spot';
				//返回页URL
				$data['return_page_url'] = '/admin/spot_detail/' . $spot_id . '/';
				if(isset($_SERVER['HTTP_REFERER'])) {
					if(strstr($_SERVER['HTTP_REFERER'], 'admin/spot_list')) {
						$data['return_page_url'] = $_SERVER['HTTP_REFERER'];
					}
				}
				
				//获取地区列表
				$data['area_list'] = Model_Area::SelectAreaList(array('active_only' => true));
				//获取景点类型列表
				$data['spot_type_list'] = Model_SpotType::SelectSpotTypeList(array('active_only' => true));
				
				//form控件默认值设定
				$data['input_spot_name'] = $spot['spot_name'];
				$data['input_spot_area'] = $spot['spot_area'];
				$data['input_spot_type'] = $spot['spot_type'];
				$data['input_free_flag'] = $spot['free_flag'];
				$data['input_spot_price'] = $spot['spot_price'];
				$data['input_special_price_list'] = $spot['special_price_list'];
				$data['input_spot_status'] = $spot['spot_status'];
				$data['input_spot_detail_list'] = $spot['spot_detail_list'];
				
				//景点详情数据整理
				$max_detail_num = 0;
				foreach($spot['spot_detail_list'] as $detail_key => $detail_info) {
					//当前景点详情最大ID
					if(intval($detail_info['spot_detail_id']) > $max_detail_num) {
						$max_detail_num = intval($detail_info['spot_detail_id']);
					}
					
					//已上传景点图片列表
					$image_list = array();
					$max_image_id = 0;
					foreach($detail_info['image_list'] as $image_id) {
						$image_list[] = array(
							'image_id' => $image_id,
							'image_type' => 'uploaded',
							'image_name' => '/pc/upload/spot/' . $spot_id . '/' . $detail_info['spot_detail_id'] . '/' . $image_id . '_thumb.jpg',
						);
						if(intval($image_id) > $max_image_id) {
							$max_image_id = intval($image_id);
						}
					}
					
					$data['input_spot_detail_list'][$detail_key]['image_list'] = $image_list;
					$data['input_spot_detail_list'][$detail_key]['max_image_id'] = $max_image_id;
				}
				$data['max_detail_num'] = $max_detail_num;
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是景点信息修改页
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_spot_name'] = isset($_POST['spot_name']) ? trim($_POST['spot_name']) : $data['input_spot_name'];
						$data['input_spot_area'] = isset($_POST['spot_area']) ? $_POST['spot_area'] : $data['input_spot_area'];
						$data['input_spot_type'] = isset($_POST['spot_type']) ? $_POST['spot_type'] : $data['input_spot_type'];
						$data['input_free_flag'] = isset($_POST['free_flag']) ? $_POST['free_flag'] : $data['input_free_flag'];
						$data['input_spot_price'] = $data['input_free_flag'] == '1' ? '' : (isset($_POST['spot_price']) ? trim($_POST['spot_price']) : $data['input_spot_price']);
						$data['input_spot_status'] = isset($_POST['spot_status']) ? $_POST['spot_status'] : $data['input_spot_status'];
						
						//特别价格表控件当前值设定
						if($data['input_free_flag'] == '1') {
							$data['input_special_price_list'] = array();
						} else {
							if(isset($_POST['special_price_name']) && isset($_POST['special_price'])) {
								if(is_array($_POST['special_price_name']) && is_array($_POST['special_price'])) {
									if(count($_POST['special_price_name']) == count($_POST['special_price'])) {
										$special_price_list = array();
										foreach($_POST['special_price_name'] as $special_price_key => $special_price_name) {
											$special_price_list[] = array(
												'special_price_name' => $special_price_name,
												'special_price' => $_POST['special_price'][$special_price_key],
											);
										}
										$data['input_special_price_list'] = $special_price_list;
									}
								}
							}
						}
						
						//获取修改的景点详情数量
						$detail_num_list = array();
						$max_detail_num = 0;
						foreach($_POST as $key => $value) {
							if(preg_match('/^spot_detail_name_[0-9]+$/', $key)) {
								$detail_num = str_replace('spot_detail_name_', '', $key);
								$detail_num_list[] = $detail_num;
								if(intval($detail_num) > $max_detail_num) {
									$max_detail_num = intval($detail_num);
								}
							}
						}
						$data['max_detail_num'] = $max_detail_num;
						
						//景点详情区域控件当前值设定
						$data['input_spot_detail_list'] = array();
						//有效图片ID列表
						$image_id_active_list = array();
						foreach($detail_num_list as $detail_num) {
							//跨年FLAG
							$two_year_flag = 0;
							if(isset($_POST['two_year_flag_' . $detail_num])) {
								if($_POST['two_year_flag_' . $detail_num] == 'on') {
									$two_year_flag = 1;
								}
							}
							
							//景点图片处理
							$image_list = array();
							$image_id_active_list[$detail_num] = array();
							$max_image_id = 0;
							if(isset($_POST['image_id_list_' . $detail_num])) {
								if(is_array($_POST['image_id_list_' . $detail_num])) {
									foreach($_POST['image_id_list_' . $detail_num] as $image_id) {
										$image_key = $detail_num . '_' . $image_id;
										if(isset($_POST['image_type_' . $image_key])) {
											//图片处理类型判别
											switch($_POST['image_type_' . $image_key]) {
												case 'new':
													//本次上传图片处理
													if(isset($_FILES['image_file_' . $image_key])) {
														if(!$_FILES['image_file_' . $image_key]['error']) {
															$files_upload = $_FILES['image_file_' . $image_key];
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
																$file_directory_tmp = DOCROOT . 'assets/img/tmp/' . $_SESSION['login_user']['id'] . '/spot/';
																if(!file_exists($file_directory_tmp)) {
																	mkdir($file_directory_tmp, 0777, TRUE);
																}
																$file_name_tmp = $file_directory_tmp . $image_key . '.' . $extension;
																move_uploaded_file($files_upload['tmp_name'], $file_name_tmp);
																$image_list[] = array(
																	'image_id' => $image_id,
																	'image_type' => 'tmp',
																	'image_name' => '/tmp/' . $_SESSION['login_user']['id'] . '/spot/' . $image_key . '.' . $extension,
																);
																$image_id_active_list[$detail_num][] = $image_id;
															}
														}
													}
													break;
												case 'tmp':
													//往次上传图片处理
													if(isset($_POST['image_name_' . $image_key])) {
														$image_name = $_POST['image_name_' . $image_key];
														if(isset($_FILES['image_file_' . $image_key])) {
															if(!$_FILES['image_file_' . $image_key]['error']) {
																//图片重新上传
																$files_upload = $_FILES['image_file_' . $image_key];
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
																	$file_directory_tmp = DOCROOT . 'assets/img/tmp/' . $_SESSION['login_user']['id'] . '/spot/';
																	if(!file_exists($file_directory_tmp)) {
																		mkdir($file_directory_tmp, 0777, TRUE);
																	}
																	$file_name_tmp = $file_directory_tmp . $image_key . '.' . $extension;
																	move_uploaded_file($files_upload['tmp_name'], $file_name_tmp);
																	$image_name = '/tmp/' . $_SESSION['login_user']['id'] . '/spot/' . $image_key . '.' . $extension;
																}
															}
														}
														
														$image_list[] = array(
															'image_id' => $image_id,
															'image_type' => 'tmp',
															'image_name' => $image_name,
														);
														$image_id_active_list[$detail_num][] = $image_id;
													}
													break;
												case 'uploaded':
													//上传完成图片处理
													$image_list[] = array(
														'image_id' => $image_id,
														'image_type' => 'uploaded',
														'image_name' => '/pc/upload/spot/' . $spot_id . '/' . $detail_num . '/' . $image_id . '_thumb.jpg',
													);
													$image_id_active_list[$detail_num][] = $image_id;
													break;
											}
											if(intval($image_id) > $max_image_id) {
												$max_image_id = intval($image_id);
											}
										}
									}
								}
							}
							
							$data['input_spot_detail_list'][] = array(
								'spot_detail_id' => $detail_num,
								'spot_detail_name' => isset($_POST['spot_detail_name_' . $detail_num]) ? trim($_POST['spot_detail_name_' . $detail_num]) : '',
								'spot_description_text' => isset($_POST['spot_description_text_' . $detail_num]) ? $_POST['spot_description_text_' . $detail_num] : '',
								'image_list' => $image_list,
								'max_image_id' => $max_image_id,
								'two_year_flag' => $two_year_flag,
								'spot_start_month' => isset($_POST['spot_start_month_' . $detail_num]) ? $_POST['spot_start_month_' . $detail_num] : '',
								'spot_end_month' => isset($_POST['spot_end_month_' . $detail_num]) ? $_POST['spot_end_month_' . $detail_num] : '',
							);
						}
						
						//修改景点用数据生成
						$params_update = array(
							'spot_id' => $spot_id,
							'spot_name' => $data['input_spot_name'],
							'spot_area' => $data['input_spot_area'],
							'spot_type' => $data['input_spot_type'],
							'free_flag' => $data['input_free_flag'],
							'spot_price' => $data['input_spot_price'],
							'spot_status' => $data['input_spot_status'],
							'created_by' => $spot['created_by'],
							'modified_by' => $_SESSION['login_user']['id'],
							'special_price_list' => $data['input_special_price_list'],
							'spot_detail_list' => $data['input_spot_detail_list'],
						);
						
						//更新内容检查
						$result_check = Model_Spot::CheckEditSpot($params_update);
						
						if($result_check['result']) {
							//更新景点信息
							$result_update = Model_Spot::UpdateSpot($params_update);
							
							//对应终端列表
							$device_index_list = array('pc', 'sp');
							
							//被删除图片同步
							foreach($device_index_list as $device_index) {
								//获取景点详情图片文件夹列表
								$dir_spot = DOCROOT . 'assets/img/' . $device_index . '/upload/spot/' . $spot_id . '/';
								if(file_exists($dir_spot)) {
									$dir_detail_list = scandir($dir_spot);
									foreach($dir_detail_list as $dir_detail) {
										preg_match('/^\d{1,}/', $dir_detail, $matches, PREG_OFFSET_CAPTURE);
										if(count($matches) == 1) {
											$detail_num = $matches[0][0];
											$dir_image = DOCROOT . 'assets/img/' . $device_index . '/upload/spot/' . $spot_id . '/' . $detail_num . '/';
											
											if(in_array($detail_num, $detail_num_list)) {
												if(file_exists($dir_image)) {
													//获取图片文件列表
													$file_image_list = scandir($dir_image);
													foreach($file_image_list as $file_image) {
														preg_match('/^\d{1,}/', $file_image, $matches, PREG_OFFSET_CAPTURE);
														if(count($matches) == 1) {
															$image_id = $matches[0][0];
															if(!in_array($image_id, $image_id_active_list[$detail_num])) {
																//删除图片
																unlink($dir_image . $file_image);
															}
														}
													}
												}
												
											} else {
												//删除整个景点详情
												$file_image_list = scandir($dir_image);
												foreach($file_image_list as $file_image) {
													if($file_image != '.' && $file_image != '..') {
														unlink($dir_image . $file_image);
													}
												}
												rmdir(DOCROOT . 'assets/img/' . $device_index . '/upload/spot/' . $spot_id . '/' . $detail_num . '/');
											}
										}
									}
								}
							}
							
							if($result_update) {
								//将本次追加的图片临时文件转存至景点图片文件夹
								foreach($data['input_spot_detail_list'] as $spot_detail) {
									foreach($spot_detail['image_list'] as $image_info) {
										if($image_info['image_type'] == 'tmp') {
											$file_name_tmp = DOCROOT . 'assets/img/' . $image_info['image_name'];
											
											//调整PC用图片尺寸
											$file_directory_pc = DOCROOT . 'assets/img/pc/upload/spot/' . $spot_id . '/' . $spot_detail['spot_detail_id'] . '/';
											if(!file_exists($file_directory_pc)) {
												mkdir($file_directory_pc, 0777, TRUE);
											}
											$image_option_list_pc = Model_Imageoptimize::SelectImageOptionList(array('image_type' => 'spot_detail_image', 'image_device' => 'pc'));
											foreach($image_option_list_pc as $image_option_pc) {
												Model_Imageoptimize::ImageResizeToJpg($file_name_tmp, $image_option_pc['max_width'], $image_option_pc['max_height'], 
														$file_directory_pc . $image_info['image_id'] . '_' . $image_option_pc['image_option_slug'] . '.jpg');
											}
											
											//调整SP用图片尺寸
											$file_directory_sp = DOCROOT . 'assets/img/sp/upload/spot/' . $spot_id . '/' . $spot_detail['spot_detail_id'] . '/';
											if(!file_exists($file_directory_sp)) {
												mkdir($file_directory_sp, 0777, TRUE);
											}
											$image_option_list_sp = Model_Imageoptimize::SelectImageOptionList(array('image_type' => 'spot_detail_image', 'image_device' => 'sp'));
											foreach($image_option_list_sp as $image_option_sp) {
												Model_Imageoptimize::ImageResizeToJpg($file_name_tmp, $image_option_sp['max_width'], $image_option_sp['max_height'], 
														$file_directory_sp . $image_info['image_id'] . '_' . $image_option_sp['image_option_slug'] . '.jpg');
											}
											
											//删除图片临时文件
											unlink($file_name_tmp);
										}
									}
								}
								
								//更新成功 页面跳转
								$_SESSION['modify_spot_success'] = true;
								header('Location: //' . $_SERVER['HTTP_HOST'] . '/admin/spot_detail/' . $spot_id . '/');
								exit;
							} else {
								$error_message_list[] = '数据库错误：数据修改失败';
							}
						} else {
							//获取错误信息
							foreach($result_check['error'] as $update_error) {
								switch($update_error) {
									case 'empty_spot_name': 
										$error_message_list[] = '请输入景点名';
										break;
									case 'long_spot_name': 
										$error_message_list[] = '景点名不能超过100字';
										break;
									case 'dup_spot_name': 
										$error_message_list[] = '该景点名与其他景点重复,请选用其他景点名';
										break;
									case 'empty_spot_area': 
										$error_message_list[] = '请选择景点地区';
										break;
									case 'empty_spot_type': 
										$error_message_list[] = '请选择景点类别';
										break;
									case 'empty_special_price_name': 
										$error_message_list[] = '请输入价格条件';
										break;
									case 'long_special_price_name': 
										$error_message_list[] = '价格条件不能超过50字';
										break;
									case 'noint_spot_price': 
									case 'minus_spot_price': 
									case 'noint_special_price': 
									case 'minus_special_price': 
										$error_message_list[] = '请在价格部分输入非负整数';
										break;
									case 'empty_spot_detail': 
										$error_message_list[] = '请至少为景点添加一个景点详情';
										break;
									case 'empty_spot_detail_name': 
										$error_message_list[] = '请输入景点详情名';
										break;
									case 'long_spot_detail_name': 
										$error_message_list[] = '景点详情名不能超过100字';
										break;
									case 'empty_spot_description_text': 
										$error_message_list[] = '请输入景点介绍';
										break;
									case 'overyear_spot_se_month': 
										$error_message_list[] = '请在一年周期内选择景点详情公开期';
										break;
									case 'minus_spot_se_month': 
										$error_message_list[] = '请将详情公开期结束月设定在开始月之后';
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
				return Response::forge(View::forge($this->template . '/admin/service/spot/edit_spot', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}
	
	/**
	 * 景点公开状态更新
	 * @access  public
	 * @return  Response
	 */
	public function action_modifyspotstatus($param = null)
	{
		try {
			if(isset($_POST['page'], $_POST['modify_id'], $_POST['modify_value'])) {
				if(is_numeric($_POST['modify_id']) && $_POST['page'] == 'spot_detail') {
					$spot_id = $_POST['modify_id'];
					
					//获取景点信息
					$spot = Model_Spot::SelectSpot(array('spot_id' => $spot_id, 'active_only' => true));
					
					if($spot) {
						if($spot['created_by'] == $_SESSION['login_user']['id']) {
							//是否具备景点编辑权限
							$edit_able_flag = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 6);
						} else {
							//是否具备修改其他用户所登陆的景点信息权限
							$edit_able_flag = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 2);
						}
						
						if($edit_able_flag) {
							$params_update = array(
								'spot_id' => $spot_id,
								'spot_status' => $_POST['modify_value'],
								'modified_by' => $_SESSION['login_user']['id'],
							);
							
							$result_check = Model_Spot::CheckUpdateSpotStatus($params_update);
							
							if($result_check['result']) {
								//数据更新
								$result_update = Model_Spot::UpdateSpotStatus($params_update);
								
								if($result_update) {
									//更新成功
									$_SESSION['modify_spot_status_success'] = true;
									header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/spot_detail/' . $_POST['modify_id'] . '/');
									exit;
								}
							}
						}
					}
				}
			}
			
			//更新失敗
			$_SESSION['modify_spot_status_error'] = true;
			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/spot_detail/' . $_POST['modify_id'] . '/');
			exit;
		} catch (Exception $e) {
			//发生系统异常
			$_SESSION['modify_spot_status_error'] = true;
			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/spot_detail/' . $_POST['modify_id'] . '/');
			exit;
		}
	}

}