<?php
/* 
 * 导出酒店
 */

class Controller_Admin_Service_Hotel_Exporthotel extends Controller_Admin_App
{

	/**
	 * 导出酒店
	 * @access  public
	 * @return  Response
	 */
	public function action_index($params = null)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/hotel_list/';
		
		try {
			if(!isset($_POST['page'])) {
				//未指明来源页
				$_SESSION['export_hotel_error'] = 'error_system';
			} else {
				if(!isset($_POST['export_model'])) {
					//未设定导出模式
					$_SESSION['export_hotel_error'] = 'error_system';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 19)) {
					//当前登陆用户不具备导出酒店的权限
					$_SESSION['export_hotel_error'] = 'error_permission';
				} else {
					//Excel处理用组件
					include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel.php');
					include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
					
					$select_name_tmp = isset($_POST['select_name']) ? preg_replace('/( |　)/', ' ', $_POST['select_name']) : '';
					
					//获取酒店信息
					$params_select = array(
						'hotel_name' => trim($select_name_tmp) ? explode(' ', $select_name_tmp) : array(),
						'hotel_status' => isset($_POST['select_status']) ? ($_POST['select_status'] ? explode(',', $_POST['select_status']) : array()) : array(),
						'hotel_area' => isset($_POST['select_area']) ? ($_POST['select_area'] ? explode(',', $_POST['select_area']) : array()) : array(),
						'hotel_type' => isset($_POST['select_hotel_type']) ? ($_POST['select_hotel_type'] ? explode(',', $_POST['select_hotel_type']) : array()) : array(),
						'price_min' => isset($_POST['select_price_min']) ? $_POST['select_price_min'] : '',
						'price_max' => isset($_POST['select_price_max']) ? $_POST['select_price_max'] : '',
						'sort_column' => isset($_POST['sort_column']) ? $_POST['sort_column'] : 'created_at',
						'sort_method' => isset($_POST['sort_method']) ? $_POST['sort_method'] : 'desc',
						'active_only' => true,
						'room_flag' => true,
					);
					
					$result_select = Model_Hotel::SelectHotelList($params_select);
					
					if($result_select) {
						$hotel_list = $result_select['hotel_list'];
						if($_POST['export_model'] == 'review') {
							//阅览模式导出
							$xls_export = new PHPExcel();
							$xls_export->getProperties()->setCreator('O2H Information Manage System');
							$xls_export->setActiveSheetIndex(0);
							$sheet_hotel = $xls_export->getActiveSheet();
							$sheet_hotel->setTitle('酒店');
							
							//设定自动换行并改变列宽
							$sheet_hotel->getDefaultStyle()->getAlignment()->setWrapText(true);
							$sheet_hotel->getColumnDimension('A')->setWidth( 20 );
							$sheet_hotel->getColumnDimension('B')->setWidth( 12 );
							$sheet_hotel->getColumnDimension('C')->setWidth( 12 );
							$sheet_hotel->getColumnDimension('D')->setWidth( 20 );
							$sheet_hotel->getColumnDimension('E')->setWidth( 20 );
							
							//设定表头
							$sheet_hotel->setCellValue('A1', '酒店名');
							$sheet_hotel->setCellValue('B1', '酒店地区');
							$sheet_hotel->setCellValue('C1', '酒店类别');
							$sheet_hotel->setCellValue('D1', '价格(日元/人夜)');
							$sheet_hotel->setCellValue('E1', '可选房型');
							
							//写入酒店信息
							$row_counter = 2;
							foreach($hotel_list as $hotel) {
								$sheet_hotel->setCellValue('A' . $row_counter, $hotel['hotel_name']);
								$sheet_hotel->setCellValue('B' . $row_counter, $hotel['hotel_area_name']);
								$sheet_hotel->setCellValue('C' . $row_counter, $hotel['hotel_type_name']);
								$sheet_hotel->setCellValue('D' . $row_counter, $hotel['hotel_price']);
								
								$room_type_list = array();
								foreach($hotel['room_type_list'] as $room_type) {
									$room_type_list[] = $room_type['room_type_name'];
								}
								$sheet_hotel->setCellValue('E' . $row_counter, implode(PHP_EOL, $room_type_list));
								
								$row_counter++;
							}
							
							//下载文件
							$writer_xls = PHPExcel_IOFactory::createWriter($xls_export, 'Excel2007');
							header("Pragma: public");
							header("Expires: 0");
							header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
							header("Content-Type: application/force-download");
							header("Content-Type: application/octet-stream");
							header("Content-Type: application/download");
							header("Content-Disposition: attachment;filename=" . "export_hotel_review.xlsx");
							header("Content-Transfer-Encoding: binary ");
							$writer_xls->save('php://output');
							
							$xls_export->disconnectWorksheets();
							unset($writer_xls);
							unset($sheet_hotel);
							unset($xls_export);
							
							exit;
						} elseif($_POST['export_model'] == 'backup') {
							//备份模式导出
							//读取模板
							$xls_export = PHPExcel_IOFactory::load(DOCROOT . '/assets/xls/model/import_hotel_model.xls');
							$sheet_hotel = $xls_export->getSheetByName('酒店');
							
							//写入酒店信息
							$row_counter = 3;
							foreach($hotel_list as $hotel) {
								$sheet_hotel->setCellValue('A' . $row_counter, $hotel['hotel_name']);
								$sheet_hotel->setCellValue('B' . $row_counter, $hotel['hotel_area_name']);
								$sheet_hotel->setCellValue('C' . $row_counter, $hotel['hotel_type_name']);
								$sheet_hotel->setCellValue('D' . $row_counter, $hotel['hotel_price']);
								
								$room_type_list = array();
								foreach($hotel['room_type_list'] as $room_type) {
									$room_type_list[] = $room_type['room_type_name'];
								}
								$sheet_hotel->setCellValue('E' . $row_counter, implode(';', $room_type_list));
								
								$row_counter++;
							}
							
							//下载文件
							$writer_xls = PHPExcel_IOFactory::createWriter($xls_export, 'Excel2007');
							header("Pragma: public");
							header("Expires: 0");
							header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
							header("Content-Type: application/force-download");
							header("Content-Type: application/octet-stream");
							header("Content-Type: application/download");
							header("Content-Disposition: attachment;filename=" . "export_hotel_backup.xlsx");
							header("Content-Transfer-Encoding: binary ");
							$writer_xls->save('php://output');
							
							$xls_export->disconnectWorksheets();
							unset($writer_xls);
							unset($sheet_hotel);
							unset($sheet_detail);
							unset($xls_export);
							
							exit;
						} else {
							//导出模式无法识别
							$_SESSION['export_hotel_error'] = 'error_system';
						}
					} else {
						//未能取得任何酒店信息
						$_SESSION['export_hotel_error'] = 'empty_hotel_list';
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