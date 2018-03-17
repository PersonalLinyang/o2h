<?php
/* 
 * 首页
 */

class Controller_Spot_Spotdetail extends Controller_App
{

	/**
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index($spot_id)
	{
		$data = array();
		
		try {
			//获取景点信息
			$spot = Model_Spot::SelectSpot(array('spot_id' => $spot_id, 'spot_status' => array(1), 'active_only' => true));
			$data['spot'] = $spot ? $spot : array();
			
			//View调用
			return Response::forge(View::forge($this->template . '/spot/spot_detail', $data, false));
		} catch (Exception $e) {
			//发生系统错误
			return Response::forge(View::forge($this->template . '/error/system_error', $data, false));
		}
	}
	
}