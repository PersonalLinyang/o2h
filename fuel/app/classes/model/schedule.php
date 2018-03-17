<?php

class Model_Schedule extends Model
{
	
	//日程重复查验
	public static function CheckScheduleDuplication($user_id, $start_at, $end_at, $customer_id='') {
		try{
			//数据获取
			$sql = "SELECT ts.schedule_id " 
				. "FROM t_schedule ts " 
				. "LEFT JOIN r_user_schedule rus ON ts.schedule_id=rus.schedule_id " 
				. "WHERE rus.user_id=:user_id AND ts.start_at<:end_at AND ts.end_at>:start_at " 
				. ($customer_id ? " AND ts.schedule_id NOT IN (SELECT schedule_id FROM r_customer_schedule WHERE customer_id=:customer_id) " : "");
			$query = DB::query($sql);
			$query->param('user_id', $user_id);
			$query->param('start_at', $start_at);
			$query->param('end_at', $end_at);
			if($customer_id) {
				$query->param('customer_id', $customer_id);
			}
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

