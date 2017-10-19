<?php
/* 
 * 权限管理页
 */

class Controller_Admin_User_Modifysubgroup extends Controller_Admin_App
{

	/**
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
//		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['input_name'] = '';
			$data['master_group_name'] = '';
			$data['sub_group_name'] = '';
			$data['error_message'] = '';
			
			if(!isset($_GET['sub_group_id'])) {
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
				exit;
			} else {
				$sub_group = Model_Functiongroup::SelectSubGroupById($_GET['sub_group_id']);
				if(!$sub_group) {
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
			}
			
			$data['master_group_name'] = $sub_group['master_group_name'];
			$data['sub_group_name'] = $sub_group['sub_group_name'];
			
			if(isset($_POST['page'], $_POST['name'])) {
				if($_POST['page'] == 'modify_sg') {
					$params_update = array(
						'function_group_id' => $_GET['sub_group_id'],
						'function_group_name' => trim($_POST['name']),
					);
					$result_check = Model_Functiongroup::CheckUpdateSubGroup($params_update);
					
					if($result_check['result']) {
						$result_update = Model_Functiongroup::UpdateFunctionGroup($params_update);
						
						if($result_update) {
							$_SESSION['update_sub_group_success'] = true;
							header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
							exit;
						} else {
							$data['error_message'] = '失敗';
						}
					} else {
						foreach($result_check['error'] as $update_error) {
							$error_message_list = array();
							switch($update_error) {
								case 'noset_id':
									$error_message_list[] = 'ID未設定';
									break;
								case 'nonum_id':
									$error_message_list[] = 'ID数字';
									break;
								case 'noset_name':
									$error_message_list[] = '名前システム変更';
									break;
								case 'empty_name':
									$error_message_list[] = '名前未入力';
									break;
								case 'nomodify':
									$error_message_list[] = '未変更';
									break;
								case 'duplication':
									$error_message_list[] = '重複データ有り';
									break;
								default:
									break;
							}
							$data['error_message'] = implode('<br/>', $error_message_list);
						}
					}
					
					$data['input_name'] = $_POST['name'];
				} else {
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
			}
			
			return Response::forge(View::forge($this->template . '/admin/user/modify_sub_group', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}

}