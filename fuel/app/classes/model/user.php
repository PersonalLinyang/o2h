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
		$query->param('user_id', $user_id);
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return true;
		} else {
			return false;
		}
	}

}

