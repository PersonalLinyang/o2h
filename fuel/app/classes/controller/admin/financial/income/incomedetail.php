<?php
/* 
 * 收入记录详情页
 */

class Controller_Admin_Financial_Income_Incomedetail extends Controller_Admin_App
{

	/**
	 * 收入记录详情页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($income_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		//当前登陆用户
		$login_user_id = $_SESSION['login_user']['id'];
		
		try {
			if(!is_numeric($income_id)) {
				//收入记录ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($login_user_id, 'sub_group', 15)) {
				//当前登陆用户不具备查看收入记录的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['success_message'] = '';
				$data['error_message'] = '';
				
				//获取返回一览页时的一览页URL
				$data['income_list_url'] = '/admin/income_list/';
				if(isset($_SERVER['HTTP_REFERER'])) {
					if(strstr($_SERVER['HTTP_REFERER'], 'admin/income_list')) {
						//通过一览页链接进入
						$data['income_list_url'] = $_SERVER['HTTP_REFERER'];
					} elseif(strstr($_SERVER['HTTP_REFERER'], 'admin/modify_income/' . $income_id) || strstr($_SERVER['HTTP_REFERER'], 'admin/income_detail/' . $income_id)) {
						if(isset($_SESSION['income_list_url_detail'])) {
							$data['income_list_url'] = $_SESSION['income_list_url_detail'];
						}
					}
				}
				//暂时保留一览页URL
				$_SESSION['income_list_url_detail'] = $data['income_list_url'];
				
				//获取收入记录信息
				$income = Model_Income::SelectIncome(array('income_id' => $income_id, 'active_only' => true));
				
				if(!$income) {
					//不存在该ID的收入记录
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
				
				//收入记录信息
				$data['income_info'] = $income;
				
				if($income['created_by'] == $_SESSION['login_user']['id']) {
					//是否具备收入记录信息编辑权限
					$data['edit_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 31);
				} else {
					//是否具备修改其他用户所登陆的收入记录信息权限
					$data['edit_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 13);
				}
				//是否具备确认收入记录信息权限
				$data['approval_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 33);
				
				//输出提示信息
				if(isset($_SESSION['add_income_success'])) {
					$data['success_message'] = '收入添加成功';
					unset($_SESSION['add_income_success']);
				}
				if(isset($_SESSION['modify_income_success'])) {
					$data['success_message'] = '收入信息修改成功';
					unset($_SESSION['modify_income_success']);
				}
				if(isset($_SESSION['modify_income_status_success'])) {
					$data['success_message'] = '收入确认状态更新成功';
					unset($_SESSION['modify_income_status_success']);
				}
				if(isset($_SESSION['modify_income_status_error'])) {
					$data['error_message'] = '收入确认状态更新失敗 请重新尝试';
					unset($_SESSION['modify_income_status_error']);
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/financial/income/income_detail', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}