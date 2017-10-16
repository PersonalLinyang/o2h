<?php

class Model_User extends Model
{

	public $user_id;
	public $user_name;
	public $user_permission;
	
	/*
	 * 用户登陆
	 */
	public static function Login($user_email, $user_password) {
		//根据登陆信息检索用户
		$sql_user = "SELECT * FROM t_user WHERE user_email = :user_email AND user_password = :user_password AND delete_flag = 0";
		$query_user = DB::query($sql_user);
		$query_user->param('user_email', $user_email);
		$query_user->param('user_password', md5(sha1($user_password)));
		$result_user = $query_user->execute()->as_array();
		
		//返回信息整理
		if(count($result_user) == 1) {
			$user = new Model_User();
			$user->user_id = $result_user[0]["user_id"];
			$user->user_name = $result_user[0]["user_name"];

			$permission_list = array();
			$sql_permission = "SELECT mp.* FROM m_permission mp WHERE mp.permission_id IN "
							. "(SELECT rpp.permission_id FROM r_position_permission rpp WHERE rpp.position_id IN "
							. "(SELECT position_id FROM r_user_position rup WHERE rup.user_id = :user_id)) ";
			$query_permission = DB::query($sql_permission);
			$query_permission->param('user_id', $result_user[0]["user_id"]);
			$result_permission = $query_permission->execute()->as_array();

			foreach($result_permission as $permission) {
				if($permission['authority_id']){
					$permission_list[$permission['master_group_id']][$permission['sub_group_id']][$permission['function_id']][$permission['authority_id']] = 1;
				} else {
					$permission_list[$permission['master_group_id']][$permission['sub_group_id']][$permission['function_id']] = 1;
				}
			}
			$user->user_permission = $permission_list;
			
			return $user;
		} else {
			return false;
		}
	}

}

