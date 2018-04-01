<?php
/* 
 * 导出支出记录
 */

class Controller_Admin_Financial_Cost_Exportcost extends Controller_Admin_App
{

	/**
	 * 导出支出记录
	 * @access  public
	 * @return  Response
	 */
	public function action_index($params = null)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/cost_list/';
		
		try {
			if(!isset($_POST['page'])) {
				//未指明来源页
				$_SESSION['export_cost_error'] = 'error_system';
			} else {
				if(!isset($_POST['export_model'])) {
					//未设定导出模式
					$_SESSION['export_cost_error'] = 'error_system';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 33)) {
					//当前登陆用户不具备导出支出记录的权限
					$_SESSION['export_cost_error'] = 'error_permission';
				} else {
					//Excel处理用组件
					include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel.php');
					include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
					
					$select_cost_desc_tmp = isset($_POST['select_cost_desc']) ? preg_replace('/( |　)/', ' ', $_POST['select_cost_desc']) : '';
					
					//获取支出记录信息
					$params_select = array(
						'cost_desc' => trim($select_cost_desc_tmp) ? explode(' ', $select_cost_desc_tmp) : array(),
						'cost_type' => isset($_POST['select_cost_type']) ? ($_POST['select_cost_type'] ? explode(',', $_POST['select_cost_type']) : array()) : array(),
						'price_min' => isset($_POST['select_price_min']) ? $_POST['select_price_min'] : '',
						'price_max' => isset($_POST['select_price_max']) ? $_POST['select_price_max'] : '',
						'cost_at_min' => isset($_POST['select_cost_at_min']) ? $_POST['select_cost_at_min'] : '',
						'cost_at_max' => isset($_POST['select_cost_at_max']) ? $_POST['select_cost_at_max'] : '',
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
					
					$result_select = Model_Cost::SelectCostList($params_select);
					
					if($result_select) {
						$cost_list = $result_select['cost_list'];
						if($_POST['export_model'] == 'review') {
							//阅览模式导出
							$xls_export = new PHPExcel();
							$xls_export->getProperties()->setCreator('O2H Information Manage System');
							$xls_export->setActiveSheetIndex(0);
							$sheet_cost = $xls_export->getActiveSheet();
							$sheet_cost->setTitle('支出记录');
							
							//设定自动换行并改变列宽
							$sheet_cost->getDefaultStyle()->getAlignment()->setWrapText(true);
							$sheet_cost->getColumnDimension('A')->setWidth( 16 );
							$sheet_cost->getColumnDimension('B')->setWidth( 40 );
							$sheet_cost->getColumnDimension('C')->setWidth( 16 );
							$sheet_cost->getColumnDimension('D')->setWidth( 40 );
							$sheet_cost->getColumnDimension('E')->setWidth( 12 );
							$sheet_cost->getColumnDimension('F')->setWidth( 8 );
							$sheet_cost->getColumnDimension('G')->setWidth( 12 );
							
							//设定表头
							$sheet_cost->setCellValue('A1', '支出项目');
							$sheet_cost->setCellValue('B1', '支出说明');
							$sheet_cost->setCellValue('C1', '支出日期');
							$sheet_cost->setCellValue('D1', '支出明细');
							$sheet_cost->setCellValue('D2', '款项');
							$sheet_cost->setCellValue('E2', '单价');
							$sheet_cost->setCellValue('F2', '数量');
							$sheet_cost->setCellValue('G2', '小计');
							$sheet_cost->mergeCells('A1:A2');
							$sheet_cost->mergeCells('B1:B2');
							$sheet_cost->mergeCells('C1:C2');
							$sheet_cost->mergeCells('D1:G1');
							
							//写入支出记录信息
							$row_counter = 3;
							foreach($cost_list as $cost) {
								$row_start = $row_counter;
								$sheet_cost->setCellValue('A' . $row_counter, $cost['cost_type_name']);
								$sheet_cost->setCellValue('B' . $row_counter, $cost['cost_desc']);
								$sheet_cost->setCellValue('C' . $row_counter, date('Y年m月d日', strtotime($cost['cost_at'])));
								
								if(count($cost['cost_detail_list'])) {
									foreach($cost['cost_detail_list'] as $cost_detail) {
										$sheet_cost->setCellValue('D' . $row_counter, $cost_detail['cost_detail_desc']);
										$sheet_cost->setCellValue('E' . $row_counter, $cost_detail['cost_detail_each']);
										$sheet_cost->setCellValue('F' . $row_counter, $cost_detail['cost_detail_count']);
										$sheet_cost->setCellValue('G' . $row_counter, $cost_detail['cost_detail_total']);
										$row_counter++;
									}
								}
								$sheet_cost->setCellValue('D' . $row_counter, '合计金额');
								$sheet_cost->setCellValue('E' . $row_counter, $cost['cost_price']);
								$sheet_cost->mergeCells('E' . $row_counter . ':G' . $row_counter);
								
								$row_end = $row_counter;
								$sheet_cost->mergeCells('A' . $row_start . ':A' . $row_end);
								$sheet_cost->mergeCells('B' . $row_start . ':B' . $row_end);
								$sheet_cost->mergeCells('C' . $row_start . ':C' . $row_end);
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
							header("Content-Disposition: attachment;filename=" . "export_cost.xlsx");
							header("Content-Transfer-Encoding: binary ");
							$writer_xls->save('php://output');
							
							$xls_export->disconnectWorksheets();
							unset($writer_xls);
							unset($sheet_cost);
							unset($xls_export);
							
							exit;
						} else {
							//导出模式无法识别
							$_SESSION['export_cost_error'] = 'error_system';
						}
					} else {
						//未能取得任何支出记录信息
						$_SESSION['export_cost_error'] = 'empty_cost_list';
					}
				}
				
				//页面返回目标
				switch($_POST['page']) {
					case 'cost_list':
						if(isset($_SERVER['HTTP_REFERER'])) {
							if(strstr($_SERVER['HTTP_REFERER'], 'admin/cost_list')) {
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
			$_SESSION['import_cost_error'] = 'error_system';
		}
		
		header('Location: ' . $header_url);
		exit;
	}

}