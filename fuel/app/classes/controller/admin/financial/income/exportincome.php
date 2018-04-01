<?php
/* 
 * 导出收入记录
 */

class Controller_Admin_Financial_Income_Exportincome extends Controller_Admin_App
{

	/**
	 * 导出收入记录
	 * @access  public
	 * @return  Response
	 */
	public function action_index($params = null)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/income_list/';
		
		try {
			if(!isset($_POST['page'])) {
				//未指明来源页
				$_SESSION['export_income_error'] = 'error_system';
			} else {
				if(!isset($_POST['export_model'])) {
					//未设定导出模式
					$_SESSION['export_income_error'] = 'error_system';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 33)) {
					//当前登陆用户不具备导出收入记录的权限
					$_SESSION['export_income_error'] = 'error_permission';
				} else {
					//Excel处理用组件
					include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel.php');
					include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
					
					$select_income_desc_tmp = isset($_POST['select_income_desc']) ? preg_replace('/( |　)/', ' ', $_POST['select_income_desc']) : '';
					
					//获取收入记录信息
					$params_select = array(
						'income_desc' => trim($select_income_desc_tmp) ? explode(' ', $select_income_desc_tmp) : array(),
						'income_type' => isset($_POST['select_income_type']) ? ($_POST['select_income_type'] ? explode(',', $_POST['select_income_type']) : array()) : array(),
						'price_min' => isset($_POST['select_price_min']) ? $_POST['select_price_min'] : '',
						'price_max' => isset($_POST['select_price_max']) ? $_POST['select_price_max'] : '',
						'income_at_min' => isset($_POST['select_income_at_min']) ? $_POST['select_income_at_min'] : '',
						'income_at_max' => isset($_POST['select_income_at_max']) ? $_POST['select_income_at_max'] : '',
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
					
					$result_select = Model_Income::SelectIncomeList($params_select);
					
					if($result_select) {
						$income_list = $result_select['income_list'];
						if($_POST['export_model'] == 'review') {
							//阅览模式导出
							$xls_export = new PHPExcel();
							$xls_export->getProperties()->setCreator('O2H Information Manage System');
							$xls_export->setActiveSheetIndex(0);
							$sheet_income = $xls_export->getActiveSheet();
							$sheet_income->setTitle('收入记录');
							
							//设定自动换行并改变列宽
							$sheet_income->getDefaultStyle()->getAlignment()->setWrapText(true);
							$sheet_income->getColumnDimension('A')->setWidth( 16 );
							$sheet_income->getColumnDimension('B')->setWidth( 40 );
							$sheet_income->getColumnDimension('C')->setWidth( 16 );
							$sheet_income->getColumnDimension('D')->setWidth( 40 );
							$sheet_income->getColumnDimension('E')->setWidth( 12 );
							$sheet_income->getColumnDimension('F')->setWidth( 8 );
							$sheet_income->getColumnDimension('G')->setWidth( 12 );
							
							//设定表头
							$sheet_income->setCellValue('A1', '收入项目');
							$sheet_income->setCellValue('B1', '收入说明');
							$sheet_income->setCellValue('C1', '收入日期');
							$sheet_income->setCellValue('D1', '收入明细');
							$sheet_income->setCellValue('D2', '款项');
							$sheet_income->setCellValue('E2', '单价');
							$sheet_income->setCellValue('F2', '数量');
							$sheet_income->setCellValue('G2', '小计');
							$sheet_income->mergeCells('A1:A2');
							$sheet_income->mergeCells('B1:B2');
							$sheet_income->mergeCells('C1:C2');
							$sheet_income->mergeCells('D1:G1');
							
							//写入收入记录信息
							$row_counter = 3;
							foreach($income_list as $income) {
								$row_start = $row_counter;
								$sheet_income->setCellValue('A' . $row_counter, $income['income_type_name']);
								$sheet_income->setCellValue('B' . $row_counter, $income['income_desc']);
								$sheet_income->setCellValue('C' . $row_counter, date('Y年m月d日', strtotime($income['income_at'])));
								
								if(count($income['income_detail_list'])) {
									foreach($income['income_detail_list'] as $income_detail) {
										$sheet_income->setCellValue('D' . $row_counter, $income_detail['income_detail_desc']);
										$sheet_income->setCellValue('E' . $row_counter, $income_detail['income_detail_each']);
										$sheet_income->setCellValue('F' . $row_counter, $income_detail['income_detail_count']);
										$sheet_income->setCellValue('G' . $row_counter, $income_detail['income_detail_total']);
										$row_counter++;
									}
								}
								$sheet_income->setCellValue('D' . $row_counter, '合计金额');
								$sheet_income->setCellValue('E' . $row_counter, $income['income_price']);
								$sheet_income->mergeCells('E' . $row_counter . ':G' . $row_counter);
								
								$row_end = $row_counter;
								$sheet_income->mergeCells('A' . $row_start . ':A' . $row_end);
								$sheet_income->mergeCells('B' . $row_start . ':B' . $row_end);
								$sheet_income->mergeCells('C' . $row_start . ':C' . $row_end);
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
							header("Content-Disposition: attachment;filename=" . "export_income.xlsx");
							header("Content-Transfer-Encoding: binary ");
							$writer_xls->save('php://output');
							
							$xls_export->disconnectWorksheets();
							unset($writer_xls);
							unset($sheet_income);
							unset($xls_export);
							
							exit;
						} else {
							//导出模式无法识别
							$_SESSION['export_income_error'] = 'error_system';
						}
					} else {
						//未能取得任何收入记录信息
						$_SESSION['export_income_error'] = 'empty_income_list';
					}
				}
				
				//页面返回目标
				switch($_POST['page']) {
					case 'income_list':
						if(isset($_SERVER['HTTP_REFERER'])) {
							if(strstr($_SERVER['HTTP_REFERER'], 'admin/income_list')) {
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
			$_SESSION['import_income_error'] = 'error_system';
		}
		
		header('Location: ' . $header_url);
		exit;
	}

}