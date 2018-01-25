<?php
/* 
 * 添加餐饮页
 */

class Controller_Admin_Service_Addrestaurant extends Controller_Admin_App
{

	/**
	 * 添加餐饮页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			//设定View所需变量
			$data['error_message'] = '';
			$data['area_list'] = Model_Area::GetAreaList(array('active_only' => true));
			$data['restaurant_type_list'] = Model_RestaurantType::GetRestaurantTypeListAll();
			$data['input_restaurant_name'] = '';
			$data['input_restaurant_area'] = '';
			$data['input_restaurant_type'] = '';
			$data['input_restaurant_price_min'] = '';
			$data['input_restaurant_price_max'] = '';
			$data['input_restaurant_status'] = '';
			
			if(isset($_POST['page'])) {
				$error_message_list = array();
				$file_tmp_list = array();
				
				//数据来源检验
				if($_POST['page'] == 'add_restaurant') {
					if(isset($_POST['restaurant_name']) && isset($_POST['restaurant_area']) && isset($_POST['restaurant_type']) 
							&& isset($_POST['restaurant_price_min']) && isset($_POST['restaurant_price_max']) && isset($_POST['restaurant_status'])) {
						//添加餐饮用数据生成
						$param_insert_restaurant = array(
							'restaurant_name' => $_POST['restaurant_name'],
							'restaurant_area' => $_POST['restaurant_area'],
							'restaurant_type' => $_POST['restaurant_type'],
							'restaurant_price_min' => $_POST['restaurant_price_min'],
							'restaurant_price_max' => $_POST['restaurant_price_max'],
							'restaurant_status' => $_POST['restaurant_status'],
						);
						
						//输入内容检查
						$result_check = Model_Restaurant::CheckInsertRestaurant($param_insert_restaurant);
						
						if($result_check['result']) {
							//数据添加
							$result_insert = Model_Restaurant::InsertRestaurant($param_insert_restaurant);
							$restaurant_id = $result_insert[0];
							
							if($result_insert) {
								//添加成功 页面跳转
								$_SESSION['add_restaurant_success'] = true;
								header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/restaurant_detail/' . $restaurant_id . '/');
								exit;
							} else {
								$error_message_list[] = '数据库错误：数据添加失败';
							}
						} else {
							foreach($result_check['error'] as $insert_error) {
								switch($insert_error) {
									case 'empty_name':
										$error_message_list[] = '餐饮名不能为空';
										break;
									case 'nonum_price':
										$error_message_list[] = '餐饮的价格必须为数字';
										break;
									case 'nonum_area':
										$error_message_list[] = '请选择餐饮所属地区';
										break;
									case 'nonum_type':
										$error_message_list[] = '请选择餐饮类型';
										break;
									case 'nobool_status':
										$error_message_list[] = '请选择公开状态';
										break;
									case 'minus_price':
										$error_message_list[] = '价格不能为负';
										break;
									case 'reverse_price':
										$error_message_list[] = '底价不能高于顶价';
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
					$data['input_restaurant_name'] = isset($_POST['restaurant_name']) ? $_POST['restaurant_name'] : '';
					$data['input_restaurant_area'] = isset($_POST['restaurant_area']) ? $_POST['restaurant_area'] : '';
					$data['input_restaurant_type'] = isset($_POST['restaurant_type']) ? $_POST['restaurant_type'] : '';
					$data['input_restaurant_status'] = isset($_POST['restaurant_status']) ? $_POST['restaurant_status'] : '';
					$data['input_restaurant_price_min'] = isset($_POST['restaurant_price_min']) ? $_POST['restaurant_price_min'] : '';
					$data['input_restaurant_price_max'] = isset($_POST['restaurant_price_max']) ? $_POST['restaurant_price_max'] : '';
					
					//输出错误信息
					if(count($error_message_list)) {
						$data['error_message'] = implode('<br/>', $error_message_list);
					}
				} else {
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
			}
			
			//调用View
			return Response::forge(View::forge($this->template . '/admin/service/add_restaurant', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}

}