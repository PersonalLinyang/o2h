<?php

class Model_User extends Model
{

	/*
	 * 用户登陆
	 */
	public static function Login($user_email, $user_password) {
		//根据登陆信息检索用户
		$sql = "SELECT user_id, user_name FROM t_user WHERE user_email = :user_email AND user_password = :user_password AND delete_flag = 0";
		$query = DB::query($sql);
		$query->param('user_email', $user_email);
		$query->param('user_password', md5(sha1($user_password)));
		$result = $query->execute()->as_array();
		
		//返回信息整理
		if(count($result) == 1) {
			$user_info = $result[0];
			return $user_info;
		} else {
			return false;
		}
	}
	
	/*
	 * 按条件获得用户简易列表
	 */
	public static function SelectUserSimpleList($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			$sql_order_column = "user_id";
			$sql_order_method = "asc";
			
			//检索条件处理
			foreach($params as $param_key => $param_value) {
				switch($param_key) {
					case 'active_only':
						$sql_where[] = " tu.delete_flag = 0 ";
						break;
					case 'sort_column':
						$sort_column_list = array('user_id', 'user_name');
						if(in_array($param_value, $sort_column_list)) {
							$sql_order_column = $param_value;
						}
						break;
					case 'sort_method':
						if(in_array($param_value, array('asc', 'desc'))) {
							$sql_order_method = $param_value;
						}
						break;
					default:
						break;
				}
			}
			
			//符合条件的用户简易列表获取
			$sql = "SELECT tu.user_id, tu.user_name "
				. "FROM t_user tu "
				. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "")
				. "ORDER BY " . $sql_order_column . " " . $sql_order_method;
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
	 * 根据用户ID获得用户类型
	 */
	public static function SelectUserTypeById($user_id) {
		$sql = "SELECT user_type FROM t_user WHERE user_id = :user_id";
		$query = DB::query($sql);
		$query->param('user_id', $user_id);
		$result = $query->execute()->as_array();
		
		if(count($result) == 1) {
			$user_type = $result[0]['user_type'];
			return $user_type;
		} else {
			return false;
		}
	}
	
	/*
	 * 检查用户ID是否存在
	 */
	public static function CheckUserIdExist($user_id, $active_check = 0) {
		try {
			$sql = "SELECT user_id FROM t_user WHERE user_id = :user_id " . ($active_check ? " AND delete_flag = 0 " : "");
			$query = DB::query($sql);
			$query->param('user_id', $user_id);
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

}

