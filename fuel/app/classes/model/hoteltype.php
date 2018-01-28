<?php

class Model_Hoteltype extends Model
{

	/*
	 * 添加酒店类别
	 */
	public static function InsertHotelType($params) {
		try {
			$sql = "INSERT INTO m_hotel_type(hotel_type_name, delete_flag, sort_id) VALUES(:hotel_type_name, 0, 1)";
			$query = DB::query($sql);
			$query->param('hotel_type_name', $params['hotel_type_name']);
			$result = $query->execute();
			
			if($result) {
				//新酒店类别ID
				$hotel_type_id = intval($result[0]);
				return $hotel_type_id;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 删除酒店类别
	 */
	public static function DeleteHotelType($params) {
		try {
			//删除酒店类别
			$sql_type = "UPDATE m_hotel_type SET delete_flag = 1 WHERE hotel_type_id = :hotel_type_id";
			$query_type = DB::query($sql_type);
			$query_type->param('hotel_type_id', $params['hotel_type_id']);
			$result_type = $query_type->execute();
			
			return $result_type;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新酒店类别名称
	 */
	public static function UpdateHotelType($params) {
		try {
			$sql = "UPDATE m_hotel_type SET hotel_type_name = :hotel_type_name WHERE hotel_type_id = :hotel_type_id";
			$query = DB::query($sql);
			$query->param('hotel_type_id', $params['hotel_type_id']);
			$query->param('hotel_type_name', $params['hotel_type_name']);
			$result = $query->execute();
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	//获得符合特定条件的酒店类别
	public static function SelectHotelTypeList($params) {
		try{
			$sql_select = array();
			$sql_from = array();
			$sql_where = array();
			$sql_group_by = array();
			$sql_params = array();
			
			//有效酒店类别限定
			if(isset($params['active_only'])) {
				$sql_where[] = " mst.delete_flag = 0 ";
			}
			//获取所属酒店数
			if(isset($params['hotel_count_flag'])) {
				$sql_select[] = " COUNT(ts.hotel_id) hotel_count ";
				$sql_from[] = " LEFT JOIN (SELECT * FROM t_hotel WHERE delete_flag=0) ts ON ts.hotel_type = mst.hotel_type_id ";
				$sql_group_by[] = " mst.hotel_type_id ";
			}
			
			$sql = "SELECT mst.* " . (count($sql_select) ? (", " . implode(", ", $sql_select)) : "") 
				. "FROM m_hotel_type mst " . (count($sql_from) ? implode(" ", array_unique($sql_from)) : "") 
				. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "") 
				. (count($sql_group_by) ? (" GROUP BY " . implode(", ", array_unique($sql_group_by))) : "") 
				. " ORDER BY mst.sort_id, mst.hotel_type_id";
			$query = DB::query($sql);
			foreach($sql_params as $param_key => $param_value) {
				$query->param($param_key, $param_value);
			}
			$result = $query->execute()->as_array();
			
			return $result;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 获取特定单个酒店类别信息
	 */
	public static function SelectHotelType($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//酒店类别ID限定
			if(isset($params['hotel_type_id'])) {
				$sql_where[] = " mst.hotel_type_id = :hotel_type_id ";
				$sql_params['hotel_type_id'] = $params['hotel_type_id'];
			}
			//有效性限定
			if(isset($params['active_only'])) {
				if($params['active_only']) {
					$sql_where[] = " mst.delete_flag = 0 ";
				}
			}
			
			//数据获取
			$sql = "SELECT * FROM m_hotel_type mst " 
				. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");
			$query = DB::query($sql);
			foreach($sql_params as $param_key => $param_value) {
				$query->param($param_key, $param_value);
			}
			$result = $query->execute()->as_array();
			
			if(count($result) == 1) {
				return $result[0];
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 编辑酒店类别前编辑信息查验
	 */
	public static function CheckEditHotelType($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		//酒店类别名称
		if(empty($params['hotel_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_hotel_type_name';
		} elseif(mb_strlen($params['hotel_type_name']) > 50) {
			$result['result'] = false;
			$result['error'][] = 'long_hotel_type_name';
		} elseif(Model_Hoteltype::CheckHotelTypeNameDuplication($params['hotel_type_id'], $params['hotel_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'dup_hotel_type_name';
		}
		
		return $result;
	}
	
	/*
	 * 删除酒店类别前删除信息查验
	 */
	public static function CheckDeleteHotelType($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($params['hotel_type_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_hotel_type_id';
		} elseif(!Model_Hoteltype::CheckHotelTypeIdExist($params['hotel_type_id'], 1)) {
			$result['result'] = false;
			$result['error'][] = 'error_hotel_type_id';
		} else {
			//获取酒店信息
			$params_select = array(
				'hotel_type' => array($params['hotel_type_id']),
				'active_only' => 1,
			);
			$hotel_select = Model_Hotel::SelectHotelList($params_select);
			
			if($hotel_select['hotel_count']) {
				$result['result'] = false;
				$result['error'][] = 'error_hotel_list';
			}
		}
		
		return $result;
	}
	
	/*
	 * 检查酒店类别ID是否存在
	 */
	public static function CheckHotelTypeIdExist($hotel_type_id, $active_check = 0) {
		try {
			$sql = "SELECT hotel_type_id FROM m_hotel_type WHERE hotel_type_id = :hotel_type_id " . ($active_check ? " AND delete_flag = 0 " : "");
			$query = DB::query($sql);
			$query->param('hotel_type_id', $hotel_type_id);
			$result = $query->execute()->as_array();
			
			if(count($result)) {
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 酒店类别名称重复查验
	 */
	public static function CheckHotelTypeNameDuplication($hotel_type_id, $hotel_type_name) {
		try {
			//数据获取
			$sql = "SELECT hotel_type_id FROM m_hotel_type WHERE hotel_type_name = :hotel_type_name AND delete_flag = 0" . ($hotel_type_id ? " AND hotel_type_id != :hotel_type_id " : "");
			$query = DB::query($sql);
			if($hotel_type_id) {
				$query->param('hotel_type_id', $hotel_type_id);
			}
			$query->param('hotel_type_name', $hotel_type_name);
			$result = $query->execute()->as_array();
			
			if(count($result)) {
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return true;
		}
	}
	
	/*
	 * 酒店信息上传用模板Excel更新
	 */
	public static function ModifyHotelModelExcel() {
		try {
			//修改酒店上传用模板Excel
			//Excel处理用组件
			include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel.php');
			include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
			
			//读取模板
			$xls_model = PHPExcel_IOFactory::load(DOCROOT . '/assets/xls/model/import_hotel_model.xls');
			$sheet_type = $xls_model->getSheetByName('hotel_type');
			
			//删除原有信息
			$sheet_type->removeColumn('A');
			
			//写入酒店类别名列表
			$hotel_type_list = Model_HotelType::SelectHotelTypeList(array('active_only' => 1));
			$row_counter = 1;
			foreach($hotel_type_list as $hotel_type) {
				$sheet_type->setCellValue('B' . $row_counter, $hotel_type['hotel_type_name']);
				$row_counter++;
			}
			
			//删除原有信息
			$sheet_type->removeColumn('A');
			
			//写入酒店类别名列表
			$hotel_type_list = Model_HotelType::SelectHotelTypeList(array('active_only' => 1));
			$row_counter = 1;
			foreach($hotel_type_list as $hotel_type) {
				$sheet_type->setCellValue('B' . $row_counter, $hotel_type['hotel_type_name']);
				$row_counter++;
			}
			
			//删除原有信息
			$sheet_type->removeColumn('A');
			
			//更新文件
			$writer_xls = PHPExcel_IOFactory::createWriter($xls_model, 'Excel2007');
			$writer_xls->save(DOCROOT . '/assets/xls/model/import_hotel_model.xls');
			
			//释放缓存
			$xls_model->disconnectWorksheets();
			unset($writer_xls);
			unset($sheet_type);
			unset($xls_model);
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

}

