<?php
/* 
 * 权限管理页
 */

class Controller_Admin_User_Addauthority extends Controller_Admin_App
{

	/**
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		//调用共用Header
//		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['input_name'] = '';
			$data['master_group_name'] = '';
			$data['sub_group_name'] = '';
			$data['function_name'] = '';
			$data['error_message'] = '';
			
			if(!isset($_GET['function_id'])) {
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
				exit;
			} else {
				$function = Model_Function::SelectFunctionById($_GET['function_id']);
				if(!$function) {
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
			}
			
			$data['master_group_name'] = $function['master_group_name'];
			$data['sub_group_name'] = $function['sub_group_name'];
			$data['function_name'] = $function['function_name'];
			
			if(isset($_POST['page'], $_POST['name'])) {
				if($_POST['page'] == 'add_authority') {
					$params_insert = array(
						'authority_name' => trim($_POST['name']),
						'function_id' => $_GET['function_id'],
					);
					$result_check = Model_Authority::CheckInsertAuthority($params_insert);
					
					if($result_check['result']) {
						$result_insert = Model_Authority::InsertAuthority($params_insert);
						
						if($result_insert) {
							$_SESSION['add_authority_success'] = true;
							header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/permission_list/');
							exit;
						} else {
							$data['error_message'] = '失敗';
						}
					} else {
						foreach($result_check['error'] as $insert_error) {
							$error_message_list = array();
							switch($insert_error) {
								case 'noset_name':
									$error_message_list[] = '名前システム変更';
									break;
								case 'empty_name':
									$error_message_list[] = '名前未入力';
									break;
								case 'noset_function':
									$error_message_list[] = '親設定してない';
									break;
								case 'nonum_function':
									$error_message_list[] = '親数字じゃない';
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
			
			return Response::forge(View::forge($this->template . '/admin/user/add_authority', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}

}