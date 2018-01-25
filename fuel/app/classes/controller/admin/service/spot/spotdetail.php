<?php
/* 
 * 景点详细信息页
 */

class Controller_Admin_Service_Spot_Spotdetail extends Controller_Admin_App
{

	/**
	 * 景点详细信息页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($spot_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		try{
			if(!is_numeric($spot_id)) {
				//景点类型ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'sub_group', 9)) {
				//当前登陆用户不具备查看景点的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['success_message'] = '';
				$data['error_message'] = '';
				$data['spot_list_url'] = '/admin/spot_list/';
				
				//获取景点信息
				$spot = Model_Spot::SelectSpot(array('spot_id' => $spot_id, 'active_only' => 1));
				
				if(!$spot) {
					//不存在该ID的景点
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
				
				//景点信息
				$data['spot_info'] = $spot;
				if($spot['created_by'] == $_SESSION['login_user']['id']) {
					//是否具备景点编辑权限
					$data['edit_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 6);
				} else {
					//是否具备修改其他用户所登陆的景点信息权限
					$data['edit_able_flag'] = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 2);
				}
				
				//输出提示信息
				if(isset($_SESSION['modify_spot_status_success'])) {
					$data['success_message'] = '景点公开状态更新成功';
					unset($_SESSION['modify_spot_status_success']);
				}
				if(isset($_SESSION['modify_spot_status_error'])) {
					$data['error_message'] = '景点公开状态更新失敗';
					unset($_SESSION['modify_spot_status_error']);
				}
				if(isset($_SESSION['add_spot_success'])) {
					$data['success_message'] = '景点添加成功';
					unset($_SESSION['add_spot_success']);
				}
				if(isset($_SESSION['modify_spot_success'])) {
					$data['success_message'] = '景点信息修改成功';
					unset($_SESSION['modify_spot_success']);
				}
				
				if(isset($_SERVER['HTTP_REFERER'])) {
					if(strstr($_SERVER['HTTP_REFERER'], 'admin/spot_list')) {
						$data['spot_list_url'] = $_SERVER['HTTP_REFERER'];
					}
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/spot/spot_detail', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}