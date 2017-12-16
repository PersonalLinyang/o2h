<?php
/* 
 * 个人专属首页
 */

class Controller_Member_Login extends Controller_App
{

	/**
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		try{
			$data = array();

			if(isset($_SESSION['login_member'])) {
				header( 'Location: //' . $_SERVER['HTTP_HOST'] . '/member/' );
				exit;
			}
			
			//共同Header调用
			$data['header'] = Request::forge('common/header')->execute()->response();
			//共同Footer调用
			$data['footer'] = Request::forge('common/footer')->execute()->response();

			$data['input_member_name'] = '';
			$data['input_member_email_access'] = '';
			$data['input_member_password_access'] = '';
			$data['input_member_repassword'] = '';
			$data['input_member_gender'] = '';
			$data['input_member_birth_year'] = '';
			$data['input_member_tel'] = '';
			$data['input_member_wechat'] = '';
			$data['input_member_qq'] = '';
			$data['error_message_access'] = '';

			if(isset($_POST['page'])) {
				$error_message_list = array();

				if($_POST['page'] == 'member_access') {
					//注册处理
					$data['input_member_name'] = isset($_POST['member_name']) ? trim($_POST['member_name']) : '';
					$data['input_member_email_access'] = isset($_POST['member_email_access']) ? trim($_POST['member_email_access']) : '';
					$data['input_member_password_access'] = isset($_POST['member_password_access']) ? trim($_POST['member_password_access']) : '';
					$data['input_member_repassword'] = isset($_POST['member_repassword']) ? trim($_POST['member_repassword']) : '';
					$data['input_member_gender'] = isset($_POST['member_gender']) ? $_POST['member_gender'] : '';
					$data['input_member_birth_year'] = isset($_POST['member_birth_year']) ? $_POST['member_birth_year'] : '';
					$data['input_member_tel'] = isset($_POST['member_tel']) ? trim($_POST['member_tel']) : '';
					$data['input_member_wechat'] = isset($_POST['member_wechat']) ? trim($_POST['member_wechat']) : '';
					$data['input_member_qq'] = isset($_POST['member_qq']) ? trim($_POST['member_qq']) : '';

					//添加会员用数据生成
					$params_insert = array(
						'member_name' => $data['input_member_name'],
						'member_email' => $data['input_member_email_access'],
						'member_password' => $data['input_member_password_access'],
						'member_gender' => $data['input_member_gender'],
						'member_birth_year' => $data['input_member_birth_year'],
						'member_tel' => $data['input_member_tel'],
						'member_wechat' => $data['input_member_wechat'],
						'member_qq' => $data['input_member_qq'],
					);

					//添加数据查验
					$result_check = Model_Member::CheckInsertMember($params_insert);

					//两次密码相同判定
					if($data['input_member_password_access'] != $data['input_member_repassword']) {
						$result_check['result'] = false;
						$result_check['error'][] = 'diff_password';
					}

					if($result_check['result']) {
						$result_insert = Model_Member::InsertMember($params_insert);

						if($result_insert) {
							//添加成功 页面跳转
							header('Location: //' . $_SERVER['HTTP_HOST'] . '/member/');
							exit;
						} else {
							$error_message_list[] = '※数据库错误：数据添加失败,请重新尝试';
						}
					} else {
						foreach($result_check['error'] as $insert_error) {
							switch($insert_error) {
								case 'empty_name':
									$error_message_list[] = '※请输入姓名';
									break;
								case 'long_name':
									$error_message_list[] = '※姓名不能超过50字';
									break;
								case 'empty_email':
									$error_message_list[] = '※请输入电子邮箱';
									break;
								case 'long_email':
									$error_message_list[] = '※电子邮箱不能超过200字';
									break;
								case 'error_email':
									$error_message_list[] = '※电子邮箱不符合格式,请确认后重新输入';
									break;
								case 'dup_email':
									$error_message_list[] = '※该邮箱已被注册,请使用其他邮箱';
									break;
								case 'empty_password':
									$error_message_list[] = '※请输入密码';
									break;
								case 'error_password':
									$error_message_list[] = '※请设置密码为6～16位字母或数字';
									break;
								case 'diff_password':
									$error_message_list[] = '※两次输入的密码不一致';
									break;
								case 'error_tel':
									$error_message_list[] = '※联系电话不符合格式,请确认后重新输入';
									break;
								case 'error_wechat':
									$error_message_list[] = '※微信号不符合格式,请确认后重新输入';
									break;
								case 'error_qq':
									$error_message_list[] = '※QQ号不符合格式,请确认后重新输入';
									break;
								default:
									$error_message_list[] = '※发生系统错误,请勿随意更改网页原有控件设置';
									break;
							}
						}
					}

					$error_message_list = array_unique($error_message_list);

					//输出错误信息
					if(count($error_message_list)) {
						$data['error_message_access'] = implode('<br/>', $error_message_list);
					}
				} elseif ($_POST['page'] == 'member_login') {
					//登陆处理
				}
			}
			
			//View调用
			return Response::forge(View::forge($this->template . '/member/login', $data, false));
		} catch(Exception $e) {
			return Response::forge(View::forge($this->template . '/error/system_error', false));
		}
	}
	
}