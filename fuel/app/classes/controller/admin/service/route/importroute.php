<?php
/* 
 * 批量导入旅游路线
 */

class Controller_Admin_Service_Route_Importroute extends Controller_Admin_App
{

	/**
	 * 批量导入旅游路线
	 * @access  public
	 * @return  Response
	 */
	public function action_index($page = 1)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/route_list/';
		
		try {
			if(!isset($_POST['page'])) {
				//未指明来源页
				$_SESSION['import_route_error'] = 'error_system';
			} else {
				if(!isset($_FILES['file_route_list']['name'])) {
					//未上传任何文件
					$_SESSION['import_route_error'] = 'noexist_file';
				} elseif($_FILES['file_route_list']['type'] != 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' && $_FILES['file_route_list']['type'] != 'application/vnd.ms-excel') {
					//上传的文件不是Excel
					$_SESSION['import_route_error'] = 'noexcel_file';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 8)) {
					//当前登陆用户不具备批量导入旅游路线的权限
					$_SESSION['import_route_error'] = 'error_permission';
				} else {
					//Excel处理用组件
					include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel.php');
					include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
					
					//旅游路线临时ID列表
					$route_tmpid_list = array();
					//异常列表
					$error_list_route = array();
					$error_list_detail = array();
					//删除行列表
					$row_list_remove_route = array();
					$row_list_remove_detail = array();
					//旅游路线数据列表
					$route_list = array();
					//当前登陆用户ID
					$login_user_id = $_SESSION['login_user']['id'];
					
					//景点列表
					$spot_master_list = array();
					$spot_list = Model_Spot::SelectSpotSimpleList(array('spot_status' => array(1), 'active_only' => true));
					foreach($spot_list as $spot) {
						$spot_master_list[$spot['spot_name']] = $spot['spot_id'];
					}
					
					//文件拓展名获取
					preg_match('/[^.]+$/', $_FILES['file_route_list']['name'], $tmp);
					$extension = $tmp[0];
					//临时文件位置
					$filepath = $_FILES['file_route_list']['tmp_name'];
					
					//文件内容获取
					if($extension == 'xls'){
						$xls_upload = PHPExcel_IOFactory::load($filepath);
					}else{
						$xls_upload = PHPExcel_IOFactory::createReader('Excel2007')->load($filepath);
					}
					
					if(!$xls_upload->sheetNameExists('旅游路线') || !$xls_upload->sheetNameExists('详细日程')) {
						//必要的sheet不存在
						$_SESSION['import_route_error'] = 'noexist_sheet';
					} else {
						//添加数据整理
						//旅游路线Sheet
						$sheet_route = $xls_upload->getSheetByName('旅游路线');
						//详细日程Sheet
						$sheet_detail = $xls_upload->getSheetByName('详细日程');
						
						//逐行获取数据
						foreach($sheet_route->getRowIterator() as $row_id => $row) {
							//跳过表头与范例,及旅游路线名为空的行
							if($row_id > 2 && $sheet_route->getCell('A' . $row_id)->getValue()) {
								$route_info = array();
								//逐个单元格获取数据
								foreach($row->getCellIterator() as $cell_id => $cell_date) {
									switch($cell_id) {
										case 'A':
											//旅游路线名
											$route_name = strval($cell_date->getCalculatedValue());
											$route_info['route_name'] = $route_name;
											if(!isset($route_tmpid_list[$route_name])) {
												$route_tmpid_list[$route_name] = $row_id;
											}
											break;
										case 'B':
											//旅游路线简介
											$route_info['route_description'] = strval($cell_date->getCalculatedValue());
											break;
										case 'C':
											//最低价
											$route_info['route_price_min'] = strval($cell_date->getCalculatedValue());
											break;
										case 'D':
											//最高价
											$route_info['route_price_max'] = strval($cell_date->getCalculatedValue());
											break;
										case 'E':
											//基础成本
											$route_info['route_base_cost'] = strval($cell_date->getCalculatedValue());
											break;
										case 'F':
											//停车费
											$route_info['route_traffic_cost'] = strval($cell_date->getCalculatedValue());
											break;
										case 'G':
											//交通费
											$route_info['route_parking_cost'] = strval($cell_date->getCalculatedValue());
											break;
									}
								}
								$route_list[$row_id] = array(
									'route_id' => '',
									'route_name' => $route_info['route_name'],
									'route_description' => $route_info['route_description'],
									'route_price_min' => $route_info['route_price_min'],
									'route_price_max' => $route_info['route_price_max'],
									'route_base_cost' => $route_info['route_base_cost'],
									'route_traffic_cost' => $route_info['route_traffic_cost'],
									'route_parking_cost' => $route_info['route_parking_cost'],
									'route_status' => 0,
									'created_by' => $login_user_id,
									'modified_by' => $login_user_id,
									'detail_list' => array(),
								);
							}
						}
						
						if(!count($route_tmpid_list)) {
							//上传的文件中未设定任何旅游路线名
							$_SESSION['import_route_error'] = 'empty_route_name';
							//释放缓存
							$xls_upload->disconnectWorksheets();
							unset($sheet_route);
							unset($sheet_detail);
							unset($xls_upload);
						} else {
							//逐行获取数据
							foreach ($sheet_detail->getRowIterator() as $row_id => $row) {
								//跳过表头与范例
								if($row_id > 2 && $sheet_detail->getCell('A' . $row_id)->getValue()) {
									$route_detail = array();
									//逐个单元格获取数据
									foreach ($row->getCellIterator() as $cell_id => $cell_date) {
										switch($cell_id) {
											case 'A':
												//旅游路线名
												$route_name = strval($cell_date->getCalculatedValue());
												if($route_name) {
													if(isset($route_tmpid_list[$route_name])) {
														$route_id = $route_tmpid_list[$route_name];
													} else {
														$error_list_detail[$row_id][] = '不属于本次添加的景点,请确认景点名是否正确';
													}
												}
												break;
											case 'B':
												//第几天
												$route_detail['route_detail_day'] = strval($cell_date->getCalculatedValue());
												break;
											case 'C':
												//标题
												$route_detail['route_detail_title'] = strval($cell_date->getCalculatedValue());
												break;
											case 'D':
												//简介
												$route_detail['route_detail_content'] = strval($cell_date->getCalculatedValue());
												break;
											case 'E':
												//早餐
												$route_detail['route_breakfast'] = strval($cell_date->getCalculatedValue());
												break;
											case 'F':
												//午餐
												$route_detail['route_lunch'] = strval($cell_date->getCalculatedValue());
												break;
											case 'G':
												//晚餐
												$route_detail['route_dinner'] = strval($cell_date->getCalculatedValue());
												break;
											case 'H':
												//酒店
												$route_detail['route_hotel'] = strval($cell_date->getCalculatedValue());
												break;
											case 'I':
												//景点
												$spot_text = $cell_date->getCalculatedValue();
												$spot_list = array();
												if($spot_text) {
													$spot_text_list = explode(';', $spot_text);
													foreach($spot_text_list as $spot_name) {
														if(!isset($spot_master_list[$spot_name])) {
															$spot_list[] = -1;
														} else {
															$spot_list[] = $spot_master_list[$spot_name];
														}
													}
												}
												$route_detail['route_spot_list'] = $spot_list;
												break;
											default:
												break;
										}
									}
									if(isset($route_list[$route_id])) {
										$route_list[$route_id]['detail_list'][] = array(
											'row_id' => $row_id,
											'route_detail_day' => $route_detail['route_detail_day'],
											'route_detail_title' => $route_detail['route_detail_title'],
											'route_detail_content' => $route_detail['route_detail_content'],
											'route_spot_list' => $route_detail['route_spot_list'],
											'route_breakfast' => $route_detail['route_breakfast'],
											'route_lunch' => $route_detail['route_lunch'],
											'route_dinner' => $route_detail['route_dinner'],
											'route_hotel' => $route_detail['route_hotel'],
										);
									}
								}
							}
							
							//添加旅游路线
							if(count($route_list)) {
								foreach($route_list as $row_id => $params_insert) {
									//输入内容检查
									$result_check = Model_Route::CheckEditRoute($params_insert);
									
									if($result_check['result']) {
										//添加数据
										$result_insert = Model_Route::InsertRoute($params_insert);
										
										if(!$result_insert) {
											//添加失败
											$error_list_route[$row_id][] = '发生数据库错误,请重新尝试添加';
										} else {
											$row_list_remove_route[] = $row_id;
											foreach($route_list[$row_id]['detail_list'] as $detail) {
												$row_list_remove_detail[] = $detail['row_id'];
											}
										}
									} else {
										//获取错误信息
										foreach($result_check['error'] as $error) {
											switch($error) {
												case 'empty_route_name': 
													$error_list_route[$row_id][] = '旅游路线名不能空白,请输入旅游路线名';
													break;
												case 'long_route_name': 
													$error_list_route[$row_id][] = '旅游路线名不能超过100字,请调整旅游路线名';
													break;
												case 'dup_route_name': 
													$error_list_route[$row_id][] = '该旅游路线名与系统中其他旅游路线重复,请选用其他旅游路线名';
													break;
												case 'empty_route_description': 
													$error_list_route[$row_id][] = '旅游路线简介不能空白,请输入旅游路线简介';
													break;
												case 'empty_route_price': 
													$error_list_route[$row_id][] = '价格不能空白,请输入一个非负整数';
													break;
												case 'nonum_route_price': 
												case 'minus_route_price': 
													$error_list_route[$row_id][] = '价格部分不符合要求,请输入一个非负整数';
													break;
												case 'error_route_price': 
													$error_list_route[$row_id][] = '最低价不能高于最高价';
													break;
												case 'empty_route_base_cost': 
													$error_list_route[$row_id][] = '基本成本不能空白,请输入一个非负整数';
													break;
												case 'nonum_route_base_cost': 
												case 'minus_route_base_cost': 
													$error_list_route[$row_id][] = '基本成本部分不符合要求,请输入一个非负整数';
													break;
												case 'empty_route_traffic_cost': 
													$error_list_route[$row_id][] = '交通费不能空白,请输入一个非负整数';
													break;
												case 'nonum_route_traffic_cost': 
												case 'minus_route_traffic_cost': 
													$error_list_route[$row_id][] = '交通费部分不符合要求,请输入一个非负整数';
													break;
												case 'empty_route_parking_cost': 
													$error_list_route[$row_id][] = '停车费不能空白,请输入一个非负整数';
													break;
												case 'nonum_route_parking_cost': 
												case 'minus_route_parking_cost': 
													$error_list_route[$row_id][] = '停车费部分不符合要求,请输入一个非负整数';
													break;
												case 'empty_detail_list': 
													$error_list_route[$row_id][] = '详细日程表中没有相应的日程信息,请至少为旅游路线添加一天日程';
													break;
												case 'empty_route_detail_day': 
													$error_list_route[$row_id][] = '详细日程表中天数序号不能空白,请输入天数序号';
													break;
												case 'noint_route_detail_day': 
												case 'minus_route_detail_day': 
													$error_list_route[$row_id][] = '详细日程表中天数序号不符合要求,请输入一个非负整数';
													break;
												case 'over_route_detail_day': 
													$error_list_route[$row_id][] = '详细日程表中天数序号不连贯,请检查并修改天数序号';
													break;
												case 'dup_route_detail_day': 
													$error_list_route[$row_id][] = '详细日程表中同一路线的天数序号存在重复,请检查并修改天数序号';
													break;
												case 'empty_route_detail_title': 
													$error_list_route[$row_id][] = '详细日程表中标题不能空白,请输入标题';
													break;
												case 'long_route_detail_title': 
													$error_list_route[$row_id][] = '详细日程表中标题不能超过100字,请调整标题';
													break;
												case 'empty_route_detail_content': 
													$error_list_route[$row_id][] = '详细日程表中简介不能空白,请输入简介';
													break;
												case 'error_spot_list': 
													$error_list_route[$row_id][] = '详细日程表中所输入的景点名不存在,请确认景点名后重新尝试添加';
													break;
												default:
													$error_list_route[$row_id][] = '发生系统错误,请重新尝试添加';
													break;
											}
										}
									}
								}
							}
							
							if(!count($error_list_route) && !count($error_list_detail)) {
								//全部成功添加
								$_SESSION['import_route_success'] = true;
								//释放缓存
								$xls_upload->disconnectWorksheets();
								unset($sheet_route);
								unset($sheet_detail);
								unset($xls_upload);
							} else {
								//设置修改项目列宽度
								$sheet_route->getColumnDimension('H')->setWidth( 80 );
								$sheet_detail->getColumnDimension('J')->setWidth( 80 );
								//设置修改项目列表头
								$sheet_route->setCellValue('H1', '修改项目');
								$sheet_detail->setCellValue('J1', '修改项目');
								
								//在Excel中加入异常报告
								foreach($error_list_route as $row_id => $error_list) {
									$sheet_route->setCellValue('H' . $row_id, implode(PHP_EOL, array_unique($error_list)));
									$sheet_route->getStyle('H' . $row_id)->getAlignment()->setWrapText(true);
								}
								foreach($error_list_detail as $row_id => $error_list) {
									$sheet_detail->setCellValue('J' . $row_id, implode(PHP_EOL, array_unique($error_list)));
									$sheet_detail->getStyle('J' . $row_id)->getAlignment()->setWrapText(true);
								}
								
								//删除没有问题的行
								if(count($row_list_remove_route)) {
									$row_list_remove_route = array_unique($row_list_remove_route);
									rsort($row_list_remove_route);
									foreach($row_list_remove_route as $row_id) {
										$sheet_route->removeRow($row_id, 1);
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
								$file_directory_tmp = DOCROOT . 'assets/xls/tmp/' . $_SESSION['login_user']['id'] . '/route/';
								if(!file_exists($file_directory_tmp)) {
									mkdir($file_directory_tmp, 0777, TRUE);
								}
								$writer_xls->save($file_directory_tmp . 'import_route_error.xls');
								
								//部分景点未能成功导入
								$_SESSION['import_route_error'] = 'error_import';
								
								//释放缓存
								$xls_upload->disconnectWorksheets();
								unset($writer_xls);
								unset($sheet_route);
								unset($sheet_detail);
								unset($xls_upload);
							}
						}
					}
				}
				
				//页面返回目标
				switch($_POST['page']) {
					case 'route_list':
						if(isset($_SERVER['HTTP_REFERER'])) {
							if(strstr($_SERVER['HTTP_REFERER'], 'admin/route_list')) {
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
			$_SESSION['import_route_error'] = 'error_system';
		}
		
		header('Location: ' . $header_url);
		exit;
	}

}