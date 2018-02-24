<?php
/* 
 * 成本项目一览页
 */

class Controller_Admin_Business_Customercosttype_Customercosttypelist extends Controller_Admin_App
{

	/**
	 * 获取成本项目列表
	 * @access  public
	 * @return  Response
	 */
	public function action_apicustomercosttypelist($page = null)
	{
		$result = array('result' => false, 'customer_cost_type_list' => array());
		try {
			$allow_page_list = array('edit_customer');
			
			if(isset($_POST['page'])) {
				if(in_array($_POST['page'], $allow_page_list)) {
					$customer_cost_type_list = Model_Customercosttype::SelectCustomerCostTypeList(array('active_only' => true));
					$result = array('result' => true, 'customer_cost_type_list' => $customer_cost_type_list);
				}
			}
		} catch (Exception $e) {
		}
		return json_encode($result);
	}

}