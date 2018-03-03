<?php
/* 
 * 首页
 */

class Controller_Interface_Home extends Controller_App
{

	/**
	 * 获取酒店类别列表
	 * @access  public
	 * @return  Response
	 */
	public function action_api_hotel_type_list($page = null)
	{
		$result = array('result' => false, 'hotel_type_list' => array());
		try {
			$allow_page_list = array('index');
			
			if(isset($_POST['page'])) {
				if(in_array($_POST['page'], $allow_page_list)) {
					$hotel_type_list = Model_Hoteltype::SelectHotelTypeList(array('active_only' => true));
					$result = array('result' => true, 'hotel_type_list' => $hotel_type_list);
				}
			}
		} catch (Exception $e) {
		}
		return json_encode($result);
	}

	/**
	 * 获取房型列表
	 * @access  public
	 * @return  Response
	 */
	public function action_api_room_type_list($page = null)
	{
		$result = array('result' => false, 'room_type_list' => array());
		try {
			$allow_page_list = array('index');
			
			if(isset($_POST['page'])) {
				if(in_array($_POST['page'], $allow_page_list)) {
					$room_type_list = Model_Roomtype::SelectRoomTypeList(array('active_only' => true));
					$result = array('result' => true, 'room_type_list' => $room_type_list);
				}
			}
		} catch (Exception $e) {
		}
		return json_encode($result);
	}

	/**
	 * 动态获取路线详细信息
	 * @access  public
	 * @return  Response
	 */
	public function action_api_route_info($param = null)
	{
		$result = array('result' => false, 'route_info' => array());
		try {
			$allow_page_list = array('index');
			
			if(isset($_POST['page'])) {
				if(in_array($_POST['page'], $allow_page_list)) {
					if(isset($_POST['route_id'])) {
						$route = Model_Route::SelectRoute(array('route_id' => $_POST['route_id'], 'route_status' => 1, 'active_only' => true));
						if($route) {
							$spot_list = array();
							foreach($route['detail_list'] as $detail) {
								foreach($detail['route_spot_list'] as $spot) {
									if(!in_array($spot, $spot_list)) {
										$spot_list[] = $spot;
									}
								}
							}
							$route_info = array(
								'route_id' => $route['route_id'],
								'route_name' => $route['route_name'],
								'route_description' => nl2br($route['route_description']),
								'route_price_min' => number_format($route['route_price_min']),
								'route_price_max' => number_format($route['route_price_max']),
								'detail_num' => count($route['detail_list']),
								'spot_list' => $spot_list,
							);
							$result = array('result' => true, 'route_info' => $route_info);
						}
					}
				}
			}
		} catch (Exception $e) {
		}
		return json_encode($result);
	}
	
}