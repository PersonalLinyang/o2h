<?php
/* 
 * 导出景点
 */

class Controller_Admin_Service_Spot_Exportspot extends Controller_Admin_App
{

	/**
	 * 导出景点
	 * @access  public
	 * @return  Response
	 */
	public function action_index($page = 1)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/spot_list/';
		
		try {
			if(!isset($_POST['page'])) {
				//未指明来源页
				$_SESSION['export_spot_error'] = 'error_system';
			} else {
				if(!isset($_POST['export_model'])) {
					//未设定导出模式
					$_SESSION['export_spot_error'] = 'error_system';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 9)) {
					//当前登陆用户不具备导出景点的权限
					$_SESSION['export_spot_error'] = 'error_permission';
				} else {
					//Excel处理用组件
					include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel.php');
					include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
					
					$select_name_tmp = isset($_POST['select_name']) ? preg_replace('/( |　)/', ' ', $_POST['select_name']) : '';
					
					//获取景点信息
					$params_select = array(
						'spot_name' => trim($select_name_tmp) ? explode(' ', $select_name_tmp) : array(),
						'spot_status' => isset($_POST['select_status']) ? ($_POST['select_status'] ? explode(',', $_POST['select_status']) : array()) : array(),
						'spot_area' => isset($_POST['select_area']) ? ($_POST['select_area'] ? explode(',', $_POST['select_area']) : array()) : array(),
						'spot_type' => isset($_POST['select_spot_type']) ? ($_POST['select_spot_type'] ? explode(',', $_POST['select_spot_type']) : array()) : array(),
						'free_flag' => isset($_POST['select_free_flag']) ? ($_POST['select_free_flag'] ? explode(',', $_POST['select_free_flag']) : array()) : array(),
						'price_min' => isset($_POST['select_price_min']) ? $_POST['select_price_min'] : '',
						'price_max' => isset($_POST['select_price_max']) ? $_POST['select_price_max'] : '',
						'sort_column' => isset($_POST['sort_column']) ? $_POST['sort_column'] : 'created_at',
						'sort_method' => isset($_POST['sort_method']) ? $_POST['sort_method'] : 'desc',
						'active_only' => 1,
						'price_flag' => 1,
						'detail_flag' => 1,
					);
					
					$result_select = Model_Spot::SelectSpotList($params_select);
					
					if($result_select) {
						$spot_list = $result_select['spot_list'];
						
						if($_POST['export_model'] == 'review') {
							//阅览模式导出
							$xls_export = new PHPExcel();
							$xls_export->getProperties()->setCreator('O2H Information Manage System');
							$xls_export->setActiveSheetIndex(0);
							$sheet_spot = $xls_export->getActiveSheet();
							$sheet_spot->setTitle('spot');
							
							//设定自动换行并改变列宽
							$sheet_spot->getDefaultStyle()->getAlignment()->setWrapText(true);
							$sheet_spot->getColumnDimension('A')->setWidth( 20 );
							$sheet_spot->getColumnDimension('B')->setWidth( 12 );
							$sheet_spot->getColumnDimension('C')->setWidth( 12 );
							$sheet_spot->getColumnDimension('D')->setWidth( 10 );
							$sheet_spot->getColumnDimension('E')->setWidth( 10 );
							$sheet_spot->getColumnDimension('F')->setWidth( 30 );
							$sheet_spot->getColumnDimension('G')->setWidth( 20 );
							$sheet_spot->getColumnDimension('H')->setWidth( 80 );
							$sheet_spot->getColumnDimension('I')->setWidth( 10 );
							$sheet_spot->getColumnDimension('J')->setWidth( 10 );
							
							//设定表头
							$sheet_spot->setCellValue('A1', '景点名');
							$sheet_spot->setCellValue('B1', '景点地区');
							$sheet_spot->setCellValue('C1', '景点类别');
							$sheet_spot->setCellValue('D1', '免/收费');
							$sheet_spot->setCellValue('E1', '票价');
							$sheet_spot->setCellValue('F1', '特别价格');
							$sheet_spot->setCellValue('G1', '景点详情名');
							$sheet_spot->setCellValue('H1', '景点描述');
							$sheet_spot->setCellValue('I1', '开始');
							$sheet_spot->setCellValue('J1', '结束');
							
							//写入景点信息
							$row_counter = 2;
							foreach($spot_list as $spot) {
								$row_start = $row_counter;
								$sheet_spot->setCellValue('A' . $row_counter, $spot['spot_name']);
								$sheet_spot->setCellValue('B' . $row_counter, $spot['spot_area_name']);
								$sheet_spot->setCellValue('C' . $row_counter, $spot['spot_type_name']);
								$sheet_spot->setCellValue('D' . $row_counter, $spot['free_flag'] == '1' ? '收费' : '免费');
								$sheet_spot->setCellValue('E' . $row_counter, $spot['spot_price']);
								
								$special_price_list = array();
								foreach($spot['special_price_list'] as $special_price) {
									$special_price_list[] = $special_price['special_price_name'] . ':' . $special_price['special_price'];
								}
								$sheet_spot->setCellValue('F' . $row_counter, implode(PHP_EOL, $special_price_list));
								
								if(count($spot['spot_detail_list'])) {
									foreach($spot['spot_detail_list'] as $spot_detail) {
										$sheet_spot->setCellValue('G' . $row_counter, $spot_detail['spot_detail_name']);
										$sheet_spot->setCellValue('H' . $row_counter, $spot_detail['spot_description_text']);
										$sheet_spot->setCellValue('I' . $row_counter, $spot_detail['spot_start_month'] . '月');
										$sheet_spot->setCellValue('J' . $row_counter, ($spot_detail['two_year_flag'] == '1' ? '次年' : '') . $spot_detail['spot_end_month'] . '月');
										$row_counter++;
									}
								} else {
									$row_counter++;
								}
								
								$row_end = $row_counter - 1;
								$sheet_spot->mergeCells('A' . $row_start . ':A' . $row_end);
								$sheet_spot->mergeCells('B' . $row_start . ':B' . $row_end);
								$sheet_spot->mergeCells('C' . $row_start . ':C' . $row_end);
								$sheet_spot->mergeCells('D' . $row_start . ':D' . $row_end);
								$sheet_spot->mergeCells('E' . $row_start . ':E' . $row_end);
								$sheet_spot->mergeCells('F' . $row_start . ':F' . $row_end);
							}
							
							//下载文件
							$writer_xls = PHPExcel_IOFactory::createWriter($xls_export, 'Excel2007');
							header("Pragma: public");
							header("Expires: 0");
							header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
							header("Content-Type: application/force-download");
							header("Content-Type: application/octet-stream");
							header("Content-Type: application/download");
							header("Content-Disposition: attachment;filename=" . "export_spot_review.xlsx");
							header("Content-Transfer-Encoding: binary ");
							$writer_xls->save('php://output');
							
							$xls_export->disconnectWorksheets();
							unset($writer_xls);
							unset($sheet_spot);
							unset($xls_export);
							
							exit;
						} elseif($_POST['export_model'] == 'backup') {
							//备份模式导出
							//读取模板
							$xls_export = PHPExcel_IOFactory::load(DOCROOT . '/assets/xls/model/import_spot_model.xls');
							$sheet_spot = $xls_export->getSheetByName('spot');
							$sheet_detail = $xls_export->getSheetByName('spot_detail');
							
							//写入景点信息
							$row_spot_counter = 3;
							$row_detail_counter = 3;
							foreach($spot_list as $spot) {
								$sheet_spot->setCellValue('A' . $row_spot_counter, $spot['spot_name']);
								$sheet_spot->setCellValue('B' . $row_spot_counter, $spot['spot_area_name']);
								$sheet_spot->setCellValue('C' . $row_spot_counter, $spot['spot_type_name']);
								$sheet_spot->setCellValue('D' . $row_spot_counter, $spot['free_flag'] == '1' ? '收费' : '免费');
								$sheet_spot->setCellValue('E' . $row_spot_counter, $spot['spot_price']);
								
								$special_price_list = array();
								foreach($spot['special_price_list'] as $special_price) {
									$special_price_list[] = $special_price['special_price_name'] . ':' . $special_price['special_price'];
								}
								$sheet_spot->setCellValue('F' . $row_spot_counter, implode(';', $special_price_list));
								
								foreach($spot['spot_detail_list'] as $spot_detail) {
									$sheet_detail->setCellValue('A' . $row_detail_counter, $spot['spot_name']);
									$sheet_detail->setCellValue('B' . $row_detail_counter, $spot_detail['spot_detail_name']);
									$sheet_detail->setCellValue('C' . $row_detail_counter, $spot_detail['spot_description_text']);
									$sheet_detail->setCellValue('D' . $row_detail_counter, $spot_detail['spot_start_month'] . '月');
									$sheet_detail->setCellValue('E' . $row_detail_counter, ($spot_detail['two_year_flag'] == '1' ? '次年' : '') . $spot_detail['spot_end_month'] . '月');
									$row_detail_counter++;
								}
								
								$row_spot_counter++;
							}
							
							//下载文件
							$writer_xls = PHPExcel_IOFactory::createWriter($xls_export, 'Excel2007');
							header("Pragma: public");
							header("Expires: 0");
							header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
							header("Content-Type: application/force-download");
							header("Content-Type: application/octet-stream");
							header("Content-Type: application/download");
							header("Content-Disposition: attachment;filename=" . "export_spot_backup.xlsx");
							header("Content-Transfer-Encoding: binary ");
							$writer_xls->save('php://output');
							
							$xls_export->disconnectWorksheets();
							unset($writer_xls);
							unset($sheet_spot);
							unset($sheet_detail);
							unset($xls_export);
							
							exit;
						} else {
							//未能取得任何景点信息
							$_SESSION['export_spot_error'] = 'empty_spot_list';
						}
					} else {
						//导出模式无法识别
						$_SESSION['export_spot_error'] = 'error_system';
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