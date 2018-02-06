<?php
/* 
 * 批量导入景点
 */

class Controller_Admin_Service_Spot_Importspot extends Controller_Admin_App
{

	/**
	 * 批量导入景点
	 * @access  public
	 * @return  Response
	 */
	public function action_index($page = 1)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/spot_list/';
		
		try {
			if(!isset($_POST['page'])) {
				//未指明来源页
				$_SESSION['import_spot_error'] = 'error_system';
			} else {
				if(!isset($_FILES['file_spot_list']['name'])) {
					//未上传任何文件
					$_SESSION['import_spot_error'] = 'noexist_file';
				} elseif($_FILES['file_spot_list']['type'] != 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' && $_FILES['file_spot_list']['type'] != 'application/vnd.ms-excel') {
					//上传的文件不是Excel
					$_SESSION['import_spot_error'] = 'noexcel_file';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 8)) {
					//当前登陆用户不具备批量导入景点的权限
					$_SESSION['import_spot_error'] = 'error_permission';
				} else {
					//Excel处理用组件
					include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel.php');
					include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
					
					//景点临时ID列表
					$spot_tmpid_list = array();
					//异常列表
					$error_list_spot = array();
					$error_list_detail = array();
					//删除行列表
					$row_list_remove_spot = array();
					$row_list_remove_detail = array();
					//景点数据列表
					$spot_list = array();
					//当前登陆用户ID
					$login_user_id = $_SESSION['login_user']['id'];
					
					//地区列表
					$area_list = Model_Area::GetAreaList(array('active_only' => true));
					foreach($area_list as $area) {
						$area_master_list[$area['area_name']] = $area['area_id'];
					}
					
					//景点类型列表
					$spot_type_list = Model_SpotType::SelectSpotTypeList(array('active_only' => true));
					foreach($spot_type_list as $spot_type) {
						$type_master_list[$spot_type['spot_type_name']] = $spot_type['spot_type_id'];
					}
					
					//收费/免费
					$free_flag_master_list = array(
						'免费' => 1,
						'收费' => 0,
					);
					
					//文件拓展名获取
					preg_match('/[^.]+$/', $_FILES['file_spot_list']['name'], $tmp);
					$extension = $tmp[0];
					//临时文件位置
					$filepath = $_FILES['file_spot_list']['tmp_name'];
					
					//文件内容获取
					if($extension == 'xls'){
						$xls_upload = PHPExcel_IOFactory::load($filepath);
					}else{
						$xls_upload = PHPExcel_IOFactory::createReader('Excel2007')->load($filepath);
					}
					
					if(!$xls_upload->sheetNameExists('景点') || !$xls_upload->sheetNameExists('景点详情')) {
						//必要的sheet不存在
						$_SESSION['import_spot_error'] = 'noexist_sheet';
					} else {
						//添加数据整理
						//景点Sheet
						$sheet_spot = $xls_upload->getSheetByName('景点');
						//景点详情Sheet
						$sheet_detail = $xls_upload->getSheetByName('景点详情');
						
						//逐行获取数据
						foreach($sheet_spot->getRowIterator() as $row_id => $row) {
							//跳过表头与范例,及景点名为空的行
							if($row_id > 2 && $sheet_spot->getCell('A' . $row_id)->getValue()) {
								$spot_info = array();
								//逐个单元格获取数据
								foreach($row->getCellIterator() as $cell_id => $cell_date) {
									switch($cell_id) {
										case 'A':
											//景点名
											$spot_name = strval($cell_date->getCalculatedValue());
											$spot_info['spot_name'] = $spot_name;
											if(!isset($spot_tmpid_list[$spot_name])) {
												$spot_tmpid_list[$spot_name] = $row_id;
											}
											break;
										case 'B':
											//景点地区
											$spot_area_name = $cell_date->getCalculatedValue();
											if(!$spot_area_name) {
												$spot_info['spot_area'] = '';
											} elseif(!isset($area_master_list[$spot_area_name])) {
												$spot_info['spot_area'] = -1;
											} else {
												$spot_info['spot_area'] = $area_master_list[$spot_area_name];
											}
											break;
										case 'C':
											//景点类别
											$spot_type_name = $cell_date->getCalculatedValue();
											if(!$spot_type_name) {
												$spot_info['spot_type'] = '';
											} elseif(!isset($type_master_list[$spot_type_name])) {
												$spot_info['spot_type'] = -1;
											} else {
												$spot_info['spot_type'] = $type_master_list[$spot_type_name];
											}
											break;
										case 'D':
											//免/收费
											$spot_free_flag = $cell_date->getCalculatedValue();
											if(!$spot_free_flag) {
												$spot_info['free_flag'] = '';
											} elseif(!isset($free_flag_master_list[$spot_free_flag])) {
												$spot_info['free_flag'] = -1;
											} else {
												$spot_info['free_flag'] = $free_flag_master_list[$spot_free_flag];
											}
											break;
										case 'E':
											//一般票价
											$spot_info['spot_price'] = strval($cell_date->getCalculatedValue());
											break;
										case 'F':
											//特殊价格
											$special_price_text = $cell_date->getCalculatedValue();
											$special_price_list = array();
											if($special_price_text) {
												$special_price_sub_text_list = explode(';', $special_price_text);
												foreach($special_price_sub_text_list as $special_price_sub_text) {
													$special_price = explode(':', $special_price_sub_text);
													if(count($special_price)==2) {
														$special_price_list[] = array(
															'special_price_name' => $special_price[0],
															'special_price' => $special_price[1],
														);
													} else {
														$error_list_spot[$row_id][] = '特殊价格部分不符合要求,请按照范例格式重新填写';
													}
												}
											}
											$spot_info['special_price_list'] = $special_price_list;
											break;
									}
								}
								$spot_list[$row_id] = array(
									'spot_id' => '',
									'spot_name' => $spot_info['spot_name'],
									'spot_area' => $spot_info['spot_area'],
									'spot_type' => $spot_info['spot_type'],
									'free_flag' => $spot_info['free_flag'],
									'spot_price' => $spot_info['spot_price'],
									'spot_status' => 0,
									'created_by' => $login_user_id,
									'modified_by' => $login_user_id,
									'special_price_list' => $spot_info['special_price_list'],
									'spot_detail_list' => array(),
								);
							}
						}
						
						if(!count($spot_tmpid_list)) {
							//上传的文件中未设定任何景点名
							$_SESSION['import_spot_error'] = 'empty_spot_name';
							//释放缓存
							$xls_upload->disconnectWorksheets();
							unset($sheet_spot);
							unset($sheet_detail);
							unset($xls_upload);
						} else {
							//逐行获取数据
							foreach ($sheet_detail->getRowIterator() as $row_id => $row) {
								//跳过表头与范例
								if($row_id > 2 && $sheet_detail->getCell('A' . $row_id)->getValue()) {
									$spot_detail = array();
									//逐个单元格获取数据
									foreach ($row->getCellIterator() as $cell_id => $cell_date) {
										switch($cell_id) {
											case 'A':
												//景点名
												$spot_name = strval($cell_date->getCalculatedValue());
												if(isset($spot_tmpid_list[$spot_name])) {
													$spot_id = $spot_tmpid_list[$spot_name];
													$detail_num = 0;
													if(isset($spot_list[$spot_id]['spot_detail_list'])) {
														$detail_num = count($spot_list[$spot_id]['spot_detail_list']);
													}
													$spot_detail['spot_detail_id'] = $detail_num + 1;
												} else {
													$error_list_detail[$row_id][] = '不属于本次添加的景点,请确认景点名是否正确';
												}
												break;
											case 'B':
												//景点详情名
												$spot_detail['spot_detail_name'] = strval($cell_date->getCalculatedValue());
												break;
											case 'C':
												//景点描述
												$spot_detail['spot_description_text'] = strval($cell_date->getCalculatedValue());
												break;
											case 'D':
												//开始
												$spot_detail['spot_start_month'] = str_replace('月', '', $cell_date->getCalculatedValue());
												break;
											case 'E':
												//结束
												$cell_value = $cell_date->getCalculatedValue();
												if(strstr($cell_value, '次年')) {
													$cell_value = str_replace('次年', '', $cell_value);
													$spot_detail['two_year_flag'] = 1;
													$spot_detail['spot_end_month'] = str_replace('月', '', $cell_value);
												} else {
													$spot_detail['two_year_flag'] = 0;
													$spot_detail['spot_end_month'] = str_replace('月', '', $cell_value);
												}
												break;
											default:
												break;
										}
									}
									if(isset($spot_list[$spot_id])) {
										$spot_list[$spot_id]['spot_detail_list'][] = array(
											'row_id' => $row_id,
											'spot_detail_id' => $spot_detail['spot_detail_id'],
											'spot_detail_name' => $spot_detail['spot_detail_name'],
											'spot_description_text' => $spot_detail['spot_description_text'],
											'image_list' => array(),
											'max_image_id' => 0,
											'two_year_flag' => $spot_detail['two_year_flag'],
											'spot_start_month' => $spot_detail['spot_start_month'],
											'spot_end_month' => $spot_detail['spot_end_month'],
										);
									}
								}
							}
							
							//添加景点
							if(count($spot_list)) {
								foreach($spot_list as $row_id => $params_insert) {
									//输入内容检查
									$result_check = Model_Spot::CheckEditSpot($params_insert);
									
									if($result_check['result']) {
										//添加数据
										$result_insert = Model_Spot::InsertSpot($params_insert);
										
										if(!$result_insert) {
											//添加失败
											$error_list_spot[$row_id][] = '发生数据库错误,请重新尝试添加';
										} else {
											$row_list_remove_spot[] = $row_id;
											foreach($spot_list[$row_id]['spot_detail_list'] as $detail) {
												$row_list_remove_detail[] = $detail['row_id'];
											}
										}
									} else {
										//获取错误信息
										foreach($result_check['error'] as $error) {
											switch($error) {
												case 'empty_spot_name': 
													$error_list_spot[$row_id][] = '景点名不能空白,请输入景点名';
													break;
												case 'long_spot_name': 
													$error_list_spot[$row_id][] = '景点名不能超过100字,请调整景点名';
													break;
												case 'dup_spot_name': 
													$error_list_spot[$row_id][] = '该景点名与系统中其他景点重复,请选用其他景点名';
													break;
												case 'empty_spot_area': 
													$error_list_spot[$row_id][] = '景点地区不能空白,请选择景点地区';
													break;
												case 'error_spot_area': 
													$error_list_spot[$row_id][] = '所选中的景点地区不存在,请下载最新的模板';
													break;
												case 'empty_spot_type': 
													$error_list_spot[$row_id][] = '景点类别不能空白,请选择景点类别';
													break;
												case 'error_spot_type': 
													$error_list_spot[$row_id][] = '所选中的景点类别不存在,请下载最新的模板';
													break;
												case 'empty_spot_price': 
													$error_list_spot[$row_id][] = '价格不能空白,请一个非负整数';
													break;
												case 'noint_spot_price': 
												case 'minus_spot_price': 
													$error_list_spot[$row_id][] = '价格部分不符合要求,请输入一个非负整数';
													break;
												case 'empty_special_price_name': 
													$error_list_spot[$row_id][] = '特殊价格的价格条件不能为空,请输入价格条件';
													break;
												case 'long_special_price_name': 
													$error_list_spot[$row_id][] = '价格条件不能超过50字,请调整价格条件';
													break;
												case 'noint_special_price': 
												case 'minus_special_price': 
													$error_list_spot[$row_id][] = '特殊价格部分不符合要求,请输入非负整数';
													break;
												case 'empty_spot_detail': 
													$error_list_spot[$row_id][] = '景点详情表中没有相应的详情信息,请至少为景点添加一个详情';
													break;
												case 'empty_spot_detail_name': 
													$error_list_spot[$row_id][] = '景点详情表中景点详情名不能空白,请输入景点详情名';
													break;
												case 'long_spot_detail_name': 
													$error_list_spot[$row_id][] = '景点详情表中景点详情名不能超过100字,请调整景点详情名';
													break;
												case 'empty_spot_description_text': 
													$error_list_spot[$row_id][] = '景点详情表中景点介绍不能空白,请输入景点介绍';
													break;
												case 'overyear_spot_se_month': 
													$error_list_spot[$row_id][] = '景点详情表中开始与结束的时间间隔不能超过1年';
													break;
												case 'minus_spot_se_month': 
													$error_list_spot[$row_id][] = '景点详情表中结束不能在开始之前';
													break;
												default:
													$error_list_spot[$row_id][] = '发生系统错误,请重新尝试添加';
													break;
											}
										}
									}
								}
							}
							
							if(!count($error_list_spot) && !count($error_list_detail)) {
								//全部成功添加
								$_SESSION['import_spot_success'] = true;
								//释放缓存
								$xls_upload->disconnectWorksheets();
								unset($sheet_spot);
								unset($sheet_detail);
								unset($xls_upload);
							} else {
								//设置修改项目列宽度
								$sheet_spot->getColumnDimension('G')->setWidth( 80 );
								$sheet_detail->getColumnDimension('F')->setWidth( 80 );
								//设置修改项目列表头
								$sheet_spot->setCellValue('G1', '修改项目');
								$sheet_detail->setCellValue('F1', '修改项目');
								
								//在Excel中加入异常报告
								foreach($error_list_spot as $row_id => $error_list) {
									$sheet_spot->setCellValue('G' . $row_id, implode(PHP_EOL, array_unique($error_list)));
									$sheet_spot->getStyle('G' . $row_id)->getAlignment()->setWrapText(true);
								}
								foreach($error_list_detail as $row_id => $error_list) {
									$sheet_detail->setCellValue('F' . $row_id, implode(PHP_EOL, array_unique($error_list)));
									$sheet_detail->getStyle('F' . $row_id)->getAlignment()->setWrapText(true);
								}
								
								//删除没有问题的行
								if(count($row_list_remove_spot)) {
									$row_list_remove_spot = array_unique($row_list_remove_spot);
									rsort($row_list_remove_spot);
									foreach($row_list_remove_spot as $row_id) {
										$sheet_spot->removeRow($row_id, 1);
									}
								}
								if(count($row_list_remove_detail)) {
									$row_list_remove_detail = array_unique($row_list_remove_detail);
									rsort($row_list_remove_detail);
									foreach($row_list_remove_detail as $row_id) {
										$sheet_detail->removeRow($row_id, 1);
									}
								}
								
								//保存为下载用临时文件
								$writer_xls = PHPExcel_IOFactory::createWriter($xls_upload, 'Excel2007');
								$file_directory_tmp = DOCROOT . 'assets/xls/tmp/' . $_SESSION['login_user']['id'] . '/spot/';
								if(!file_exists($file_directory_tmp)) {
									mkdir($file_directory_tmp, 0777, TRUE);
								}
								$writer_xls->save($file_directory_tmp . 'import_spot_error.xls');
								
								//部分景点未能成功导入
								$_SESSION['import_spot_error'] = 'error_import';
								
								//释放缓存
								$xls_upload->disconnectWorksheets();
								unset($writer_xls);
								unset($sheet_spot);
								unset($sheet_detail);
								unset($xls_upload);
							}
						}
					}
				}
				
				//页面返回目标
				switch($_POST['page']) {
					case 'spot_list':
						if(isset($_SERVER['HTTP_REFERER'])) {
							if(strstr($_SERVER['HTTP_REFERER'], 'admin/spot_list')) {
								$header_url = $_SERVER['HTTP_REFERER'];
							}
						}
						break;
					default:
						break;
				}
			}
		} catch (Exception $e) {
			//发生系统异常
			$_SESSION['import_spot_error'] = 'error_system';
		}
		
		header('Location: ' . $header_url);
		exit;
	}

}