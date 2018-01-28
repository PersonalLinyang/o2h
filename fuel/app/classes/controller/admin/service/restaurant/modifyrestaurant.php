<?php
/* 
 * 餐饮信息修改页
 */

class Controller_Admin_Service_Restaurant_Modifyrestaurant extends Controller_Admin_App
{

	/**
	 * 餐饮信息修改页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($restaurant_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		try {
			if(!is_numeric($restaurant_id)) {
				//餐饮ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 11)) {
				//当前登陆用户不具备修改餐饮的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//获取原本餐饮信息
				$restaurant = Model_Restaurant::SelectRestaurant(array('restaurant_id' => $restaurant_id, 'active_only' => true));
				
				if(!$restaurant) {
					//不存在该ID的餐饮
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 4) && $restaurant['created_by'] != $_SESSION['login_user']['id']) {
					//该ID的餐饮为其他用户创建且当前登陆用户不具备编辑他人创建餐饮的权限
					return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
					exit;
				}
				
				//页面标题
				$data['page_title'] ='餐饮信息修改';
				//表单页面索引
				$data['form_page_index'] = 'modify_restaurant';
				//返回页URL
				$data['return_page_url'] = '/admin/restaurant_detail/' . $restaurant_id . '/';
				if(isset($_SERVER['HTTP_REFERER'])) {
					if(strstr($_SERVER['HTTP_REFERER'], 'admin/restaurant_list')) {
						$data['return_page_url'] = $_SERVER['HTTP_REFERER'];
					}
				}
				
				//获取地区列表
				$data['area_list'] = Model_Area::GetAreaList(array('active_only' => true));
				//获取餐饮类型列表
				$data['restaurant_type_list'] = Model_RestaurantType::SelectRestaurantTypeList(array('active_only' => true));
				
				//form控件默认值设定
				$data['input_restaurant_name'] = $restaurant['restaurant_name'];
				$data['input_restaurant_area'] = $restaurant['restaurant_area'];
				$data['input_restaurant_type'] = $restaurant['restaurant_type'];
				$data['input_restaurant_price_min'] = $restaurant['restaurant_price_min'];
				$data['input_restaurant_price_max'] = $restaurant['restaurant_price_max'];
				$data['input_restaurant_status'] = $restaurant['restaurant_status'];
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是餐饮信息修改页
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_restaurant_name'] = isset($_POST['restaurant_name']) ? trim($_POST['restaurant_name']) : $data['input_restaurant_name'];
						$data['input_restaurant_area'] = isset($_POST['restaurant_area']) ? $_POST['restaurant_area'] : $data['input_restaurant_area'];
						$data['input_restaurant_type'] = isset($_POST['restaurant_type']) ? $_POST['restaurant_type'] : $data['input_restaurant_type'];
						$data['input_restaurant_price_min'] = isset($_POST['restaurant_price_min']) ? trim($_POST['restaurant_price_min']) : $data['input_restaurant_price_min'];
						$data['input_restaurant_price_max'] = isset($_POST['restaurant_price_max']) ? trim($_POST['restaurant_price_max']) : $data['input_restaurant_price_max'];
						$data['input_restaurant_status'] = isset($_POST['restaurant_status']) ? $_POST['restaurant_status'] : $data['input_restaurant_status'];
						
						//修改酒店用数据生成
						$params_update = array(
							'restaurant_id' => $restaurant_id,
							'restaurant_name' => $data['input_restaurant_name'],
							'restaurant_area' => $data['input_restaurant_area'],
							'restaurant_type' => $data['input_restaurant_type'],
							'restaurant_price_min' => $data['input_restaurant_price_min'],
							'restaurant_price_max' => $data['input_restaurant_price_max'],
							'restaurant_status' => $data['input_restaurant_status'],
							'created_by' => $restaurant['created_by'],
							'modified_by' => $_SESSION['login_user']['id'],
						);
						
						//更新内容检查
						$result_check = Model_Restaurant::CheckEditRestaurant($params_update);
						
						if($result_check['result']) {
							//更新景点信息
							$result_update = Model_Restaurant::UpdateRestaurant($params_update);
							
							if($result_update) {
								//更新成功 页面跳转
								$_SESSION['modify_restaurant_success'] = true;
								header('Location: //' . $_SERVER['HTTP_HOST'] . '/admin/restaurant_detail/' . $restaurant_id . '/');
								exit;
							} else {
								$error_message_list[] = '数据库错误：数据添加失败';
							}
						} else {
							//获取错误信息
							foreach($result_check['error'] as $update_error) {
								switch($update_error) {
									case 'empty_restaurant_name': 
										$error_message_list[] = '请输入餐饮店名';
										break;
									case 'long_restaurant_name': 
										$error_message_list[] = '餐饮店名不能超过100字';
										break;
									case 'dup_restaurant_name': 
										$error_message_list[] = '该餐饮店名与其他餐饮店重复,请选用其他餐饮店名';
										break;
									case 'empty_restaurant_area': 
										$error_message_list[] = '请选择餐饮店所属地区';
										break;
									case 'empty_restaurant_type': 
										$error_message_list[] = '请选择餐饮店类别';
										break;
									case 'noint_restaurant_price_min': 
									case 'minus_restaurant_price_min': 
									case 'noint_restaurant_price_max': 
									case 'minus_restaurant_price_max': 
										$error_message_list[] = '请在价格部分输入非负整数';
										break;
									default:
										$error_message_list[] = '发生系统错误,请重新尝试添加';
										break;
								}
							}
						}
						
						//输出错误信息
						$error_message_list = array_unique($error_message_list);
						if(count($error_message_list)) {
							$data['error_message'] = implode('<br/>', $error_message_list);
						}
					}
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/restaurant/edit_restaurant', $data, false));
			}
//		} catch (Exception $e) {
//			//发生系统异常
//			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
//		}
	}
	
	/**
	 * 餐饮公开状态更新
	 * @access  public
	 * @return  Response
	 */
	public function action_modifyrestaurantstatus($param = null)
	{
//		try {
			if(isset($_POST['page'], $_POST['modify_id'], $_POST['modify_value'])) {
				if(is_numeric($_POST['modify_id']) && $_POST['page'] == 'restaurant_detail') {
					$restaurant_id = $_POST['modify_id'];
					
					//获取餐饮信息
					$restaurant = Model_Restaurant::SelectRestaurant(array('restaurant_id' => $restaurant_id, 'active_only' => true));
					
					if($restaurant) {
						if($restaurant['created_by'] == $_SESSION['login_user']['id']) {
							//是否具备餐饮编辑权限
							$edit_able_flag = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 11);
						} else {
							//是否具备修改其他用户所登陆的餐饮信息权限
							$edit_able_flag = Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 4);
						}
						
						if($edit_able_flag) {
							$params_update = array(
								'restaurant_id' => $restaurant_id,
								'restaurant_status' => $_POST['modify_value'],
							);
							
							$result_check = Model_Restaurant::CheckUpdateRestaurantStatus($params_update);
							
							if($result_check['result']) {
								//数据更新
								$result_update = Model_Restaurant::UpdateRestaurantStatus($params_update);
								
								if($result_update) {
									//更新成功
									$_SESSION['modify_restaurant_status_success'] = true;
									header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/restaurant_detail/' . $_POST['modify_id'] . '/');
									exit;
								}
							}
						}
					}
				}
			}
			
			//更新失敗
			$_SESSION['modify_restaurant_status_error'] = true;
			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/restaurant_detail/' . $_POST['modify_id'] . '/');
			exit;
//		} catch (Exception $e) {
//			//发生系统异常
//			$_SESSION['modify_restaurant_status_error'] = true;
//			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/restaurant_detail/' . $_POST['modify_id'] . '/');
//			exit;
//		}
	}

}