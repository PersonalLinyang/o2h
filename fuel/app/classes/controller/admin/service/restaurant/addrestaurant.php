<?php
/* 
 * 添加餐饮店页
 */

class Controller_Admin_Service_Restaurant_Addrestaurant extends Controller_Admin_App
{

	/**
	 * 添加餐饮店页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		try {
			if(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 11)) {
				//当前登陆用户不具备添加餐饮店的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//页面标题
				$data['page_title'] ='添加餐饮店';
				//表单页面索引
				$data['form_page_index'] = 'add_restaurant';
				//返回页URL
				$data['return_page_url'] = '/admin/restaurant_list/';
				if(isset($_SERVER['HTTP_REFERER'])) {
					if(strstr($_SERVER['HTTP_REFERER'], 'admin/restaurant_list')) {
						$data['return_page_url'] = $_SERVER['HTTP_REFERER'];
					}
				}
				
				//获取地区列表
				$data['area_list'] = Model_Area::SelectAreaList(array('active_only' => true));
				//获取餐饮店类型列表
				$data['restaurant_type_list'] = Model_RestaurantType::SelectRestaurantTypeList(array('active_only' => true));
				
				//form控件默认值设定
				$data['input_restaurant_name'] = '';
				$data['input_restaurant_area'] = '';
				$data['input_restaurant_type'] = '';
				$data['input_restaurant_price_min'] = '';
				$data['input_restaurant_price_max'] = '';
				$data['input_restaurant_status'] = '';
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是添加餐饮店页
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_restaurant_name'] = isset($_POST['restaurant_name']) ? trim($_POST['restaurant_name']) : $data['input_restaurant_name'];
						$data['input_restaurant_area'] = isset($_POST['restaurant_area']) ? $_POST['restaurant_area'] : $data['input_restaurant_area'];
						$data['input_restaurant_type'] = isset($_POST['restaurant_type']) ? $_POST['restaurant_type'] : $data['input_restaurant_type'];
						$data['input_restaurant_price_min'] = isset($_POST['restaurant_price_min']) ? trim($_POST['restaurant_price_min']) : $data['input_restaurant_price_min'];
						$data['input_restaurant_price_max'] = isset($_POST['restaurant_price_max']) ? trim($_POST['restaurant_price_max']) : $data['input_restaurant_price_max'];
						$data['input_restaurant_status'] = isset($_POST['restaurant_status']) ? $_POST['restaurant_status'] : $data['input_restaurant_status'];
						
						//添加餐饮店用数据生成
						$params_insert = array(
							'restaurant_id' => '',
							'restaurant_name' => $data['input_restaurant_name'],
							'restaurant_area' => $data['input_restaurant_area'],
							'restaurant_type' => $data['input_restaurant_type'],
							'restaurant_price_min' => $data['input_restaurant_price_min'],
							'restaurant_price_max' => $data['input_restaurant_price_max'],
							'restaurant_status' => $data['input_restaurant_status'],
							'created_by' => $_SESSION['login_user']['id'],
							'modified_by' => $_SESSION['login_user']['id'],
						);
						
						//输入内容检查
						$result_check = Model_Restaurant::CheckEditRestaurant($params_insert);
						
						if($result_check['result']) {
							//添加餐饮店
							$result_insert = Model_Restaurant::InsertRestaurant($params_insert);
							
							if($result_insert) {
								//添加成功 页面跳转
								$_SESSION['add_restaurant_success'] = true;
								header('Location: //' . $_SERVER['HTTP_HOST'] . '/admin/restaurant_detail/' . $result_insert . '/');
								exit;
							}
						} else {
							//获取错误信息
							foreach($result_check['error'] as $insert_error) {
								switch($insert_error) {
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
									case 'noint_restaurant_price': 
									case 'minus_restaurant_price': 
										$error_message_list[] = '请在价格部分输入非负整数';
										break;
									case 'error_restaurant_price': 
										$error_message_list[] = '最低价不能高于最高价';
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
				return Response::forge(View::forge($this->template . '/admin/service/restaurant/edit_restaurant', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}