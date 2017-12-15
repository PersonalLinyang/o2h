<?php

class Model_User extends Model
{

	/*
	 * 用户登陆
	 */
	public static function Login($user_email, $user_password) {
		//根据登陆信息检索用户
		$sql_user = "SELECT user_id, user_name FROM t_user WHERE user_email = :user_email AND user_password = :user_password AND delete_flag = 0";
		$query_user = DB::query($sql_user);
		$query_user->param('user_email', $user_email);
		$query_user->param('user_password', md5(sha1($user_password)));
		$result_user = $query_user->execute()->as_array();
		
		//返回信息整理
		if(count($result_user) == 1) {
			$result = $result_user[0];

			$permission_list = array(
				'master_group' => array(),
				'sub_group' => array(),
				'function' => array(),
				'authority' => array(),
			);
			$permission_type_list = array(
				'1' => 'master_group',
				'2' => 'sub_group',
				'3' => 'function',
				'4' => 'authority',
			);
			$sql_permission = "SELECT rp.permission_type, rp.permission_id FROM r_permission rp WHERE rp.user_type_id IN "
							. "(SELECT tu.user_type FROM t_user tu WHERE tu.user_id = :user_id) ";
			$query_permission = DB::query($sql_permission);
			$query_permission->param('user_id', $result_user[0]["user_id"]);
			$result_permission = $query_permission->execute()->as_array();

			foreach($result_permission as $permission) {
				$permission_list[$permission_type_list[$permission['permission_type']]][] = $permission['permission_id'];
			}
			$result['user_permission'] = $permission_list;
			
			return $result;
		} else {
			return false;
		}
	}
	
	/*
	 * 获取全部在职用户简易信息列表
	 */
	public static function GetUserSimpleListActive() {
		$sql = "SELECT user_id, user_name FROM t_user Where delete_flag = 0 ORDER BY user_id";
		$query = DB::query($sql);
		$result = $query->execute()->as_array();
		
		return $result;
	}
	
	/*
	 * 检查用户ID是否在职
	 */
	public static function CheckUserIdActive($user_id) {
		$sql = "SELECT user_id FROM t_user WHERE user_id = :user_id AND delete_flag = 0";
		$query = DB::query($sql);
		$query->param(':user_id', $user_id);
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return true;
		} else {
			return false;
		}
	}

}

