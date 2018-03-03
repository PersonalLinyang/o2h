<?php
/* 
 * 批量导入餐饮店
 */

class Controller_Admin_Service_Restaurant_Importrestaurant extends Controller_Admin_App
{

	/**
	 * 批量导入餐饮店
	 * @access  public
	 * @return  Response
	 */
	public function action_index($page = 1)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/restaurant_list/';
		
		try {
			if(!isset($_POST['page'])) {
				//未指明来源页
				$_SESSION['import_restaurant_error'] = 'error_system';
			} else {
				if(!isset($_FILES['file_restaurant_list']['name'])) {
					//未上传任何文件
					$_SESSION['import_restaurant_error'] = 'noexist_file';
				} elseif($_FILES['file_restaurant_list']['type'] != 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' && $_FILES['file_restaurant_list']['type'] != 'application/vnd.ms-excel') {
					//上传的文件不是Excel
					$_SESSION['import_restaurant_error'] = 'noexcel_file';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 13)) {
					//当前登陆用户不具备批量导入餐饮店的权限
					$_SESSION['import_restaurant_error'] = 'error_permission';
				} else {
					//Excel处理用组件
					include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel.php');
					include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
					
					//餐饮店临时ID列表
					$restaurant_tmpid_list = array();
					//异常列表
					$error_list_restaurant = array();
					//删除行列表
					$row_list_remove = array();
					//餐饮店数据列表
					$restaurant_list = array();
					//当前登陆用户ID
					$login_user_id = $_SESSION['login_user']['id'];
					
					//地区列表
					$area_list = Model_Area::SelectAreaList(array('active_only' => true));
					foreach($area_list as $area) {
						$area_master_list[$area['area_name']] = $area['area_id'];
					}
					
					//餐饮店类型列表
					$restaurant_type_list = Model_RestaurantType::SelectRestaurantTypeList(array('active_only' => true));
					foreach($restaurant_type_list as $restaurant_type) {
						$type_master_list[$restaurant_type['restaurant_type_name']] = $restaurant_type['restaurant_type_id'];
					}
					
					//文件拓展名获取
					preg_match('/[^.]+$/', $_FILES['file_restaurant_list']['name'], $tmp);
					$extension = $tmp[0];
					//临时文件位置
					$filepath = $_FILES['file_restaurant_list']['tmp_name'];
					
					//文件内容获取
					if($extension == 'xls'){
						$xls_upload = PHPExcel_IOFactory::load($filepath);
					}else{
						$xls_upload = PHPExcel_IOFactory::createReader('Excel2007')->load($filepath);
					}
					
					if(!$xls_upload->sheetNameExists('餐饮店')) {
						//必要的sheet不存在
						$_SESSION['import_restaurant_error'] = 'noexist_sheet';
					} else {
						//添加数据整理
						//餐饮店Sheet
						$sheet_restaurant = $xls_upload->getSheetByName('餐饮店');
						
						//逐行获取数据
						foreach($sheet_restaurant->getRowIterator() as $row_id => $row) {
							//跳过表头与范例,及餐饮店名为空的行
							if($row_id > 2 && $sheet_restaurant->getCell('A' . $row_id)->getValue()) {
								$restaurant_info = array();
								//逐个单元格获取数据
								$error_flag = false;
								foreach($row->getCellIterator() as $cell_id => $cell_date) {
									switch($cell_id) {
										case 'A':
											//餐饮店名
											$restaurant_info['restaurant_name'] = strval($cell_date->getCalculatedValue());
											break;
										case 'B':
											//餐饮店地区
											$restaurant_area_name = $cell_date->getCalculatedValue();
											if(!$restaurant_area_name) {
												$restaurant_info['restaurant_area'] = '';
											} elseif(!isset($area_master_list[$restaurant_area_name])) {
												$restaurant_info['restaurant_area']  = -1;
											} else {
												$restaurant_info['restaurant_area'] = $area_master_list[$restaurant_area_name];
											}
											break;
										case 'C':
											//餐饮店类别
											$restaurant_type_name = $cell_date->getCalculatedValue();
											if(!$restaurant_type_name) {
												$restaurant_info['restaurant_type'] = '';
											} elseif(!isset($type_master_list[$restaurant_type_name])) {
												$restaurant_info['restaurant_type'] = -1;
											} else {
												$restaurant_info['restaurant_type'] = $type_master_list[$restaurant_type_name];
											}
											break;
										case 'D':
											//最低价
											$restaurant_info['restaurant_price_min'] = strval($cell_date->getCalculatedValue());
											break;
										case 'E':
											//最高价
											$restaurant_info['restaurant_price_max'] = strval($cell_date->getCalculatedValue());
											break;
									}
								}
								if(!$error_flag) {
									$restaurant_list[$row_id] = array(
										'restaurant_id' => '',
										'restaurant_name' => $restaurant_info['restaurant_name'],
										'restaurant_area' => $restaurant_info['restaurant_area'],
										'restaurant_type' => $restaurant_info['restaurant_type'],
										'restaurant_price_min' => $restaurant_info['restaurant_price_min'],
										'restaurant_price_max' => $restaurant_info['restaurant_price_max'],
										'restaurant_status' => 0,
										'created_by' => $login_user_id,
										'modified_by' => $login_user_id,
									);
								}
							}
						}
						
						//添加餐饮店
						if(count($restaurant_list)) {
							foreach($restaurant_list as $row_id => $params_insert) {
								//输入内容检查
								$result_check = Model_Restaurant::CheckEditRestaurant($params_insert);
								
								if($result_check['result']) {
									//添加数据
									$result_insert = Model_Restaurant::InsertRestaurant($params_insert);
									
									if(!$result_insert) {
										//添加失败
										$error_list_restaurant[$row_id][] = '发生数据库错误,请重新尝试添加';
									} else {
										$row_list_remove[] = $row_id;
									}
								} else {
									//获取错误信息
									foreach($result_check['error'] as $error) {
										switch($error) {
											case 'empty_restaurant_name': 
												$error_list_restaurant[$row_id][] = '餐饮店名不能空白,请输入餐饮店名';
												break;
											case 'long_restaurant_name': 
												$error_list_restaurant[$row_id][] = '餐饮店名不能超过100字,请调整餐饮店名';
												break;
											case 'dup_restaurant_name': 
												$error_list_restaurant[$row_id][] = '该餐饮店名与其他餐饮店重复,请选用其他餐饮店名';
												break;
											case 'empty_restaurant_area': 
												$error_list_restaurant[$row_id][] = '餐饮店地区不能空白,请选择餐饮店地区';
												break;
											case 'error_restaurant_area': 
												$error_list_restaurant[$row_id][] = '所选中的餐饮店地区不存在,请下载最新的模板';
												break;
											case 'empty_restaurant_type': 
												$error_list_restaurant[$row_id][] = '餐饮店类别不能空白,请选择餐饮店类别';
												break;
											case 'error_restaurant_type': 
												$error_list_restaurant[$row_id][] = '所选中的餐饮店类别不存在,请下载最新的模板';
												break;
											case 'empty_restaurant_price': 
												$error_list_restaurant[$row_id][] = '价格不能空白,请输入一个非负整数';
												break;
											case 'noint_restaurant_price': 
											case 'noint_restaurant_price': 
												$error_list_restaurant[$row_id][] = '价格不符合要求,请输入一个非负整数';
												break;
											case 'error_restaurant_price': 
												$error_list_restaurant[$row_id][] = '最低价不能高于最高价';
												break;
											default:
												$error_list_restaurant[$row_id][] = '发生系统错误,请重新尝试添加';
												break;
										}
									}
								}
							}
						}
						
						if(!count($error_list_restaurant)) {
							//全部成功添加
							$_SESSION['import_restaurant_success'] = true;
							//释放缓存
							$xls_upload->disconnectWorksheets();
							unset($sheet_restaurant);
							unset($sheet_detail);
							unset($xls_upload);
						} else {
							//设置修改项目列宽度
							$sheet_restaurant->getColumnDimension('F')->setWidth( 80 );
							//设置修改项目列表头
							$sheet_restaurant->setCellValue('F1', '修改项目');
							
							//在Excel中加入异常报告
							foreach($error_list_restaurant as $row_id => $error_list) {
								$sheet_restaurant->setCellValue('F' . $row_id, implode(PHP_EOL, array_unique($error_list)));
								$sheet_restaurant->getStyle('F' . $row_id)->getAlignment()->setWrapText(true);
							}
							
							//删除没有问题的行
							if(count($row_list_remove)) {
								$row_list_remove = array_unique($row_list_remove);
								rsort($row_list_remove);
								foreach($row_list_remove as $row_id) {
									$sheet_restaurant->removeRow($row_id, 1);
								}
							}
							
							//保存为下载用临时文件
							$writer_xls = PHPExcel_IOFactory::createWriter($xls_upload, 'Excel2007');
							$file_directory_tmp = DOCROOT . 'assets/xls/tmp/' . $_SESSION['login_user']['id'] . '/restaurant/';
							if(!file_exists($file_directory_tmp)) {
								mkdir($file_directory_tmp, 0777, TRUE);
							}
							$writer_xls->save($file_directory_tmp . 'import_restaurant_error.xls');
							
							//部分餐饮店未能成功导入
							$_SESSION['import_restaurant_error'] = 'error_import';
							
							//释放缓存
							$xls_upload->disconnectWorksheets();
							unset($writer_xls);
							unset($sheet_restaurant);
							unset($sheet_detail);
							unset($xls_upload);
						}
					}
				}
				
				//页面返回目标
				switch($_POST['page']) {
					case 'restaurant_list':
						if(isset($_SERVER['HTTP_REFERER'])) {
							if(strstr($_SERVER['HTTP_REFERER'], 'admin/restaurant_list')) {
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
			$_SESSION['import_restaurant_error'] = 'error_system';
		}
		
		header('Location: ' . $header_url);
		exit;
	}

}