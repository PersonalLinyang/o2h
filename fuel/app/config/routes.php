<?php
return array(
	'_root_'  => 'index',  // The default route
	'_404_'   => '404',    // The main 404 route

	//景点详情页
	'spot/(:spot_id)' => 'spot/spotdetail/index/$1',

	//个人专属
	'member' => 'member/index',

	//管理画面
	'admin' => 'admin/index',
	
	//景点管理
	'admin/spot_list'              => 'admin/service/spot/spotlist/index',
	'admin/spot_list/(:page)'      => 'admin/service/spot/spotlist/index/$1',
	'admin/spot_detail/(:spot_id)' => 'admin/service/spot/spotdetail/index/$1',
	'admin/add_spot'               => 'admin/service/spot/addspot/index',
	'admin/modify_spot/(:spot_id)' => 'admin/service/spot/modifyspot/index/$1',
	'admin/modify_spot_status'     => 'admin/service/spot/modifyspot/modifyspotstatus',
	'admin/import_spot'            => 'admin/service/spot/importspot/index',
	'admin/export_spot'            => 'admin/service/spot/exportspot/index',
	'admin/delete_spot'            => 'admin/service/spot/deletespot/index',
	'admin/delete_spot_checked'    => 'admin/service/spot/deletespot/deletespotchecked',
	
	//景点类别管理
	'admin/spot_type_list'                   => 'admin/service/spottype/spottypelist/index',
	'admin/add_spot_type'                    => 'admin/service/spottype/addspottype/index',
	'admin/modify_spot_type/(:spot_type_id)' => 'admin/service/spottype/modifyspottype/index/$1',
	'admin/delete_spot_type'                 => 'admin/service/spottype/deletespottype/index',
	
	//酒店管理
	'admin/hotel_list'               => 'admin/service/hotel/hotellist/index',
	'admin/hotel_list/(:page)'       => 'admin/service/hotel/hotellist/index/$1',
	'admin/hotel_detail/(:hotel_id)' => 'admin/service/hotel/hoteldetail/index/$1',
	'admin/add_hotel'                => 'admin/service/hotel/addhotel/index',
	'admin/modify_hotel/(:hotel_id)' => 'admin/service/hotel/modifyhotel/index/$1',
	'admin/modify_hotel_status'      => 'admin/service/hotel/modifyhotel/modifyhotelstatus',
	'admin/import_hotel'             => 'admin/service/hotel/importhotel/index',
	'admin/export_hotel'             => 'admin/service/hotel/exporthotel/index',
	'admin/delete_hotel'             => 'admin/service/hotel/deletehotel/index',
	'admin/delete_hotel_checked'     => 'admin/service/hotel/deletehotel/deletehotelchecked',
	
	//酒店类别管理
	'admin/hotel_type_list'                    => 'admin/service/hoteltype/hoteltypelist/index',
	'admin/add_hotel_type'                     => 'admin/service/hoteltype/addhoteltype/index',
	'admin/modify_hotel_type/(:hotel_type_id)' => 'admin/service/hoteltype/modifyhoteltype/index/$1',
	'admin/delete_hotel_type'                  => 'admin/service/hoteltype/deletehoteltype/index',
	
	//房型管理
	'admin/room_type_list'                   => 'admin/service/roomtype/roomtypelist/index',
	'admin/add_room_type'                    => 'admin/service/roomtype/addroomtype/index',
	'admin/modify_room_type/(:room_type_id)' => 'admin/service/roomtype/modifyroomtype/index/$1',
	'admin/delete_room_type'                 => 'admin/service/roomtype/deleteroomtype/index',
	
	//餐饮管理
	'admin/restaurant_list'                    => 'admin/service/restaurant/restaurantlist/index',
	'admin/restaurant_list/(:page)'            => 'admin/service/restaurant/restaurantlist/index/$1',
	'admin/restaurant_detail/(:restaurant_id)' => 'admin/service/restaurant/restaurantdetail/index/$1',
	'admin/add_restaurant'                     => 'admin/service/restaurant/addrestaurant/index',
	'admin/modify_restaurant/(:restaurant_id)' => 'admin/service/restaurant/modifyrestaurant/index/$1',
	'admin/modify_restaurant_status'           => 'admin/service/restaurant/modifyrestaurant/modifyrestaurantstatus',
	'admin/import_restaurant'                  => 'admin/service/restaurant/importrestaurant/index',
	'admin/export_restaurant'                  => 'admin/service/restaurant/exportrestaurant/index',
	'admin/delete_restaurant'                  => 'admin/service/restaurant/deleterestaurant/index',
	'admin/delete_restaurant_checked'          => 'admin/service/restaurant/deleterestaurant/deleterestaurantchecked',
	
	//餐饮类别管理
	'admin/restaurant_type_list'                         => 'admin/service/restauranttype/restauranttypelist/index',
	'admin/add_restaurant_type'                          => 'admin/service/restauranttype/addrestauranttype/index',
	'admin/modify_restaurant_type/(:restaurant_type_id)' => 'admin/service/restauranttype/modifyrestauranttype/index/$1',
	'admin/delete_restaurant_type'                       => 'admin/service/restauranttype/deleterestauranttype/index',
	
	//旅游路线管理
	'admin/route_list'               => 'admin/service/route/routelist/index',
	'admin/route_list/(:page)'       => 'admin/service/route/routelist/index/$1',
	'admin/route_detail/(:route_id)' => 'admin/service/route/routedetail/index/$1',
	'admin/add_route'                => 'admin/service/route/addroute/index',
	'admin/modify_route/(:route_id)' => 'admin/service/route/modifyroute/index/$1',
	'admin/modify_route_status'      => 'admin/service/route/modifyroute/modifyroutestatus',
	'admin/import_route'             => 'admin/service/route/importroute/index',
	'admin/export_route'             => 'admin/service/route/exportroute/index',
	'admin/delete_route'             => 'admin/service/route/deleteroute/index',
	'admin/delete_route_checked'     => 'admin/service/route/deleteroute/deleteroutechecked',
	
	//顾客管理
	'admin/customer_list'                  => 'admin/business/customer/customerlist/index',
	'admin/customer_list/(:page)'          => 'admin/business/customer/customerlist/index/$1',
	'admin/customer_detail/(:customer_id)' => 'admin/business/customer/customerdetail/index/$1',
	'admin/add_customer'                   => 'admin/business/customer/addcustomer/index',
	'admin/modify_customer/(:customer_id)' => 'admin/business/customer/modifycustomer/index/$1',
	'admin/modify_customer_status'         => 'admin/business/customer/modifycustomer/modifycustomerstatus',
	'admin/modify_customer_delete'         => 'admin/business/customer/modifycustomer/modifycustomerdelete',
	
	//收入管理
	'admin/income_list'                => 'admin/financial/income/incomelist/index',
	'admin/income_list/(:page)'        => 'admin/financial/income/incomelist/index/$1',
	'admin/income_detail/(:income_id)' => 'admin/financial/income/incomedetail/index/$1',
	'admin/add_income'                 => 'admin/financial/income/addincome/index',
	'admin/modify_income/(:income_id)' => 'admin/financial/income/modifyincome/index/$1',
	'admin/modify_income_status'       => 'admin/financial/income/modifyincome/modifyapprovalstatus',
	'admin/export_income'              => 'admin/financial/income/exportincome/index',
	'admin/delete_income'              => 'admin/financial/income/deleteincome/index',
	'admin/delete_income_checked'      => 'admin/financial/income/deleteincome/deleteincomechecked',
	
	//收入项目管理
	'admin/income_type_list'                     => 'admin/financial/incometype/incometypelist/index',
	'admin/add_income_type'                      => 'admin/financial/incometype/addincometype/index',
	'admin/modify_income_type/(:income_type_id)' => 'admin/financial/incometype/modifyincometype/index/$1',
	'admin/delete_income_type'                   => 'admin/financial/incometype/deleteincometype/index',
	
	//支出管理
	'admin/cost_list'                => 'admin/financial/cost/costlist/index',
	'admin/cost_list/(:page)'        => 'admin/financial/cost/costlist/index/$1',
	'admin/cost_detail/(:cost_id)' => 'admin/financial/cost/costdetail/index/$1',
	'admin/add_cost'                 => 'admin/financial/cost/addcost/index',
	'admin/modify_cost/(:cost_id)' => 'admin/financial/cost/modifycost/index/$1',
	'admin/export_cost'              => 'admin/financial/cost/exportcost/index',
	'admin/delete_cost'              => 'admin/financial/cost/deletecost/index',
	'admin/delete_cost_checked'      => 'admin/financial/cost/deletecost/deletecostchecked',
	
	//支出项目管理
	'admin/cost_type_list'                     => 'admin/financial/costtype/costtypelist/index',
	'admin/add_cost_type'                      => 'admin/financial/costtype/addcosttype/index',
	'admin/modify_cost_type/(:cost_type_id)' => 'admin/financial/costtype/modifycosttype/index/$1',
	'admin/delete_cost_type'                   => 'admin/financial/costtype/deletecosttype/index',
	
	//系统权限管理
	'admin/permission_list'                  => 'admin/user/permission/permissionlist/index',
	'admin/add_master_group'                 => 'admin/user/permission/addmastergroup/index',
	'admin/add_sub_group/(:master_group_id)' => 'admin/user/permission/addsubgroup/index/$1',
	'admin/add_function/(:sub_group_id)'     => 'admin/user/permission/addfunction/index/$1',
	'admin/add_authority/(:function_id)'     => 'admin/user/permission/addauthority/index/$1',
	'admin/modify_master_group/(:group_id)'  => 'admin/user/permission/modifymastergroup/index/$1',
	'admin/modify_sub_group/(:group_id)'     => 'admin/user/permission/modifysubgroup/index/$1',
	'admin/modify_function/(:function_id)'   => 'admin/user/permission/modifyfunction/index/$1',
	'admin/modify_authority/(:authority_id)' => 'admin/user/permission/modifyauthority/index/$1',
	'admin/delete_master_group'              => 'admin/user/permission/permissionlist/deletemastergroup',
	'admin/delete_sub_group'                 => 'admin/user/permission/permissionlist/deletesubgroup',
	'admin/delete_function'                  => 'admin/user/permission/permissionlist/deletefunction',
	'admin/delete_authority'                 => 'admin/user/permission/permissionlist/deleteauthority',
	
	//用户类型管理
	'admin/user_type_list'                   => 'admin/user/usertype/usertypelist/index',
	'admin/add_user_type'                    => 'admin/user/usertype/addusertype/index',
	'admin/modify_user_type/(:user_type_id)' => 'admin/user/usertype/modifyusertype/index/$1',
	'admin/delete_user_type'                 => 'admin/user/usertype/usertypelist/deleteusertype/',
	'admin/user_type_detail/(:user_type_id)' => 'admin/user/usertype/usertypedetail/index/$1',
);
