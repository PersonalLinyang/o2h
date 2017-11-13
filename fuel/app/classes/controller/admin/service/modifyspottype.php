<?php
/* 
 * 景点类别名称修改页
 */

class Controller_Admin_Service_Modifyspottype extends Controller_Admin_App
{

	/**
	 * 景点类别名称修改页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($spot_type_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['error_message'] = '';
			
			$spot_type_info = Model_SpotType::SelectSpotTypeById($spot_type_id);
			
			if($spot_type_info) {
				$data['spot_type_name'] = $spot_type_info['spot_type_name'];
				$data['input_spot_type_name'] = '';
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					//数据来源检验
					if($_POST['page'] == 'modify_spot_type') {
						if(isset($_POST['name'])) {
							
							//修改景点用数据生成
							$param_update = array(
								'spot_type_id' => $spot_type_id,
								'spot_type_name' => $_POST['name'],
							);
							
							//修改内容检查
							$result_check = Model_SpotType::CheckUpdateSpotType($param_update);
							
							if($result_check['result']) {
								//数据修改
								$result_update = Model_SpotType::UpdateSpotType($param_update);
								
								if($result_update) {
									//修改成功 页面跳转
									$_SESSION['modify_spot_type_success'] = true;
									header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/spot_type_list/');
									exit;
								} else {
									$error_message_list[] = '数据库错误：数据添加失败';
								}
							} else {
								foreach($result_check['error'] as $insert_error) {
									switch($insert_error) {
										case 'nonum_id':
											$error_message_list[] = '景点类别编号不是数字';
											break;
										case 'empty_name':
											$error_message_list[] = '请输入修改后景点类别名称';
											break;
										case 'nomodify':
											$error_message_list[] = '请输入与原名称不同的景点类别名称';
											break;
										case 'duplication':
											$error_message_list[] = '已存在该名称的景点类别，无法重复设定';
											break;
										default:
											break;
									}
								}
							}
						} else {
							$error_message_list[] = '系统错误：请勿修改表单中的控件名称';
						}
						
						//检查发生错误时将之前输入的信息反映至表单中
						$data['input_spot_type_name'] = isset($_POST['name']) ? $_POST['name'] : '';
					
						//输出错误信息
						if(count($error_message_list)) {
							$data['error_message'] = implode('<br/>', $error_message_list);
						}
					} else {
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
						exit;
					}
				} else {
					$data['input_spot_type_name'] = '';
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/modify_spot_type', $data, false));
			} else {
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			}
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}

}