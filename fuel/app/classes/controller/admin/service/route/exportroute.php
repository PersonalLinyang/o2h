<?php
/* 
 * 导出旅游路线
 */

class Controller_Admin_Service_Route_Exportroute extends Controller_Admin_App
{

	/**
	 * 导出旅游路线
	 * @access  public
	 * @return  Response
	 */
	public function action_index($page = 1)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/route_list/';
		
		try {
			if(!isset($_POST['page'])) {
				//未指明来源页
				$_SESSION['export_route_error'] = 'error_system';
			} else {
				if(!isset($_POST['export_model'])) {
					//未设定导出模式
					$_SESSION['export_route_error'] = 'error_system';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 9)) {
					//当前登陆用户不具备导出旅游路线的权限
					$_SESSION['export_route_error'] = 'error_permission';
				} else {
					//Excel处理用组件
					include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel.php');
					include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
					
					//获取旅游路线信息
					$select_name_tmp = isset($_POST['select_name']) ? preg_replace('/( |　)/', ' ', $_POST['select_name']) : '';
					$params_select = array(
						'route_name' => trim($select_name_tmp) ? explode(' ', $select_name_tmp) : array(),
						'route_status' => isset($_POST['select_status']) ? ($_POST['select_status'] ? explode(',', $_POST['select_status']) : array()) : array(),
						'price_min' => isset($_POST['select_price_min']) ? $_POST['select_price_min'] : '',
						'price_max' => isset($_POST['select_price_max']) ? $_POST['select_price_max'] : '',
						'base_cost_min' => isset($_POST['select_base_cost_min']) ? $_POST['select_base_cost_min'] : '',
						'base_cost_max' => isset($_POST['select_base_cost_max']) ? $_POST['select_base_cost_max'] : '',
						'traffic_cost_min' => isset($_POST['select_traffic_cost_min']) ? $_POST['select_traffic_cost_min'] : '',
						'traffic_cost_max' => isset($_POST['select_traffic_cost_max']) ? $_POST['select_traffic_cost_max'] : '',
						'parking_cost_min' => isset($_POST['select_parking_cost_min']) ? $_POST['select_parking_cost_min'] : '',
						'parking_cost_max' => isset($_POST['select_parking_cost_max']) ? $_POST['select_parking_cost_max'] : '',
						'total_cost_min' => isset($_POST['select_total_cost_min']) ? $_POST['select_total_cost_min'] : '',
						'total_cost_max' => isset($_POST['select_total_cost_max']) ? $_POST['select_total_cost_max'] : '',
						'sort_column' => isset($_POST['sort_column']) ? $_POST['sort_column'] : 'created_at',
						'sort_method' => isset($_POST['sort_method']) ? $_POST['sort_method'] : 'desc',
						'active_only' => true,
						'detail_flag' => true,
					);
					if(isset($_POST['select_self_flag'])) {
						if($_POST['select_self_flag']) {
							$params_select['created_by'] = $_SESSION['login_user']['id'];
						}
					}
					
					$result_select = Model_Route::SelectRouteList($params_select);
					
					if($result_select) {
						$route_list = $result_select['route_list'];
						
						if($_POST['export_model'] == 'review') {
							//阅览模式导出
							$xls_export = new PHPExcel();
							$xls_export->getProperties()->setCreator('O2H Information Manage System');
							$xls_export->setActiveSheetIndex(0);
							$sheet_route = $xls_export->getActiveSheet();
							$sheet_route->setTitle('旅游路线');
							
							//设定自动换行并改变列宽
							$sheet_route->getDefaultStyle()->getAlignment()->setWrapText(true);
							$sheet_route->getColumnDimension('A')->setWidth( 20 );
							$sheet_route->getColumnDimension('B')->setWidth( 40 );
							$sheet_route->getColumnDimension('C')->setWidth( 16 );
							$sheet_route->getColumnDimension('D')->setWidth( 10 );
							$sheet_route->getColumnDimension('E')->setWidth( 10 );
							$sheet_route->getColumnDimension('F')->setWidth( 10 );
							$sheet_route->getColumnDimension('G')->setWidth( 10 );
							$sheet_route->getColumnDimension('H')->setWidth( 6 );
							$sheet_route->getColumnDimension('I')->setWidth( 20 );
							$sheet_route->getColumnDimension('J')->setWidth( 40 );
							$sheet_route->getColumnDimension('K')->setWidth( 40 );
							$sheet_route->getColumnDimension('L')->setWidth( 30 );
							$sheet_route->getColumnDimension('M')->setWidth( 30 );
							$sheet_route->getColumnDimension('N')->setWidth( 30 );
							$sheet_route->getColumnDimension('O')->setWidth( 30 );
							
							//设定表头
							$sheet_route->setCellValue('A1', '旅游路线名');
							$sheet_route->setCellValue('B1', '旅游路线简介');
							$sheet_route->setCellValue('C1', '价格');
							$sheet_route->setCellValue('D1', '基本成本');
							$sheet_route->setCellValue('E1', '交通费');
							$sheet_route->setCellValue('F1', '停车费');
							$sheet_route->setCellValue('G1', '成本合计');
							$sheet_route->setCellValue('H1', '日程');
							$sheet_route->setCellValue('I1', '标题');
							$sheet_route->setCellValue('J1', '简介');
							$sheet_route->setCellValue('K1', '景点');
							$sheet_route->setCellValue('L1', '早餐');
							$sheet_route->setCellValue('M1', '午餐');
							$sheet_route->setCellValue('N1', '晚餐');
							$sheet_route->setCellValue('O1', '酒店');
							
							//写入景点信息
							$row_counter = 2;
							foreach($route_list as $route) {
								$row_start = $row_counter;
								$sheet_route->setCellValue('A' . $row_counter, $route['route_name']);
								$sheet_route->setCellValue('B' . $row_counter, $route['route_description']);
								$sheet_route->setCellValue('C' . $row_counter, $route['route_price_min'] . '～' . $route['route_price_max']);
								$sheet_route->setCellValue('D' . $row_counter, $route['route_base_cost']);
								$sheet_route->setCellValue('E' . $row_counter, $route['route_parking_cost']);
								$sheet_route->setCellValue('F' . $row_counter, $route['route_traffic_cost']);
								$sheet_route->setCellValue('G' . $row_counter, $route['route_total_cost']);
								
								if(count($route['detail_list'])) {
									foreach($route['detail_list'] as $route_detail) {
										$sheet_route->setCellValue('H' . $row_counter, $route_detail['route_detail_day']);
										$sheet_route->setCellValue('I' . $row_counter, $route_detail['route_detail_title']);
										$sheet_route->setCellValue('J' . $row_counter, $route_detail['route_detail_content']);
										$spot_name_list = array();
										foreach($route_detail['route_spot_list'] as $route_spot) {
											$spot_name_list[] = $route_spot['spot_name'];
										}
										$sheet_route->setCellValue('K' . $row_counter, implode(',', $spot_name_list));
										$sheet_route->setCellValue('L' . $row_counter, $route_detail['route_breakfast']);
										$sheet_route->setCellValue('M' . $row_counter, $route_detail['route_lunch']);
										$sheet_route->setCellValue('N' . $row_counter, $route_detail['route_dinner']);
										$sheet_route->setCellValue('O' . $row_counter, $route_detail['route_hotel']);
										$row_counter++;
									}
								} else {
									$row_counter++;
								}
								
								$row_end = $row_counter - 1;
								$sheet_route->mergeCells('A' . $row_start . ':A' . $row_end);
								$sheet_route->mergeCells('B' . $row_start . ':B' . $row_end);
								$sheet_route->mergeCells('C' . $row_start . ':C' . $row_end);
								$sheet_route->mergeCells('D' . $row_start . ':D' . $row_end);
								$sheet_route->mergeCells('E' . $row_start . ':E' . $row_end);
								$sheet_route->mergeCells('F' . $row_start . ':F' . $row_end);
								$sheet_route->mergeCells('G' . $row_start . ':G' . $row_end);
							}
							
							//下载文件
							$writer_xls = PHPExcel_IOFactory::createWriter($xls_export, 'Excel2007');
							header("Pragma: public");
							header("Expires: 0");
							header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
							header("Content-Type: application/force-download");
							header("Content-Type: application/octet-stream");
							header("Content-Type: application/download");
							header("Content-Disposition: attachment;filename=" . "export_route_review.xlsx");
							header("Content-Transfer-Encoding: binary ");
							$writer_xls->save('php://output');
							
							$xls_export->disconnectWorksheets();
							unset($writer_xls);
							unset($sheet_route);
							unset($xls_export);
							
							exit;
						} elseif($_POST['export_model'] == 'backup') {
							//备份模式导出
							//读取模板
							$xls_export = PHPExcel_IOFactory::load(DOCROOT . '/assets/xls/model/import_route_model.xls');
							$sheet_route = $xls_export->getSheetByName('旅游路线');
							$sheet_detail = $xls_export->getSheetByName('详细日程');
							
							//写入旅游路线信息
							$row_route_counter = 3;
							$row_detail_counter = 3;
							foreach($route_list as $route) {
								$sheet_route->setCellValue('A' . $row_route_counter, $route['route_name']);
								$sheet_route->setCellValue('B' . $row_route_counter, $route['route_description']);
								$sheet_route->setCellValue('C' . $row_route_counter, $route['route_price_min']);
								$sheet_route->setCellValue('D' . $row_route_counter, $route['route_price_max']);
								$sheet_route->setCellValue('E' . $row_route_counter, $route['route_base_cost']);
								$sheet_route->setCellValue('F' . $row_route_counter, $route['route_parking_cost']);
								$sheet_route->setCellValue('G' . $row_route_counter, $route['route_traffic_cost']);
								
								foreach($route['detail_list'] as $route_detail) {
									$sheet_detail->setCellValue('A' . $row_detail_counter, $route['route_name']);
									$sheet_detail->setCellValue('B' . $row_detail_counter, $route_detail['route_detail_day']);
									$sheet_detail->setCellValue('C' . $row_detail_counter, $route_detail['route_detail_title']);
									$sheet_detail->setCellValue('D' . $row_detail_counter, $route_detail['route_detail_content']);
									$sheet_detail->setCellValue('E' . $row_detail_counter, $route_detail['route_breakfast']);
									$sheet_detail->setCellValue('F' . $row_detail_counter, $route_detail['route_lunch']);
									$sheet_detail->setCellValue('G' . $row_detail_counter, $route_detail['route_dinner']);
									$sheet_detail->setCellValue('H' . $row_detail_counter, $route_detail['route_hotel']);
									$spot_name_list = array();
									foreach($route_detail['route_spot_list'] as $route_spot) {
										$spot_name_list[] = $route_spot['spot_name'];
									}
									$sheet_detail->setCellValue('I' . $row_detail_counter, implode(';', $spot_name_list));
									$row_detail_counter++;
								}
								
								$row_route_counter++;
							}
							
							//下载文件
							$writer_xls = PHPExcel_IOFactory::createWriter($xls_export, 'Excel2007');
							header("Pragma: public");
							header("Expires: 0");
							header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
							header("Content-Type: application/force-download");
							header("Content-Type: application/octet-stream");
							header("Content-Type: application/download");
							header("Content-Disposition: attachment;filename=" . "export_route_backup.xlsx");
							header("Content-Transfer-Encoding: binary ");
							$writer_xls->save('php://output');
							
							$xls_export->disconnectWorksheets();
							unset($writer_xls);
							unset($sheet_route);
							unset($sheet_detail);
							unset($xls_export);
							
							exit;
						} else {
							//导出模式无法识别
							$_SESSION['export_route_error'] = 'error_system';
						}
					} else {
						//未能取得任何景点信息
						$_SESSION['export_route_error'] = 'empty_route_list';
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