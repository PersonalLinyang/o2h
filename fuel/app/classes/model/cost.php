<?php

class Model_Cost extends Model
{

	/*
	 * 添加支出记录
	 */
	public static function InsertCost($params) {
		try {
			//添加支出记录
			$sql_cost = "INSERT INTO t_cost(cost_type, cost_desc, cost_price, cost_at, "
						. "approval_status, delete_flag, created_at, created_by, modified_at, modified_by) "
						. "VALUES(:cost_type, :cost_desc, :cost_price, :cost_at, "
						. "0, 0, :created_at, :created_by, :modified_at, :modified_by)";
			$query_cost = DB::query($sql_cost);
			$query_cost->param('cost_type', $params['cost_type']);
			$query_cost->param('cost_desc', $params['cost_desc']);
			$query_cost->param('cost_price', $params['cost_price']);
			$query_cost->param('cost_at', $params['cost_at'] . ' 00:00:00');
			$time_now = date('Y-m-d H:i:s', time());
			$query_cost->param('created_at', $time_now);
			$query_cost->param('created_by', $params['created_by']);
			$query_cost->param('modified_at', $time_now);
			$query_cost->param('modified_by', $params['modified_by']);
			$result_cost = $query_cost->execute();
			
			if($result_cost) {
				//新支出记录ID
				$cost_id = intval($result_cost[0]);
				
				//添加支出明细
				if(isset($params['cost_detail_list'])) {
					$sql_values_detail = array();
					$sql_params_detail = array();
					foreach($params['cost_detail_list'] as $param_key => $cost_detail) {
						$sql_values_detail[] = "(:cost_id, :row_id_" . $param_key . ", :cost_detail_desc_" . $param_key . ", :cost_detail_each_" . $param_key . ", " 
											. ":cost_detail_count_" . $param_key . ", :cost_detail_total_" . $param_key . ")";
						$sql_params_detail['row_id_' . $param_key] = $param_key;
						$sql_params_detail['cost_detail_desc_' . $param_key] = $cost_detail['cost_detail_desc'] ? $cost_detail['cost_detail_desc'] : null;
						$sql_params_detail['cost_detail_each_' . $param_key] = $cost_detail['cost_detail_each'] ? $cost_detail['cost_detail_each'] : null;
						$sql_params_detail['cost_detail_count_' . $param_key] = $cost_detail['cost_detail_count'] ? $cost_detail['cost_detail_count'] : null;
						$sql_params_detail['cost_detail_total_' . $param_key] = $cost_detail['cost_detail_total'] ? $cost_detail['cost_detail_total'] : null;
					}
					
					if(count($sql_values_detail)) {
						$sql_detail = "INSERT INTO e_cost_detail(cost_id, row_id, cost_detail_desc, cost_detail_each, cost_detail_count, cost_detail_total)"
									. " VALUES" . implode(",", $sql_values_detail);
						$query_detail = DB::query($sql_detail);
						$query_detail->param('cost_id', $cost_id);
						foreach($sql_params_detail as $param_key => $param_value) {
							$query_detail->param($param_key, $param_value);
						}
						$result_detail = $query_detail->execute();
					}
				}
				
				return $cost_id;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 删除支出记录
	 */
	public static function DeleteCost($params) {
		try {
			//删除支出记录
			$sql_cost = "UPDATE t_cost SET delete_flag = 1, modified_at=:modified_at, modified_by=:modified_by WHERE cost_id IN :cost_id_list";
			$query_cost = DB::query($sql_cost);
			$query_cost->param('cost_id_list', $params['cost_id_list']);
			$query_cost->param('modified_at', date('Y-m-d H:i:s', time()));
			$query_cost->param('modified_by', $params['deleted_by']);
			$result_cost = $query_cost->execute();
			
			//删除支出明细
			$sql_detail = "DELETE FROM e_cost_detail WHERE cost_id IN :cost_id_list";
			$query_detail = DB::query($sql_detail);
			$query_detail->param('cost_id_list', $params['cost_id_list']);
			$result_detail = $query_detail->execute();
			
			return $result_cost;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新支出记录
	 */
	public static function UpdateCost($params) {
		try {
			//更新支出记录
			$sql_cost = "UPDATE t_cost "
						. "SET cost_type=:cost_type, cost_desc=:cost_desc, cost_price=:cost_price, "
						. "cost_at=:cost_at, modified_at=:modified_at, modified_by=:modified_by "
						. "WHERE cost_id=:cost_id";
			$query_cost = DB::query($sql_cost);
			$query_cost->param('cost_id', $params['cost_id']);
			$query_cost->param('cost_type', $params['cost_type']);
			$query_cost->param('cost_desc', $params['cost_desc']);
			$query_cost->param('cost_price', $params['cost_price']);
			$query_cost->param('cost_at', $params['cost_at']);
			$query_cost->param('modified_at', date('Y-m-d H:i:s', time()));
			$query_cost->param('modified_by', $params['modified_by']);
			$result_cost = $query_cost->execute();
			
			//删除原有支出明细
			if(isset($params['cost_detail_list'])) {
				$sql_detail_delete = "DELETE FROM e_cost_detail WHERE cost_id=:cost_id";
				$query_detail_delete = DB::query($sql_detail_delete);
				$query_detail_delete->param('cost_id', $params['cost_id']);
				$result_detail_delete = $query_detail_delete->execute();
				
				//添加支出明细
				$sql_values_detail = array();
				$sql_params_detail = array();
				foreach($params['cost_detail_list'] as $param_key => $cost_detail) {
					$sql_values_detail[] = "(:cost_id, :row_id_" . $param_key . ", :cost_detail_desc_" . $param_key . ", :cost_detail_each_" . $param_key . ", " 
										. ":cost_detail_count_" . $param_key . ", :cost_detail_total_" . $param_key . ")";
					$sql_params_detail['row_id_' . $param_key] = $param_key;
					$sql_params_detail['cost_detail_desc_' . $param_key] = $cost_detail['cost_detail_desc'] ? $cost_detail['cost_detail_desc'] : null;
					$sql_params_detail['cost_detail_each_' . $param_key] = $cost_detail['cost_detail_each'] ? $cost_detail['cost_detail_each'] : null;
					$sql_params_detail['cost_detail_count_' . $param_key] = $cost_detail['cost_detail_count'] ? $cost_detail['cost_detail_count'] : null;
					$sql_params_detail['cost_detail_total_' . $param_key] = $cost_detail['cost_detail_total'] ? $cost_detail['cost_detail_total'] : null;
				}
				
				if(count($sql_values_detail)) {
					$sql_detail = "INSERT INTO e_cost_detail(cost_id, row_id, cost_detail_desc, cost_detail_each, cost_detail_count, cost_detail_total)"
								. " VALUES" . implode(",", $sql_values_detail);
					$query_detail = DB::query($sql_detail);
					$query_detail->param('cost_id', $params['cost_id']);
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
			$sql = "UPDATE t_cost SET approval_status = :approval_status, approval_at = :approval_at, approval_by = :approval_by WHERE cost_id = :cost_id";
			$query = DB::query($sql);
			$query->param('cost_id', $params['cost_id']);
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
	 * 按条件获得支出记录列表
	 */
	public static function SelectCostList($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			$sql_order_column = "created_at";
			$sql_order_method = "desc";
			$sql_limit = "";
			$sql_offset = "";
			
			foreach($params as $param_key => $param_value) {
				switch($param_key) {
					case 'cost_id_list':
						if(count($param_value)) {
							$sql_where[] = " tc.cost_id IN :cost_id_list ";
							$sql_params['cost_id_list'] = $param_value;
						}
						break;
					case 'cost_desc':
						if(count($param_value)) {
							$sql_sub_where = array();
							foreach($param_value as $name_key => $name) {
								$sql_sub_where[] = "tc.cost_desc LIKE :cost_desc_" . $name_key;
								$sql_params['cost_desc_' . $name_key] = '%' . $name . '%';
							}
							$sql_where[] = " (" . implode(" OR ", $sql_sub_where) . ") ";
						}
						break;
					case 'cost_type':
						if(count($param_value)) {
							$sql_where[] = " tc.cost_type IN :cost_type_list ";
							$sql_params['cost_type_list'] = $param_value;
						}
						break;
					case 'price_min':
						if(is_numeric($param_value)) {
							$sql_where[] = " tc.cost_price >= :price_min ";
							$sql_params['price_min'] = floatval($param_value);
						}
						break;
					case 'price_max':
						if(is_numeric($param_value)) {
							$sql_where[] = " tc.cost_price <= :price_max ";
							$sql_params['price_max'] = floatval($param_value);
						}
						break;
					case 'cost_at_min':
						if(strtotime($param_value)) {
							$sql_where[] = " tc.cost_at >= :cost_at_min ";
							$sql_params['cost_at_min'] = date('Y-m-d', strtotime($param_value)) . ' 00:00:00';
						}
						break;
					case 'cost_at_max':
						if(strtotime($param_value)) {
							$sql_where[] = " tc.cost_at <= :cost_at_max ";
							$sql_params['cost_at_max'] = date('Y-m-d', strtotime($param_value)) . ' 23:59:59';
						}
						break;
					case 'created_by':
						$sql_where[] = " tc.created_by = :created_by ";
						$sql_params['created_by'] = $param_value;
						break;
					case 'active_only':
						$sql_where[] = " tc.delete_flag = 0 ";
						break;
					case 'sort_column':
						$sort_column_list = array('cost_type', 'cost_price', 'cost_at', 'created_at', 'modified_at');
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
			
			//符合条件的支出记录总数获取
			$sql_count = "SELECT COUNT(DISTINCT tc.cost_id) cost_count "
						. "FROM t_cost tc "
						. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");
			$query_count = DB::query($sql_count);
			foreach ($sql_params as $param_key => $param_value) {
				$query_count->param($param_key, $param_value);
			}
			$result_count = $query_count->execute()->as_array();
			
			if(count($result_count)) {
				$cost_count = intval($result_count[0]['cost_count']);
				
				if($cost_count) {
					//支出记录信息获取
					$sql_cost = "SELECT tc.*, mct.cost_type_name " 
							. "FROM t_cost tc " 
							. "LEFT JOIN m_cost_type mct ON tc.cost_type = mct.cost_type_id "
							. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "")
							. "ORDER BY " . $sql_order_column . " " . $sql_order_method . " "
							. $sql_limit . $sql_offset;
					$query_cost = DB::query($sql_cost);
					foreach ($sql_params as $param_key => $param_value) {
						$query_cost->param($param_key, $param_value);
					}
					$result_cost = $query_cost->execute()->as_array();
					
					if(count($result_cost)) {
						$cost_list = array();
						$cost_id_list = array();
						foreach($result_cost as $cost) {
							$cost_list[$cost['cost_id']] = $cost;
							$cost_list[$cost['cost_id']]['cost_detail_list'] = array();
							$cost_id_list[] = intval($cost['cost_id']);
						}
						
						//支出明细信息获取
						if(isset($params['detail_flag'])) {
							$sql_detail = "SELECT eid.* "
									. "FROM e_cost_detail eid "
									. "WHERE eid.cost_id IN :cost_id_list "
									. "ORDER BY eid.cost_id ASC, eid.row_id ASC";
							$query_detail = DB::query($sql_detail);
							$query_detail->param('cost_id_list', $cost_id_list);
							$result_detail = $query_detail->execute()->as_array();
							
							if(count($result_detail)) {
								foreach($result_detail as $cost_detail) {
									$cost_list[$cost_detail['cost_id']]['cost_detail_list'][] = $cost_detail;
								}
							}
						}
						
						//返回值整理
						$result = array(
							'cost_count' => $cost_count,
							'cost_list' => $cost_list,
							'start_number' => $sql_offset + 1,
							'end_number' => count($cost_count) + $sql_offset,
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
	 * 获取特定单个支出记录信息
	 */
	public static function SelectCost($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//支出ID限定
			if(isset($params['cost_id'])) {
				$sql_where[] = " tc.cost_id = :cost_id ";
				$sql_params['cost_id'] = $params['cost_id'];
			}
			//有效性限定
			if(isset($params['active_only'])) {
				if($params['active_only']) {
					$sql_where[] = " tc.delete_flag = 0 ";
				}
			}
			
			//数据获取
			$sql_cost = "SELECT tc.*, mct.cost_type_name, tuc.user_name created_name, tum.user_name modified_name, tua.user_name approval_name " 
					. "FROM t_cost tc " 
					. "LEFT JOIN m_cost_type mct ON tc.cost_type = mct.cost_type_id " 
					. "LEFT JOIN t_user tuc ON tc.created_by = tuc.user_id " 
					. "LEFT JOIN t_user tum ON tc.modified_by = tum.user_id " 
					. "LEFT JOIN t_user tua ON tc.approval_by = tua.user_id " 
					. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");
			$query_cost = DB::query($sql_cost);
			foreach($sql_params as $param_key => $param_value) {
				$query_cost->param($param_key, $param_value);
			}
			$result_cost = $query_cost->execute()->as_array();
			
			if(count($result_cost) == 1) {
				$result = $result_cost[0];
				
				//获取支出明细
				$sql_detail = "SELECT eid.* "
							. "FROM e_cost_detail eid "
							. "WHERE eid.cost_id = :cost_id "
							. "ORDER BY eid.row_id ASC ";
				$query_detail = DB::query($sql_detail);
				$query_detail->param('cost_id', $result['cost_id']);
				$result_detail = $query_detail->execute()->as_array();
				$result['cost_detail_list'] = $result_detail;
				
				return $result;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 编辑支出记录前编辑信息查验
	 */
	public static function CheckEditCost($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		//支出项目
		if(empty($params['cost_type'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_cost_type';
		} elseif(!is_numeric($params['cost_type']) || !is_int($params['cost_type'] + 0)) {
			$result['result'] = false;
			$result['error'][] = 'noint_cost_type';
		} elseif(!Model_Costtype::CheckCostTypeIdExist($params['cost_type'], 1)) {
			$result['result'] = false;
			$result['error'][] = 'error_cost_type';
		}
		
		//支出说明
		if(empty($params['cost_desc'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_cost_desc';
		}
		
		//支出日期
		if(empty($params['cost_at'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_cost_at';
		} elseif(!preg_match('/^\d{4}\/\d{1,2}\/\d{1,2}$/', $params['cost_at'])) {
			$result['result'] = false;
			$result['error'][] = 'format_cost_at';
		} else {
			list($year, $month, $day) = explode('/', $params['cost_at']);
			if(!checkdate(intval($month), intval($day), intval($year))) {
				$result['result'] = false;
				$result['error'][] = 'error_cost_at';
			}
		}
		
		//支出明细
		if(isset($params['cost_detail_list'])) {
			if(!is_array($params['cost_detail_list'])) {
				$result['result'] = false;
				$result['error'][] = 'noarray_cost_detail_list';
			} elseif(!count($params['cost_detail_list'])) {
				$result['result'] = false;
				$result['error'][] = 'empty_cost_detail_list';
			} else {
				foreach($params['cost_detail_list'] as $cost_detail) {
					//款项
					if(empty($cost_detail['cost_detail_desc'])) {
						$result['result'] = false;
						$result['error'][] = 'empty_cost_detail_desc';
					}
					
					//单价
					if(empty($cost_detail['cost_detail_each'])) {
						$result['result'] = false;
						$result['error'][] = 'empty_cost_detail_each';
					} elseif(!preg_match('/^(\d+)(\.\d{1,2})?$/', $cost_detail['cost_detail_each'])) {
						$result['result'] = false;
						$result['error'][] = 'error_cost_detail_each';
					}
					
					//数量
					if(empty($cost_detail['cost_detail_count'])) {
						$result['result'] = false;
						$result['error'][] = 'empty_cost_detail_count';
					} elseif(!preg_match('/^(\d+)(\.\d+)?$/', $cost_detail['cost_detail_count'])) {
						$result['result'] = false;
						$result['error'][] = 'error_cost_detail_count';
					}
				}
			}
		}
		
		return $result;
	}
	
	/*
	 * 删除支出记录前删除信息查验
	 */
	public static function CheckDeleteCost($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_array($params['cost_id_list'])) {
			$result['result'] = false;
			$result['error'][] = 'noarray_cost_id';
		} elseif(!count($params['cost_id_list'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_cost_id';
		} else {
			$all_num_flag = true;
			
			foreach($params['cost_id_list'] as $cost_id) {
				if(!is_numeric($cost_id)) {
					$result['result'] = false;
					$all_num_flag = false;
					$result['error'][] = 'nonum_cost_id';
					break;
				}
			}
			
			if($all_num_flag) {
				$params_select = array('cost_id_list' => $params['cost_id_list']);
				$result_select = Model_Cost::SelectCostList($params_select);
				
				if($result_select['cost_count'] != count(array_unique($params['cost_id_list']))) {
					$result['result'] = false;
					$result['error'][] = 'error_cost_id';
				} elseif($params['self_only']) {
					foreach($result_select['cost_list'] as $cost_select) {
						if($cost_select['approval_status']) {
							$result['result'] = false;
							$result['error'][] = 'error_status';
							break;
						} elseif($params['delete_by'] != $cost_select['created_by']) {
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
	 * 更新支出记录确认状态前更新信息查验
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
