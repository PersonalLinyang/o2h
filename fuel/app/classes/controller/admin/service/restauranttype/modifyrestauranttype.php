<?php
/* 
 * 餐饮类别修改页
 */

class Controller_Admin_Service_Restauranttype_Modifyrestauranttype extends Controller_Admin_App
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
		
		try {
			if(!is_numeric($restaurant_type_id)) {
				//餐饮类别ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 15)) {
				//当前登陆用户不具备餐饮类别管理的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//获取原本餐饮类别信息
				$params_select = array(
					'restaurant_type_id' => $restaurant_type_id,
					'active_only' => true,
				);
				$restaurant_type = Model_Restauranttype::SelectRestaurantType($params_select);
				
				if(!$restaurant_type) {
					//不存在该ID的餐饮类别
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
				
				//页面标题
				$data['page_title'] ='餐饮类别信息修改';
				//表单页面索引
				$data['form_page_index'] = 'modify_restaurant_type';
				
				//form控件默认值设定
				$data['input_restaurant_type_name'] = $restaurant_type['restaurant_type_name'];
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是餐饮类别信息修改页
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_restaurant_type_name'] = isset($_POST['restaurant_type_name']) ? trim($_POST['restaurant_type_name']) : $data['input_restaurant_name'];
						
						//修改餐饮类别用数据生成
						$params_update = array(
							'restaurant_type_id' => $restaurant_type_id,
							'restaurant_type_name' => $data['input_restaurant_type_name'],
						);
						
						//输入内容检查
						$result_check = Model_Restauranttype::CheckEditRestaurantType($params_update);
						
						if($result_check['result']) {
							//更新餐饮类别信息
							$result_update = Model_Restauranttype::UpdateRestaurantType($params_update);
							
							if($result_update) {
								//更新餐饮信息导入模板
								$result_excel = Model_Restauranttype::ModifyRestaurantModelExcel();
								
								if($result_excel) {
									//添加成功 页面跳转
									$_SESSION['modify_restaurant_type_success'] = true;
									header('Location: //' . $_SERVER['HTTP_HOST'] . '/admin/restaurant_type_list/');
									exit;
								} else {
									//添加成功 页面跳转
									$_SESSION['modify_restaurant_type_error'] = 'error_excel';
									header('Location: //' . $_SERVER['HTTP_HOST'] . '/admin/restaurant_type_list/');
									exit;
								}
							} else {
								$error_message_list[] = '数据库错误：数据添加失败';
							}
						} else {
							//获取错误信息
							foreach($result_check['error'] as $update_error) {
								switch($update_error) {
									case 'empty_restaurant_type_name': 
										$error_message_list[] = '请输入餐饮类别名';
										break;
									case 'long_restaurant_type_name': 
										$error_message_list[] = '餐饮类别名不能超过50字';
										break;
									case 'dup_restaurant_type_name': 
										$error_message_list[] = '该餐饮类别名与其他餐饮类别重复,请选用其他餐饮类别名';
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
				return Response::forge(View::forge($this->template . '/admin/service/restaurant_type/edit_restaurant_type', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}