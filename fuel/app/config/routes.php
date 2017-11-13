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
	'admin/delete_checked_spot'      => 'admin/service/spotlist/deletecheckedspot',
	
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
