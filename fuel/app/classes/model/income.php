<?php

class Model_Income extends Model
{

	/*
	 * 添加收入记录
	 */
	public static function InsertIncome($params) {
		try {
			//添加收入记录
			$sql_income = "INSERT INTO t_income(income_type, income_desc, income_price, income_at, "
						. "approval_status, delete_flag, created_at, created_by, modified_at, modified_by) "
						. "VALUES(:income_type, :income_desc, :income_price, :income_at, "
						. "0, 0, :created_at, :created_by, :modified_at, :modified_by)";
			$query_income = DB::query($sql_income);
			$query_income->param('income_type', $params['income_type']);
			$query_income->param('income_desc', $params['income_desc']);
			$query_income->param('income_price', $params['income_price']);
			$query_income->param('income_at', $params['income_at'] . ' 00:00:00');
			$time_now = date('Y-m-d H:i:s', time());
			$query_income->param('created_at', $time_now);
			$query_income->param('created_by', $params['created_by']);
			$query_income->param('modified_at', $time_now);
			$query_income->param('modified_by', $params['modified_by']);
			$result_income = $query_income->execute();
			
			if($result_income) {
				//新收入记录ID
				$income_id = intval($result_income[0]);
				
				//添加收入明细
				if(isset($params['income_detail_list'])) {
					$sql_values_detail = array();
					$sql_params_detail = array();
					foreach($params['income_detail_list'] as $param_key => $income_detail) {
						$sql_values_detail[] = "(:income_id, :row_id_" . $param_key . ", :income_detail_desc_" . $param_key . ", :income_detail_each_" . $param_key . ", " 
											. ":income_detail_count_" . $param_key . ", :income_detail_total_" . $param_key . ")";
						$sql_params_detail['row_id_' . $param_key] = $param_key;
						$sql_params_detail['income_detail_desc_' . $param_key] = $income_detail['income_detail_desc'] ? $income_detail['income_detail_desc'] : null;
						$sql_params_detail['income_detail_each_' . $param_key] = $income_detail['income_detail_each'] ? $income_detail['income_detail_each'] : null;
						$sql_params_detail['income_detail_count_' . $param_key] = $income_detail['income_detail_count'] ? $income_detail['income_detail_count'] : null;
						$sql_params_detail['income_detail_total_' . $param_key] = $income_detail['income_detail_total'] ? $income_detail['income_detail_total'] : null;
					}
					
					if(count($sql_values_detail)) {
						$sql_detail = "INSERT INTO e_income_detail(income_id, row_id, income_detail_desc, income_detail_each, income_detail_count, income_detail_total)"
									. " VALUES" . implode(",", $sql_values_detail);
						$query_detail = DB::query($sql_detail);
						$query_detail->param('income_id', $income_id);
						foreach($sql_params_detail as $param_key => $param_value) {
							$query_detail->param($param_key, $param_value);
						}
						$result_detail = $query_detail->execute();
					}
				}
				
				return $income_id;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 删除收入记录
	 */
	public static function DeleteIncome($params) {
		try {
			//删除收入记录
			$sql_income = "UPDATE t_income SET delete_flag = 1, modified_at=:modified_at, modified_by=:modified_by WHERE income_id IN :income_id_list";
			$query_income = DB::query($sql_income);
			$query_income->param('income_id_list', $params['income_id_list']);
			$query_income->param('modified_at', date('Y-m-d H:i:s', time()));
			$query_income->param('modified_by', $params['deleted_by']);
			$result_income = $query_income->execute();
			
			//删除收入明细
			$sql_detail = "DELETE FROM e_income_detail WHERE income_id IN :income_id_list";
			$query_detail = DB::query($sql_detail);
			$query_detail->param('income_id_list', $params['income_id_list']);
			$result_detail = $query_detail->execute();
			
			return $result_income;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新收入记录
	 */
	public static function UpdateIncome($params) {
		try {
			//更新收入记录
			$sql_income = "UPDATE t_income "
						. "SET income_type=:income_type, income_desc=:income_desc, income_price=:income_price, "
						. "income_at=:income_at, modified_at=:modified_at, modified_by=:modified_by "
						. "WHERE income_id=:income_id";
			$query_income = DB::query($sql_income);
			$query_income->param('income_id', $params['income_id']);
			$query_income->param('income_type', $params['income_type']);
			$query_income->param('income_desc', $params['income_desc']);
			$query_income->param('income_price', $params['income_price']);
			$query_income->param('income_at', $params['income_at']);
			$query_income->param('modified_at', date('Y-m-d H:i:s', time()));
			$query_income->param('modified_by', $params['modified_by']);
			$result_income = $query_income->execute();
			
			//删除原有收入明细
			if(isset($params['income_detail_list'])) {
				$sql_detail_delete = "DELETE FROM e_income_detail WHERE income_id=:income_id";
				$query_detail_delete = DB::query($sql_detail_delete);
				$query_detail_delete->param('income_id', $params['income_id']);
				$result_detail_delete = $query_detail_delete->execute();
				
				//添加收入明细
				$sql_values_detail = array();
				$sql_params_detail = array();
				foreach($params['income_detail_list'] as $param_key => $income_detail) {
					$sql_values_detail[] = "(:income_id, :row_id_" . $param_key . ", :income_detail_desc_" . $param_key . ", :income_detail_each_" . $param_key . ", " 
										. ":income_detail_count_" . $param_key . ", :income_detail_total_" . $param_key . ")";
					$sql_params_detail['row_id_' . $param_key] = $param_key;
					$sql_params_detail['income_detail_desc_' . $param_key] = $income_detail['income_detail_desc'] ? $income_detail['income_detail_desc'] : null;
					$sql_params_detail['income_detail_each_' . $param_key] = $income_detail['income_detail_each'] ? $income_detail['income_detail_each'] : null;
					$sql_params_detail['income_detail_count_' . $param_key] = $income_detail['income_detail_count'] ? $income_detail['income_detail_count'] : null;
					$sql_params_detail['income_detail_total_' . $param_key] = $income_detail['income_detail_total'] ? $income_detail['income_detail_total'] : null;
				}
				
				if(count($sql_values_detail)) {
					$sql_detail = "INSERT INTO e_income_detail(income_id, row_id, income_detail_desc, income_detail_each, income_detail_count, income_detail_total)"
								. " VALUES" . implode(",", $sql_values_detail);
					$query_detail = DB::query($sql_detail);
					$query_detail->param('income_id', $params['income_id']);
					foreach($sql_params_detail as $param_key => $param_value) {
						$query_detail->param($param_key, $param_value);
					}
					$result_detail = $query_detail->execute();
				}
			}
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新确认状态
	 */
	public static function UpdateApprovalStatus($params) {
		try {
			$sql = "UPDATE t_income SET approval_status = :approval_status, approval_at = :approval_at, approval_by = :approval_by WHERE income_id = :income_id";
			$query = DB::query($sql);
			$query->param('income_id', $params['income_id']);
			$query->param('approval_status', $params['approval_status']);
			$query->param('approval_at', date('Y-m-d H:i:s', time()));
			$query->param('approval_by', $params['approval_by']);
			$result = $query->execute();
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 按条件获得收入记录列表
	 */
	public static function SelectIncomeList($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			$sql_order_column = "created_at";
			$sql_order_method = "desc";
			$sql_limit = "";
			$sql_offset = "";
			
			foreach($params as $param_key => $param_value) {
				switch($param_key) {
					case 'income_id_list':
						if(count($param_value)) {
							$sql_where[] = " ti.income_id IN :income_id_list ";
							$sql_params['income_id_list'] = $param_value;
						}
						break;
					case 'income_desc':
						if(count($param_value)) {
							$sql_sub_where = array();
							foreach($param_value as $name_key => $name) {
								$sql_sub_where[] = "ti.income_desc LIKE :income_desc_" . $name_key;
								$sql_params['income_desc_' . $name_key] = '%' . $name . '%';
							}
							$sql_where[] = " (" . implode(" OR ", $sql_sub_where) . ") ";
						}
						break;
					case 'income_type':
						if(count($param_value)) {
							$sql_where[] = " ti.income_type IN :income_type_list ";
							$sql_params['income_type_list'] = $param_value;
						}
						break;
					case 'price_min':
						if(is_numeric($param_value)) {
							$sql_where[] = " ti.income_price >= :price_min ";
							$sql_params['price_min'] = floatval($param_value);
						}
						break;
					case 'price_max':
						if(is_numeric($param_value)) {
							$sql_where[] = " ti.income_price <= :price_max ";
							$sql_params['price_max'] = floatval($param_value);
						}
						break;
					case 'income_at_min':
						if(strtotime($param_value)) {
							$sql_where[] = " ti.income_at >= :income_at_min ";
							$sql_params['income_at_min'] = date('Y-m-d', strtotime($param_value)) . ' 00:00:00';
						}
						break;
					case 'income_at_max':
						if(strtotime($param_value)) {
							$sql_where[] = " ti.income_at <= :income_at_max ";
							$sql_params['income_at_max'] = date('Y-m-d', strtotime($param_value)) . ' 23:59:59';
						}
						break;
					case 'created_by':
						$sql_where[] = " ti.created_by = :created_by ";
						$sql_params['created_by'] = $param_value;
						break;
					case 'active_only':
						$sql_where[] = " ti.delete_flag = 0 ";
						break;
					case 'sort_column':
						$sort_column_list = array('income_type', 'income_price', 'income_at', 'created_at', 'modified_at');
						if(in_array($param_value, $sort_column_list)) {
							$sql_order_column = $param_value;
						}
						break;
					case 'sort_method':
						if(in_array($param_value, array('asc', 'desc'))) {
							$sql_order_method = $param_value;
						}
						break;
					case '':
						break;
				}
			}
			
			if(isset($params['num_per_page']) && isset($params['page'])) {
				$sql_limit = intval($params['num_per_page']);
				$sql_offset = (intval($params['page']) - 1) * $sql_limit;
				$sql_limit = " LIMIT " . $sql_limit;
				$sql_offset = " OFFSET " . $sql_offset;
			}
			
			//符合条件的收入记录总数获取
			$sql_count = "SELECT COUNT(DISTINCT ti.income_id) income_count "
						. "FROM t_income ti "
						. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");
			$query_count = DB::query($sql_count);
			foreach ($sql_params as $param_key => $param_value) {
				$query_count->param($param_key, $param_value);
			}
			$result_count = $query_count->execute()->as_array();
			
			if(count($result_count)) {
				$income_count = intval($result_count[0]['income_count']);
				
				if($income_count) {
					//收入记录信息获取
					$sql_income = "SELECT ti.*, mit.income_type_name " 
							. "FROM t_income ti " 
							. "LEFT JOIN m_income_type mit ON ti.income_type = mit.income_type_id "
							. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "")
							. "ORDER BY " . $sql_order_column . " " . $sql_order_method . " "
							. $sql_limit . $sql_offset;
					$query_income = DB::query($sql_income);
					foreach ($sql_params as $param_key => $param_value) {
						$query_income->param($param_key, $param_value);
					}
					$result_income = $query_income->execute()->as_array();
					
					if(count($result_income)) {
						$income_list = array();
						$income_id_list = array();
						foreach($result_income as $income) {
							$income_list[$income['income_id']] = $income;
							$income_list[$income['income_id']]['income_detail_list'] = array();
							$income_id_list[] = intval($income['income_id']);
						}
						
						//收入明细信息获取
						if(isset($params['detail_flag'])) {
							$sql_detail = "SELECT eid.* "
									. "FROM e_income_detail eid "
									. "WHERE eid.income_id IN :income_id_list "
									. "ORDER BY eid.income_id ASC, eid.row_id ASC";
							$query_detail = DB::query($sql_detail);
							$query_detail->param('income_id_list', $income_id_list);
							$result_detail = $query_detail->execute()->as_array();
							
							if(count($result_detail)) {
								foreach($result_detail as $income_detail) {
									$income_list[$income_detail['income_id']]['income_detail_list'][] = $income_detail;
								}
							}
						}
						
						//返回值整理
						$result = array(
							'income_count' => $income_count,
							'income_list' => $income_list,
							'start_number' => $sql_offset + 1,
							'end_number' => count($income_count) + $sql_offset,
						);
						return $result;
					}
				}
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 获取特定单个收入记录信息
	 */
	public static function SelectIncome($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//收入ID限定
			if(isset($params['income_id'])) {
				$sql_where[] = " ti.income_id = :income_id ";
				$sql_params['income_id'] = $params['income_id'];
			}
			//有效性限定
			if(isset($params['active_only'])) {
				if($params['active_only']) {
					$sql_where[] = " ti.delete_flag = 0 ";
				}
			}
			
			//数据获取
			$sql_income = "SELECT ti.*, mit.income_type_name, tuc.user_name created_name, tum.user_name modified_name, tua.user_name approval_name " 
					. "FROM t_income ti " 
					. "LEFT JOIN m_income_type mit ON ti.income_type = mit.income_type_id " 
					. "LEFT JOIN t_user tuc ON ti.created_by = tuc.user_id " 
					. "LEFT JOIN t_user tum ON ti.modified_by = tum.user_id " 
					. "LEFT JOIN t_user tua ON ti.approval_by = tua.user_id " 
					. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");
			$query_income = DB::query($sql_income);
			foreach($sql_params as $param_key => $param_value) {
				$query_income->param($param_key, $param_value);
			}
			$result_income = $query_income->execute()->as_array();
			
			if(count($result_income) == 1) {
				$result = $result_income[0];
				
				//获取收入明细
				$sql_detail = "SELECT eid.* "
							. "FROM e_income_detail eid "
							. "WHERE eid.income_id = :income_id "
							. "ORDER BY eid.row_id ASC ";
				$query_detail = DB::query($sql_detail);
				$query_detail->param('income_id', $result['income_id']);
				$result_detail = $query_detail->execute()->as_array();
				$result['income_detail_list'] = $result_detail;
				
				return $result;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 编辑收入记录前编辑信息查验
	 */
	public static function CheckEditIncome($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		//收入项目
		if(empty($params['income_type'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_income_type';
		} elseif(!is_numeric($params['income_type']) || !is_int($params['income_type'] + 0)) {
			$result['result'] = false;
			$result['error'][] = 'noint_income_type';
		} elseif(!Model_Incometype::CheckIncomeTypeIdExist($params['income_type'], 1)) {
			$result['result'] = false;
			$result['error'][] = 'error_income_type';
		}
		
		//收入说明
		if(empty($params['income_desc'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_income_desc';
		}
		
		//收入日期
		if(empty($params['income_at'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_income_at';
		} elseif(!preg_match('/^\d{4}\/\d{1,2}\/\d{1,2}$/', $params['income_at'])) {
			$result['result'] = false;
			$result['error'][] = 'format_income_at';
		} else {
			list($year, $month, $day) = explode('/', $params['income_at']);
			if(!checkdate(intval($month), intval($day), intval($year))) {
				$result['result'] = false;
				$result['error'][] = 'error_income_at';
			}
		}
		
		//收入明细
		if(isset($params['income_detail_list'])) {
			if(!is_array($params['income_detail_list'])) {
				$result['result'] = false;
				$result['error'][] = 'noarray_income_detail_list';
			} elseif(!count($params['income_detail_list'])) {
				$result['result'] = false;
				$result['error'][] = 'empty_income_detail_list';
			} else {
				foreach($params['income_detail_list'] as $income_detail) {
					//款项
					if(empty($income_detail['income_detail_desc'])) {
						$result['result'] = false;
						$result['error'][] = 'empty_income_detail_desc';
					}
					
					//单价
					if(empty($income_detail['income_detail_each'])) {
						$result['result'] = false;
						$result['error'][] = 'empty_income_detail_each';
					} elseif(!preg_match('/^(\d+)(\.\d{1,2})?$/', $income_detail['income_detail_each'])) {
						$result['result'] = false;
						$result['error'][] = 'error_income_detail_each';
					}
					
					//数量
					if(empty($income_detail['income_detail_count'])) {
						$result['result'] = false;
						$result['error'][] = 'empty_income_detail_count';
					} elseif(!preg_match('/^(\d+)(\.\d+)?$/', $income_detail['income_detail_count'])) {
						$result['result'] = false;
						$result['error'][] = 'error_income_detail_count';
					}
				}
			}
		}
		
		return $result;
	}
	
	/*
	 * 删除收入记录前删除信息查验
	 */
	public static function CheckDeleteIncome($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_array($params['income_id_list'])) {
			$result['result'] = false;
			$result['error'][] = 'noarray_income_id';
		} elseif(!count($params['income_id_list'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_income_id';
		} else {
			$all_num_flag = true;
			
			foreach($params['income_id_list'] as $income_id) {
				if(!is_numeric($income_id)) {
					$result['result'] = false;
					$all_num_flag = false;
					$result['error'][] = 'nonum_income_id';
					break;
				}
			}
			
			if($all_num_flag) {
				$params_select = array('income_id_list' => $params['income_id_list']);
				$result_select = Model_Income::SelectIncomeList($params_select);
				
				if($result_select['income_count'] != count(array_unique($params['income_id_list']))) {
					$result['result'] = false;
					$result['error'][] = 'error_income_id';
				} elseif($params['self_only']) {
					foreach($result_select['income_list'] as $income_select) {
						if($income_select['approval_status']) {
							$result['result'] = false;
							$result['error'][] = 'error_status';
							break;
						} elseif($params['delete_by'] != $income_select['created_by']) {
							$result['result'] = false;
							$result['error'][] = 'error_creator';
							break;
						}
					}
				}
			}
		}
		
		return $result;
	}
	
	/*
	 * 更新收入记录确认状态前更新信息查验
	 */
	public static function CheckUpdateApprovalStatus($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!in_array($params['approval_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_approval_status';
		}
		
		return $result;
	}

}
