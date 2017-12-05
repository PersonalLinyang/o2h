<?php

class Model_Member extends Model
{
	
	/*
	 * 检查会员ID是否存在
	 */
	public static function CheckExistMemberId($member_id) {
		/*
		$sql = "SELECT * FROM t_member WHERE member_id = :member_id";
		$query = DB::query($sql);
		$query->param(':member_id', $member_id);
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return true;
		} else {
			return false;
		}
		*/
		
		return true;
	}

}

