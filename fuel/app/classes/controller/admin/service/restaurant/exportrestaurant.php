<?php
/* 
 * 导出餐饮店
 */

class Controller_Admin_Service_Restaurant_Exportrestaurant extends Controller_Admin_App
{

	/**
	 * 导出餐饮店
	 * @access  public
	 * @return  Response
	 */
	public function action_index($page = 1)
	{
		$header_url = '//' . $_SERVER['HTTP_HOST'] . '/admin/restaurant_list/';
		
		try {
			if(!isset($_POST['page'])) {
				//未指明来源页
				$_SESSION['export_restaurant_error'] = 'error_system';
			} else {
				if(!isset($_POST['export_model'])) {
					//未设定导出模式
					$_SESSION['export_restaurant_error'] = 'error_system';
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 14)) {
					//当前登陆用户不具备导出餐饮店的权限
					$_SESSION['export_restaurant_error'] = 'error_permission';
				} else {
					//Excel处理用组件
					include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel.php');
					include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
					
					$select_name_tmp = isset($_POST['select_name']) ? preg_replace('/( |　)/', ' ', $_POST['select_name']) : '';
					
					//获取餐饮店信息
					$params_select = array(
						'restaurant_name' => trim($select_name_tmp) ? explode(' ', $select_name_tmp) : array(),
						'restaurant_status' => isset($_POST['select_status']) ? ($_POST['select_status'] ? explode(',', $_POST['select_status']) : array()) : array(),
						'restaurant_area' => isset($_POST['select_area']) ? ($_POST['select_area'] ? explode(',', $_POST['select_area']) : array()) : array(),
						'restaurant_type' => isset($_POST['select_restaurant_type']) ? ($_POST['select_restaurant_type'] ? explode(',', $_POST['select_restaurant_type']) : array()) : array(),
						'price_min' => isset($_POST['select_price_min']) ? $_POST['select_price_min'] : '',
						'price_max' => isset($_POST['select_price_max']) ? $_POST['select_price_max'] : '',
						'sort_column' => isset($_POST['sort_column']) ? $_POST['sort_column'] : 'created_at',
						'sort_method' => isset($_POST['sort_method']) ? $_POST['sort_method'] : 'desc',
						'active_only' => true,
					);
					if(isset($_POST['select_self_flag'])) {
						if($_POST['select_self_flag']) {
							$params_select['created_by'] = $_SESSION['login_user']['id'];
						}
					}
					
					$result_select = Model_Restaurant::SelectRestaurantList($params_select);
					
					if($result_select) {
						$restaurant_list = $result_select['restaurant_list'];
						
						if($_POST['export_model'] == 'review') {
							//阅览模式导出
							$xls_export = new PHPExcel();
							$xls_export->getProperties()->setCreator('O2H Information Manage System');
							$xls_export->setActiveSheetIndex(0);
							$sheet_restaurant = $xls_export->getActiveSheet();
							$sheet_restaurant->setTitle('餐饮店');
							
							//设定自动换行并改变列宽
							$sheet_restaurant->getDefaultStyle()->getAlignment()->setWrapText(true);
							$sheet_restaurant->getColumnDimension('A')->setWidth( 20 );
							$sheet_restaurant->getColumnDimension('B')->setWidth( 12 );
							$sheet_restaurant->getColumnDimension('C')->setWidth( 12 );
							$sheet_restaurant->getColumnDimension('D')->setWidth( 20 );
							
							//设定表头
							$sheet_restaurant->setCellValue('A1', '餐饮店名');
							$sheet_restaurant->setCellValue('B1', '餐饮店地区');
							$sheet_restaurant->setCellValue('C1', '餐饮店类别');
							$sheet_restaurant->setCellValue('D1', '价格(日元/人)');
							
							//写入餐饮店信息
							$row_counter = 2;
							foreach($restaurant_list as $restaurant) {
								$sheet_restaurant->setCellValue('A' . $row_counter, $restaurant['restaurant_name']);
								$sheet_restaurant->setCellValue('B' . $row_counter, $restaurant['restaurant_area_name']);
								$sheet_restaurant->setCellValue('C' . $row_counter, $restaurant['restaurant_type_name']);
								$sheet_restaurant->setCellValue('D' . $row_counter, $restaurant['restaurant_price_min'] . '～' . $restaurant['restaurant_price_max']);
								
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
							header("Content-Disposition: attachment;filename=" . "export_restaurant_review.xlsx");
							header("Content-Transfer-Encoding: binary ");
							$writer_xls->save('php://output');
							
							$xls_export->disconnectWorksheets();
							unset($writer_xls);
							unset($sheet_restaurant);
							unset($xls_export);
							
							exit;
						} elseif($_POST['export_model'] == 'backup') {
							//备份模式导出
							//读取模板
							$xls_export = PHPExcel_IOFactory::load(DOCROOT . '/assets/xls/model/import_restaurant_model.xls');
							$sheet_restaurant = $xls_export->getSheetByName('餐饮店');
							
							//写入餐饮店信息
							$row_counter = 3;
							foreach($restaurant_list as $restaurant) {
								$sheet_restaurant->setCellValue('A' . $row_counter, $restaurant['restaurant_name']);
								$sheet_restaurant->setCellValue('B' . $row_counter, $restaurant['restaurant_area_name']);
								$sheet_restaurant->setCellValue('C' . $row_counter, $restaurant['restaurant_type_name']);
								$sheet_restaurant->setCellValue('D' . $row_counter, $restaurant['restaurant_price_min']);
								$sheet_restaurant->setCellValue('E' . $row_counter, $restaurant['restaurant_price_max']);
								
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
							header("Content-Disposition: attachment;filename=" . "export_restaurant_backup.xlsx");
							header("Content-Transfer-Encoding: binary ");
							$writer_xls->save('php://output');
							
							$xls_export->disconnectWorksheets();
							unset($writer_xls);
							unset($sheet_restaurant);
							unset($xls_export);
							
							exit;
						} else {
							//导出模式无法识别
							$_SESSION['export_restaurant_error'] = 'error_system';
						}
					} else {
						//未能取得任何餐饮店信息
						$_SESSION['export_restaurant_error'] = 'empty_restaurant_list';
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