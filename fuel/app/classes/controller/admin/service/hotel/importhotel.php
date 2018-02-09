<?php
/* 
 * 批量导入酒店
 */

class Controller_Admin_Service_Hotel_Importhotel extends Controller_Admin_App
{

	/**
	 * 批量导入酒店
	 * @access  public
	 * @return  Response
	 */
	public function action_index($page = 1)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/hotel_list/';
		
		try {
			if(!isset($_POST['page'])) {
				//未指明来源页
				$_SESSION['import_hotel_error'] = 'error_system';
			} else {
				if(!isset($_FILES['file_hotel_list']['name'])) {
					//未上传任何文件
					$_SESSION['import_hotel_error'] = 'noexist_file';
				} elseif($_FILES['file_hotel_list']['type'] != 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' && $_FILES['file_hotel_list']['type'] != 'application/vnd.ms-excel') {
					//上传的文件不是Excel
					$_SESSION['import_hotel_error'] = 'noexcel_file';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 18)) {
					//当前登陆用户不具备批量导入酒店的权限
					$_SESSION['import_hotel_error'] = 'error_permission';
				} else {
					//Excel处理用组件
					include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel.php');
					include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
					
					//异常列表
					$error_list_hotel = array();
					//删除行列表
					$row_list_remove = array();
					//酒店数据列表
					$hotel_list = array();
					//当前登陆用户ID
					$login_user_id = $_SESSION['login_user']['id'];
					
					//地区列表
					$area_list = Model_Area::GetAreaList(array('active_only' => true));
					foreach($area_list as $area) {
						$area_master_list[$area['area_name']] = $area['area_id'];
					}
					
					//酒店类型列表
					$hotel_type_list = Model_HotelType::SelectHotelTypeList(array('active_only' => true));
					foreach($hotel_type_list as $hotel_type) {
						$type_master_list[$hotel_type['hotel_type_name']] = $hotel_type['hotel_type_id'];
					}
					
					//房型列表
					$room_type_list = Model_RoomType::SelectRoomTypeList(array('active_only' => true));
					foreach($room_type_list as $room_type) {
						$room_master_list[$room_type['room_type_name']] = $room_type['room_type_id'];
					}
					
					//文件拓展名获取
					preg_match('/[^.]+$/', $_FILES['file_hotel_list']['name'], $tmp);
					$extension = $tmp[0];
					//临时文件位置
					$filepath = $_FILES['file_hotel_list']['tmp_name'];
					
					//文件内容获取
					if($extension == 'xls'){
						$xls_upload = PHPExcel_IOFactory::load($filepath);
					}else{
						$xls_upload = PHPExcel_IOFactory::createReader('Excel2007')->load($filepath);
					}
					
					if(!$xls_upload->sheetNameExists('酒店')) {
						//必要的sheet不存在
						$_SESSION['import_hotel_error'] = 'noexist_sheet';
					} else {
						//添加数据整理
						//酒店Sheet
						$sheet_hotel = $xls_upload->getSheetByName('酒店');
						
						//逐行获取数据
						foreach($sheet_hotel->getRowIterator() as $row_id => $row) {
							//跳过表头与范例,及酒店名为空的行
							if($row_id > 2 && $sheet_hotel->getCell('A' . $row_id)->getValue()) {
								$hotel_info = array();
								//逐个单元格获取数据
								foreach($row->getCellIterator() as $cell_id => $cell_date) {
									switch($cell_id) {
										case 'A':
											//酒店名
											$hotel_info['hotel_name'] = strval($cell_date->getCalculatedValue());
											break;
										case 'B':
											//酒店地区
											$hotel_area_name = $cell_date->getCalculatedValue();
											if(!$hotel_area_name) {
												$hotel_info['hotel_area'] = '';
											} elseif(!isset($area_master_list[$hotel_area_name])) {
												$hotel_info['hotel_area'] = -1;
											} else {
												$hotel_info['hotel_area'] = $area_master_list[$hotel_area_name];
											}
											break;
										case 'C':
											//酒店类别
											$hotel_type_name = $cell_date->getCalculatedValue();
											if(!$hotel_type_name) {
												$hotel_info['hotel_type'] = '';
											} elseif(!isset($type_master_list[$hotel_type_name])) {
												$hotel_info['hotel_type'] = -1;
											} else {
												$hotel_info['hotel_type'] = $type_master_list[$hotel_type_name];
											}
											break;
										case 'D':
											//价格
											$hotel_info['hotel_price'] = strval($cell_date->getCalculatedValue());
											break;
										case 'E':
											//可选房型
											$room_type_text = $cell_date->getCalculatedValue();
											$room_type_list = array();
											if($room_type_text) {
												$room_type_text_list = explode(';', $room_type_text);
												foreach($room_type_text_list as $room_type_name) {
													if(!isset($room_master_list[$room_type_name])) {
														$room_type_list[] = -1;
													} else {
														$room_type_list[] = $room_master_list[$room_type_name];
													}
												}
											}
											$hotel_info['room_type_list'] = $room_type_list;
											break;
									}
								}
								$hotel_list[$row_id] = array(
									'hotel_id' => '',
									'hotel_name' => $hotel_info['hotel_name'],
									'hotel_area' => $hotel_info['hotel_area'],
									'hotel_type' => $hotel_info['hotel_type'],
									'hotel_price' => $hotel_info['hotel_price'],
									'hotel_status' => 0,
									'created_by' => $login_user_id,
									'modified_by' => $login_user_id,
									'room_type_list' => $hotel_info['room_type_list'],
								);
							}
						}
						
						//添加酒店
						if(count($hotel_list)) {
							foreach($hotel_list as $row_id => $params_insert) {
								//输入内容检查
								$result_check = Model_Hotel::CheckEditHotel($params_insert);
								
								if($result_check['result']) {
									//添加数据
									$result_insert = Model_Hotel::InsertHotel($params_insert);
									
									if(!$result_insert) {
										//添加失败
										$error_list_hotel[$row_id][] = '发生数据库错误,请重新尝试添加';
									} else {
										$row_list_remove[] = $row_id;
									}
								} else {
									//获取错误信息
									foreach($result_check['error'] as $error) {
										switch($error) {
											case 'empty_hotel_name': 
												$error_list_hotel[$row_id][] = '酒店名不能空白,请输入酒店名';
												break;
											case 'long_hotel_name': 
												$error_list_hotel[$row_id][] = '酒店名不能超过100字,请调整酒店名';
												break;
											case 'dup_hotel_name': 
												$error_list_hotel[$row_id][] = '该酒店名与其他酒店重复,请选用其他酒店名';
												break;
											case 'empty_hotel_area': 
												$error_list_hotel[$row_id][] = '酒店地区不能空白,请选择酒店地区';
												break;
											case 'error_hotel_area': 
												$error_list_hotel[$row_id][] = '所选中的酒店地区不存在,请下载最新的模板';
												break;
											case 'empty_hotel_type': 
												$error_list_hotel[$row_id][] = '酒店类别不能空白,请选择酒店类别';
												break;
											case 'error_hotel_type': 
												$error_list_hotel[$row_id][] = '所选中的酒店类别不存在,请下载最新的模板';
												break;
											case 'empty_hotel_price': 
												$error_list_hotel[$row_id][] = '价格不能空白,请输入一个非负整数';
												break;
											case 'noint_hotel_price': 
											case 'minus_hotel_price': 
												$error_list_hotel[$row_id][] = '价格不符合要求,请输入一个非负整数';
												break;
											case 'empty_room': 
												$error_list_hotel[$row_id][] = '可选房型不能空白,请输入可选房型';
												break;
											case 'error_room': 
												$error_list_hotel[$row_id][] = '所输入的可选房型不存在,请下载最新的模板,并参考「参考-可选房型」表';
												break;
											default:
												$error_list_hotel[$row_id][] = '发生系统错误,请重新尝试添加';
												break;
										}
									}
								}
							}
						}
						
						if(!count($error_list_hotel)) {
							//全部成功添加
							$_SESSION['import_hotel_success'] = true;
							//释放缓存
							$xls_upload->disconnectWorksheets();
							unset($sheet_hotel);
							unset($xls_upload);
						} else {
							//设置修改项目列宽度
							$sheet_hotel->getColumnDimension('F')->setWidth( 80 );
							//设置修改项目列表头
							$sheet_hotel->setCellValue('F1', '修改项目');
							
							//在Excel中加入异常报告
							foreach($error_list_hotel as $row_id => $error_list) {
								$sheet_hotel->setCellValue('F' . $row_id, implode(PHP_EOL, array_unique($error_list)));
								$sheet_hotel->getStyle('F' . $row_id)->getAlignment()->setWrapText(true);
							}
							
							//删除没有问题的行
							if(count($row_list_remove)) {
								$row_list_remove = array_unique($row_list_remove);
								rsort($row_list_remove);
								foreach($row_list_remove as $row_id) {
									$sheet_hotel->removeRow($row_id, 1);
								}
							}
							
							//保存为下载用临时文件
							$writer_xls = PHPExcel_IOFactory::createWriter($xls_upload, 'Excel2007');
							$file_directory_tmp = DOCROOT . 'assets/xls/tmp/' . $_SESSION['login_user']['id'] . '/hotel/';
							if(!file_exists($file_directory_tmp)) {
								mkdir($file_directory_tmp, 0777, TRUE);
							}
							$writer_xls->save($file_directory_tmp . 'import_hotel_error.xls');
							
							//部分酒店未能成功导入
							$_SESSION['import_hotel_error'] = 'error_import';
							
							//释放缓存
							$xls_upload->disconnectWorksheets();
							unset($writer_xls);
							unset($sheet_hotel);
							unset($xls_upload);
						}
					}
				}
				
				//页面返回目标
				switch($_POST['page']) {
					case 'hotel_list':
						if(isset($_SERVER['HTTP_REFERER'])) {
							if(strstr($_SERVER['HTTP_REFERER'], 'admin/hotel_list')) {
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
			$_SESSION['import_hotel_error'] = 'error_system';
		}
		
		header('Location: ' . $header_url);
		exit;
	}

}