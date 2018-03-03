<?php
/* 
 * 首页
 */

class Controller_Index extends Controller_App
{

	/**
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		try {
			//共同Header调用
			$data['header'] = Request::forge('common/header')->execute()->response();
			//共同Footer调用
			$data['footer'] = Request::forge('common/footer')->execute()->response();

			//获取旅游路线列表
			$route_list = Model_Route::SelectRouteSimpleList(array('route_status' => array(1), 'active_only' => true));
			$data['route_list'] = $route_list ? $route_list : array();

			//获取景点列表
			$spot_list = Model_Spot::SelectSpotList(array('spot_status' => array(1), 'active_only' => true));
			$data['spot_list'] = $spot_list ? $spot_list['spot_list'] : array();

			//获取地区列表
			$area_list = Model_Area::SelectAreaList(array('active_only' => true));
			$data['area_list'] = $area_list ? $area_list : array();

			//获取酒店类型列表
			$hotel_type_list = Model_Hoteltype::SelectHotelTypeList(array('active_only' => true));
			$data['hotel_type_list'] = $hotel_type_list ? $hotel_type_list : array();

			//获取房型列表
			$room_type_list = Model_Roomtype::SelectRoomTypeList(array('active_only' => true));
			$data['room_type_list'] = $room_type_list ? $room_type_list : array();

			//旅行目标
			$travel_reason_list = Model_Travelreason::SelectTravelReasonList(array('active_only' => true));
			$data['travel_reason_list'] = $travel_reason_list ? $travel_reason_list : array();
			
			//View调用
			return Response::forge(View::forge($this->template . '/index', $data, false));
		} catch (Exception $e) {
			//发生系统错误
			return Response::forge(View::forge($this->template . '/error/system_error', $data, false));
		}
	}
	
}