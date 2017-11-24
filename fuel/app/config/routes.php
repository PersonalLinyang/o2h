<?php
return array(
	'_root_'  => 'index',  // The default route
	'_404_'   => '404',    // The main 404 route

	//管理画面
	'admin' => 'admin/index',
	
	//景点管理
	'admin/spot_list'                => 'admin/service/spotlist/index',
	'admin/spot_list/(:page)'        => 'admin/service/spotlist/index/$1',
	'admin/add_spot'                 => 'admin/service/addspot/index',
	'admin/spot_detail/(:spot_id)'   => 'admin/service/spotdetail/index/$1',
	'admin/modify_spot_status'       => 'admin/service/spotdetail/modifyspotstatus',
	'admin/modify_spot/(:spot_id)'   => 'admin/service/modifyspot/index/$1',
	'admin/delete_spot'              => 'admin/service/spotlist/deletespot',
	'admin/delete_checked_spot'      => 'admin/service/spotlist/deletecheckedspot',
	
	//景点类别管理
	'admin/spot_type_list'           => 'admin/service/spottypelist',
	'admin/add_spot_type'            => 'admin/service/addspottype',
	'admin/modify_spot_type/(:spot_type_id)'     => 'admin/service/modifyspottype/index/$1',
	'admin/delete_spot_type'         => 'admin/service/spottypelist/deletespottype',
	
	//酒店管理
	'admin/hotel_list'               => 'admin/service/hotellist/index',
	'admin/hotel_list/(:page)'       => 'admin/service/hotellist/index/$1',
	'admin/add_hotel'                => 'admin/service/addhotel/index',
	'admin/hotel_detail/(:hotel_id)' => 'admin/service/hoteldetail/index/$1',
	'admin/modify_hotel_status'      => 'admin/service/hoteldetail/modifyhotelstatus',
	'admin/modify_hotel/(:hotel_id)' => 'admin/service/modifyhotel/index/$1',
	'admin/delete_hotel'             => 'admin/service/hotellist/deletehotel',
	'admin/delete_checked_hotel'     => 'admin/service/hotellist/deletecheckedhotel',
	
	//酒店类别管理
	'admin/hotel_type_list'          => 'admin/service/hoteltypelist',
	'admin/add_hotel_type'           => 'admin/service/addhoteltype',
	'admin/modify_hotel_type/(:hotel_type_id)'   => 'admin/service/modifyhoteltype/index/$1',
	'admin/delete_hotel_type'        => 'admin/service/hoteltypelist/deletehoteltype',
	
	//餐饮管理
	'admin/restaurant_list'               => 'admin/service/restaurantlist/index',
	'admin/restaurant_list/(:page)'       => 'admin/service/restaurantlist/index/$1',
	'admin/add_restaurant'                => 'admin/service/addrestaurant/index',
	'admin/restaurant_detail/(:restaurant_id)' => 'admin/service/restaurantdetail/index/$1',
	'admin/modify_restaurant_status'      => 'admin/service/restaurantdetail/modifyrestaurantstatus',
	'admin/modify_restaurant/(:restaurant_id)' => 'admin/service/modifyrestaurant/index/$1',
	'admin/delete_restaurant'             => 'admin/service/restaurantlist/deleterestaurant',
	'admin/delete_checked_restaurant'     => 'admin/service/restaurantlist/deletecheckedrestaurant',
	
	//餐饮类别管理
	'admin/restaurant_type_list'          => 'admin/service/restauranttypelist',
	'admin/add_restaurant_type'           => 'admin/service/addrestauranttype',
	'admin/modify_restaurant_type/(:restaurant_type_id)'   => 'admin/service/modifyrestauranttype/index/$1',
	'admin/delete_restaurant_type'        => 'admin/service/restauranttypelist/deleterestauranttype',
	
	//旅游路线管理
	'admin/add_route'                => 'admin/service/addroute',
	'admin/add_route/spot_list'      => 'admin/service/addroute/spotlist',
	
	//系统权限管理
	'admin/permission_list'          => 'admin/user/permissionlist/index',
	'admin/add_master_group'         => 'admin/user/addmastergroup/index',
	'admin/add_sub_group'            => 'admin/user/addsubgroup/index',
	'admin/add_function'             => 'admin/user/addfunction/index',
	'admin/add_authority'            => 'admin/user/addauthority/index',
	'admin/modify_master_group'      => 'admin/user/modifymastergroup/index',
	'admin/modify_sub_group'         => 'admin/user/modifysubgroup/index',
	'admin/modify_function'          => 'admin/user/modifyfunction/index',
	'admin/modify_authority'         => 'admin/user/modifyauthority/index',
	'admin/delete_master_group'      => 'admin/user/permissionlist/deletemastergroup',
	'admin/delete_sub_group'         => 'admin/user/permissionlist/deletesubgroup',
	'admin/delete_function'          => 'admin/user/permissionlist/deletefunction',
	'admin/delete_authority'         => 'admin/user/permissionlist/deleteauthority',
);
