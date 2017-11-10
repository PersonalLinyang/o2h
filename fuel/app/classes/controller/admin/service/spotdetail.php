<?php
/* 
 * 景点详细信息页
 */

class Controller_Admin_Service_Spotdetail extends Controller_Admin_App
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
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['success_message'] = '';
			$data['error_message'] = '';
			
			$spot_info = Model_Spot::SelectSpotDetailBySpotId($spot_id);
			
			if($spot_info) {
				$data['spot_info'] = $spot_info;
				
				if(isset($_SESSION['modify_spot_status_success'])) {
					$data['success_message'] = '景点公开状态更新成功';
					unset($_SESSION['modify_spot_status_success']);
				}
				if(isset($_SESSION['modify_spot_status_error'])) {
					$data['error_message'] = '景点公开状态更新失敗 请重新尝试';
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
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/spot_detail', $data, false));
			} else {
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			}
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}
	
	/**
	 * 景点公开状态更新
	 * @access  public
	 * @return  Response
	 */
	public function action_modifyspotstatus($param = null)
	{
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['page'], $_POST['modify_id'], $_POST['modify_value'])) {
		if(isset($_POST['page'], $_POST['modify_id'], $_POST['modify_value'])) {
			if($_POST['page'] == 'spot_detail') {
				//删除信息检查
				switch($_POST['modify_value']) {
					case 'publish':
						$spot_status = '1';
						break;
					case 'protected':
						$spot_status = '0';
						break;
					default:
						$spot_status = '';
						break;
				}
				$params_update = array(
					'spot_id' => $_POST['modify_id'],
					'spot_status' => $spot_status,
				);
				$result_check = Model_Spot::CheckUpdateSpotStatusById($params_update);
				if($result_check['result']) {
					//数据删除
					$result_update = Model_Spot::UpdateSpotStatusById($params_update);
					
					if($result_update) {
						$_SESSION['modify_spot_status_success'] = true;
						header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/spot_detail/' . $_POST['modify_id'] . '/');
						exit;
					}
				}
			}
		}
		$_SESSION['modify_spot_status_error'] = true;
		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/spot_detail/' . $_POST['modify_id'] . '/');
		exit;
	}

}