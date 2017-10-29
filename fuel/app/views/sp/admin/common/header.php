<div id="header-follow">
	<div class="header-logo-area">
		<div class="header-logo"><img src="/assets/img/sp/admin/logo.png" alt="o2h opporutiny to happiness" /></div>
		<div class="header-menu-link">
			<span></span><span></span><span></span>
		</div>
	</div>
</div>
<div id="menu-area">
	<div class="menu-hp">
		<a href="/admin/"><p class="menu-hp-button">首页</p></a>
	</div>
	<div class="menu-main">
		<p class="menu-main-button" data-link="product">服务管理</p>
		<p class="menu-main-button" data-link="business">业务管理</p>
		<p class="menu-main-button" data-link="financial">财务管理</p>
		<?php //if(isset($login_user_permission[5][7])) : ?>
		<p class="menu-main-button" data-link="user">人事管理</p>
		<?php //endif; ?>
		<p class="menu-main-button" data-link="company">企业管理</p>
	</div>
	<div class="menu-sub">
		<div class="menu-sub-panel">
			<div class="menu-sub-list" id="menu-sub-list-product">
				<a href="/admin/"><p class="menu-main-button">路线管理</p></a>
				<a href="/admin/"><p class="menu-main-button">景点管理</p></a>
				<a href="/admin/"><p class="menu-main-button">酒店管理</p></a>
				<a href="/admin/"><p class="menu-main-button">餐饮管理</p></a>
				<a href="/admin/"><p class="menu-main-button">特级网页管理</p></a>
			</div>
			<div class="menu-sub-list" id="menu-sub-list-business">
				<a href="/admin/"><p class="menu-main-button">顾客管理</p></a>
				<a href="/admin/"><p class="menu-main-button">日程管理</p></a>
			</div>
			<div class="menu-sub-list" id="menu-sub-list-financial">
				<a href="/admin/"><p class="menu-main-button">手动进账管理</p></a>
				<a href="/admin/"><p class="menu-main-button">手动出账管理</p></a>
				<a href="/admin/"><p class="menu-main-button">常规进出账设定</p></a>
				<a href="/admin/"><p class="menu-main-button">进出账明细查询</p></a>
			</div>
			<div class="menu-sub-list" id="menu-sub-list-user">
				<?php //if(isset($login_user_permission[5][7][1])) : ?>
				<a href="/admin/user_list/"><p class="menu-main-button">公司成员管理</p></a>
				<?php //endif; ?>
				<a href="/admin/permission_list/"><p class="menu-main-button">系统权限管理</p></a>
				<a href="/admin/"><p class="menu-main-button">新员工注册</p></a>
				<a href="/admin/"><p class="menu-main-button">业务权限管理</p></a>
			</div>
			<div class="menu-sub-list" id="menu-sub-list-company">
				<a href="/admin/"><p class="menu-main-button">客户量管理</p></a>
				<a href="/admin/"><p class="menu-main-button">财务盈亏管理</p></a>
				<a href="/admin/"><p class="menu-main-button">服务满意度管理</p></a>
				<a href="/admin/"><p class="menu-main-button">周转资金查询</p></a>
			</div>
		</div>
	</div>
</div>
<div id="header-shadow">
</div>