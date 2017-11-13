<?php
/* 
 * 景点一览页
 */

class Controller_Admin_Service_Spotlist extends Controller_Admin_App
{

	/**
	 * 景点一览
	 * @access  public
	 * @return  Response
	 */
	public function action_index($page = null)
	{
		$data = array();
		
		if(!is_numeric($page)) {
			$page = 1;
		}
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['success_message'] = '';
			$data['error_message'] = '';
			$data['area_list'] = Model_Area::GetAreaListAll();
			$data['spot_type_list'] = Model_SpotType::GetSpotTypeListAll();
			$data['spot_list'] = array();
			$data['spot_count'] = 0;
			$data['start_number'] = 0;
			$data['end_number'] = 0;
			$data['page'] = $page;
			$data['page_number'] = 1;
			$num_per_page = 20;
			
			$spot_count = Model_Spot::GetSpotTotalCount();
			if($spot_count) {
				$data['spot_count'] = $spot_count;
				
				$params = array(
					'page' => $page,
					'num_per_page' => $num_per_page,
				);
				$result_select = Model_Spot::SelectSpotList($params);
				if($result_select) {
					$data['spot_list'] = $result_select['spot_list'];
					$data['start_number'] = $result_select['start_number'];
					$data['end_number'] = $result_select['end_number'];
				}
				
				if($spot_count > $num_per_page) {
					$data['page_number'] = ceil($spot_count/$num_per_page);
				}
			}
			
			if(isset($_SESSION['delete_spot_success'])) {
				$data['success_message'] = '景点削除成功';
				unset($_SESSION['delete_spot_success']);
			}
			if(isset($_SESSION['delete_spot_error'])) {
				$data['error_message'] = '景点削除失敗';
				unset($_SESSION['delete_spot_error']);
			}

			//调用View
			return Response::forge(View::forge($this->template . '/admin/service/spot_list', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}
	
	/**
	 * 削除景点
	 * @access  public
	 * @return  Response
	 */
	public function action_deletespot($param = null)
	{
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['delete_id'], $_POST['page'])) {
			if($_POST['page'] == 'spot_list') {
				//删除信息检查
				$result_check = Model_Spot::CheckDeleteSpotById($_POST['delete_id']);
				if($result_check['result']) {
					//数据删除
					$result_delete = Model_Spot::DeleteSpotById($_POST['delete_id']);
					
					if($result_delete) {
						$_SESSION['delete_spot_success'] = true;
						header('Location: ' . $_SERVER['HTTP_REFERER']);
						exit;
					}
				}
			}
//		}
		$_SESSION['delete_spot_error'] = true;
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		exit;
	}

}