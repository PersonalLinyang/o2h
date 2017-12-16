<?php

class Model_Member extends Model
{
	
	/*
	 * 添加会员
	 */
	public static function InsertMember($params) {
		//添加会员
		$sql_insert = "INSERT INTO t_member(member_name, member_email, member_password, member_gender, member_birth_year, "
					. "member_tel, member_wechat, member_qq, delete_flag, created_at, modified_at) "
					. "VALUES(:member_name, :member_email, :member_password, :member_gender, :member_birth_year, "
					. ":member_tel, :member_wechat, :member_qq, 0, :time_now, :time_now)";
		$query_insert = DB::query($sql_insert);
		$query_insert->param(':member_name', $params['member_name']);
		$query_insert->param(':member_email', $params['member_email']);
		$query_insert->param(':member_password', md5(sha1($params['member_password'])));
		$query_insert->param(':member_gender', $params['member_gender']);
		$query_insert->param(':member_birth_year', $params['member_birth_year']);
		$query_insert->param(':member_tel', $params['member_tel']);
		$query_insert->param(':member_wechat', $params['member_wechat']);
		$query_insert->param(':member_qq', $params['member_qq']);
		$query_insert->param(':time_now', date('Y-m-d H:i:s', time()));
		$result_insert = $query_insert->execute();
		
		return $result_insert;
	}

	/*
	 * 添加会员前添加信息查验
	 */
	public static function CheckInsertMember($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);

		//姓名
		if(empty($params['member_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		} elseif(mb_strlen($params['member_name']) > 50) {
			$result['result'] = false;
			$result['error'][] = 'long_name';
		}

		//电子邮箱
		if(empty($params['member_email'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_email';
		} elseif(mb_strlen($params['member_email']) > 200) {
			$result['result'] = false;
			$result['error'][] = 'long_email';
		} elseif(!preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $params['member_email'])) {
			$result['result'] = false;
			$result['error'][] = 'error_email';
		} elseif(Model_Member::CheckMemberEmailActive($params['member_email'])) {
			$result['result'] = false;
			$result['error'][] = 'dup_email';
		}
		
		//密码
		if(empty($params['member_password'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_password';
		} elseif(!preg_match('/^[0-9A-Za-z]{6,16}$/', $params['member_password'])) {
			$result['result'] = false;
			$result['error'][] = 'error_password';
		}
		
		//性别
		if(!empty($params['menber_gender'])) {
			if(!in_array($params['menber_gender'], array('1', '2'))) {
				$result['result'] = false;
				$result['error'][] = 'nobool_gender';
			}
		}

		//生日
		if(!empty($params['member_birth_year'])) {
			if(intval($params['member_birth_year']) < 1930 || intval($params['member_birth_year']) > intval(date('Y', time()))) {
				$result['result'] = false;
				$result['error'][] = 'error_birth';
			}
		}

		//联系电话
		if(!empty($params['member_tel'])) {
			if(!preg_match('/^[-_0-9]{1,20}$/', $params['member_tel'])) {
				$result['result'] = false;
				$result['error'][] = 'error_tel';
			}
		}

		//微信号
		if(!empty($params['member_wechat'])) {
			if(!preg_match('/^[a-zA-Z]{1}[-_a-zA-Z0-9]{5,19}$/', $params['member_wechat'])) {
				$result['result'] = false;
				$result['error'][] = 'error_wechat';
			}
		}

		//QQ
		if(!empty($params['member_qq'])) {
			if(!preg_match('/^[1-9][0-9]{4,9}$/', $params['member_qq'])) {
				$result['result'] = false;
				$result['error'][] = 'error_qq';
			}
		}

		return $result;
	}
	
	/*
	 * 根据电子邮箱检查会员是否有效
	 */
	public static function CheckMemberEmailActive($member_email) {
		$sql_member = "SELECT member_id FROM t_member WHERE member_email = :member_email AND delete_flag = 0";
		$query_member = DB::query($sql_member);
		$query_member->param(':member_email', $member_email);
		$result_member = $query_member->execute()->as_array();
		
		if(count($result_member) == 1) {
			return true;
		} else {
			return false;
		}
	}

}

