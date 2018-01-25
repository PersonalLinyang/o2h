<?php
/* 
 * 添加景点类别页
 */

class Controller_Admin_Service_Spottype_AddSpotType extends Controller_Admin_App
{

	/**
	 * 添加景点类别
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		try {
			if(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 10)) {
				//当前登陆用户不具备景点类别管理的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//页面标题
				$data['page_title'] ='添加景点类别';
				//表单页面索引
				$data['form_page_index'] = 'add_spot_type';
				
				//form控件默认值设定
				$data['input_spot_type_name'] = '';
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是添加景点类别页
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_spot_type_name'] = isset($_POST['spot_type_name']) ? trim($_POST['spot_type_name']) : $data['input_spot_name'];
						
						//添加景点类别用数据生成
						$params_insert = array(
							'spot_type_id' => '',
							'spot_type_name' => $data['input_spot_type_name'],
						);
						
						//输入内容检查
						$result_check = Model_Spottype::CheckEditSpotType($params_insert);
						
						if($result_check['result']) {
							//添加景点类别
							$result_insert = Model_Spottype::InsertSpotType($params_insert);
							
							if($result_insert) {
								//更新景点信息导入模板
								$result_excel = Model_Spottype::ModifySpotModelExcel();
								
								if($result_excel) {
									//添加成功 页面跳转
									$_SESSION['add_spot_type_success'] = true;
									header('Location: //' . $_SERVER['HTTP_HOST'] . '/admin/spot_type_list/');
									exit;
								} else {
									//添加成功 页面跳转
									$_SESSION['add_spot_type_error'] = 'error_excel';
									header('Location: //' . $_SERVER['HTTP_HOST'] . '/admin/spot_type_list/');
									exit;
								}
							} else {
								$error_message_list[] = '数据库错误：数据添加失败';
							}
						} else {
							//获取错误信息
							foreach($result_check['error'] as $insert_error) {
								switch($insert_error) {
									case 'empty_spot_type_name': 
										$error_message_list[] = '请输入景点类别名';
										break;
									case 'long_spot_type_name': 
										$error_message_list[] = '景点类别名不能超过50字';
										break;
									case 'dup_spot_type_name': 
										$error_message_list[] = '该景点类别名与其他景点类别重复,请选用其他景点类别名';
										break;
									default:
										$error_message_list[] = '发生系统错误,请重新尝试添加';
										break;
								}
							}
						}
						
						$error_message_list = array_unique($error_message_list);
						
						//输出错误信息
						if(count($error_message_list)) {
							$data['error_message'] = implode('<br/>', $error_message_list);
						}
					}
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/spottype/edit_spot_type', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}