<?php
/* 
 * 景点类别一览页
 */

class Controller_Admin_Service_Spottype_Spottypelist extends Controller_Admin_App
{

	/**
	 * 景点类别一览
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		try {
			if(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 10)) {
				//当前登陆用户不具备景点类别管理的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['success_message'] = '';
				$data['error_message'] = '';
				
				//获取返回一览页时的一览页URL
				$data['spot_list_url'] = '/admin/spot_list/';
				if(isset($_SERVER['HTTP_REFERER'])) {
					if(strstr($_SERVER['HTTP_REFERER'], 'admin/spot_list')) {
						//通过一览页链接进入
						$data['spot_list_url'] = $_SERVER['HTTP_REFERER'];
					} elseif(strstr($_SERVER['HTTP_REFERER'], 'admin/add_spot_type') || strstr($_SERVER['HTTP_REFERER'], 'admin/modify_spot_type')) {
						if(isset($_SESSION['url_return_spot_list'])) {
							$data['spot_list_url'] = $_SESSION['url_return_spot_list'];
						}
					}
				}
				//暂时保留一览页URL
				$_SESSION['url_return_spot_list'] = $data['spot_list_url'];
				
				//获取景点类别信息
				$params_select = array(
					'active_only' => true,
					'spot_count_flag' => true,
				);
				
				$data['spot_type_list'] = Model_SpotType::SelectSpotTypeList($params_select);
				
				//景点类别添加结果处理
				if(isset($_SESSION['add_spot_type_success'])) {
					$data['success_message'] = '景点类别添加成功';
					unset($_SESSION['add_spot_type_success']);
				}
				if(isset($_SESSION['add_spot_type_error'])) {
					switch($_SESSION['add_spot_type_error']) {
						case 'error_excel':
							$data['error_message'] = '景点类别添加成功,但批量导入景点用模板未能成功更新,请联系系统开发人员进行手动修复';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新添加';
							break;
					}
					unset($_SESSION['add_spot_type_error']);
				}
				
				//景点类别信息修改结果处理
				if(isset($_SESSION['modify_spot_type_success'])) {
					$data['success_message'] = '景点类别修改成功';
					unset($_SESSION['modify_spot_type_success']);
				}
				if(isset($_SESSION['modify_spot_type_error'])) {
					switch($_SESSION['modify_spot_type_error']) {
						case 'error_excel':
							$data['error_message'] = '景点类别修改成功,但批量导入景点用模板未能成功更新,请联系系统开发人员进行手动修复';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新修改';
							break;
					}
					unset($_SESSION['modify_spot_type_error']);
				}
				
				//景点类别削除结果处理
				if(isset($_SESSION['delete_spot_type_success'])) {
					$data['success_message'] = '景点类别削除成功';
					unset($_SESSION['delete_spot_type_success']);
				}
				if(isset($_SESSION['delete_spot_type_error'])) {
					switch($_SESSION['delete_spot_type_error']) {
						case 'error_permission':
							$data['error_message'] = '您不具备删除景点类别的权限';
							break;
						case 'error_spot_type_id':
							$data['error_message'] = '您要删除的景点类别不存在,请确认该景点类别是否已经被删除';
							break;
						case 'error_spot_list':
							$data['error_message'] = '尚存在属于该类别的景点,请在修改这些景点的类别后重新尝试删除';
							break;
						case 'error_excel':
							$data['error_message'] = '景点类别削除成功,但批量导入景点用模板未能成功更新,请联系系统开发人员进行手动修复';
							break;
						case 'error_db':
							$data['error_message'] = '发生数据库错误,请重新尝试删除';
							break;
						default:
							$data['error_message'] = '发生系统错误,请尝试重新删除';
							break;
					}
					unset($_SESSION['delete_spot_type_error']);
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/service/spot_type/spot_type_list', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}