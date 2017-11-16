<?php
/* 
 * 餐饮类别名称修改页
 */

class Controller_Admin_Service_Modifyrestauranttype extends Controller_Admin_App
{

	/**
	 * 餐饮类别名称修改页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($restaurant_type_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['error_message'] = '';
			
			$restaurant_type_info = Model_RestaurantType::SelectRestaurantTypeById($restaurant_type_id);
			
			if($restaurant_type_info) {
				$data['restaurant_type_name'] = $restaurant_type_info['restaurant_type_name'];
				$data['input_restaurant_type_name'] = '';
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					//数据来源检验
					if($_POST['page'] == 'modify_restaurant_type') {
						if(isset($_POST['name'])) {
							
							//修改餐饮用数据生成
							$param_update = array(
								'restaurant_type_id' => $restaurant_type_id,
								'restaurant_type_name' => $_POST['name'],
							);
							
							//修改内容检查
							$result_check = Model_RestaurantType::CheckUpdateRestaurantType($param_update);
							
							if($result_check['result']) {
								//数据修改
								$result_update = Model_RestaurantType::UpdateRestaurantType($param_update);
								
								if($result_update) {
									//修改成功 页面跳转
									$_SESSION['modify_restaurant_type_success'] = true;
									header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/restaurant_type_list/');
									exit;
								} else {
									$error_message_list[] = '数据库错误：数据添加失败';
								}
							} else {
								foreach($result_check['error'] as $insert_error) {
									switch($insert_error) {
										case 'nonum_id':
											$error_message_list[] = '餐饮类别编号不是数字';
											break;
										case 'empty_name':
											$error_message_list[] = '请输入修改后餐饮类别名称';
											break;
										case 'nomodify':
											$error_message_list[] = '请输入与原名称不同的餐饮类别名称';
											break;
										case 'duplication':
											$error_message_list[] = '已存在该名称的餐饮类别，无法重复设定';
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
						$data['input_restaurant_type_name'] = isset($_POST['name']) ? $_POST['name'] : '';
					
						//输出错误信息
						if(count($error_message_list)) {
							$data['error_message'] = implode('<br/>', $error_message_list);
						}
					} else {
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
						exit;
					}
				} else {
					$data['input_restaurant_type_name'] = '';
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/modify_restaurant_type', $data, false));
			} else {
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			}
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}

}