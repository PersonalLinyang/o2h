<header>
	<div class="header-area">
		<div class="header-main">
			<div class="title"><img class="header-logo" src="/assets/img/pc/admin/logo.png" alt="o2h企业情报管理系统" /></div>
			<div class="content">
				<div>欢迎使用o2h企业情报管理系统</div>
				<div class="button">
					<div class="logout">
						<form id="logout-form" action="" method="post">
							<input type="hidden" name="logout">
							<a id="link-logout">退出登录</a>
						</form>
					</div>
					<div class="profile"><a href="/admin/profile/">个人信息</a></div>
				</div>
			</div>
		</div>
		<div class="header-navi">
			<ul>
				<li><a href="/admin/" class="main-navi-link">首页</a></li>
				<?php if(in_array('2', $login_user_permission['master_group'])) : ?>
				<li class="js-navi-sub">
					<a class="main-navi-link">服务管理</a>
					<div class="header-sub-navi">
						<ul>
<!--
							<li><a href="/admin/" class="sub-navi-link">线路管理</a></li>
							<li><a href="/admin/spot_list/" class="sub-navi-link">景点管理</a></li>
							<li><a href="/admin/" class="sub-navi-link">酒店管理</a></li>
							<li><a href="/admin/" class="sub-navi-link">餐饮管理</a></li>
							<li><a href="/admin/" class="sub-navi-link">特辑网页管理</a></li>
-->
						</ul>
					</div>
				</li>
				<?php endif; ?>
				<?php if(in_array('3', $login_user_permission['master_group'])) : ?>
				<li class="js-navi-sub">
					<a class="main-navi-link">业务管理</a>
					<div class="header-sub-navi">
						<ul>
<!--
							<li><a href="/admin/" class="sub-navi-link">顾客管理</a></li>
							<li><a href="/admin/" class="sub-navi-link">日程管理</a></li>
-->
						</ul>
					</div>
				</li>
				<?php endif; ?>
				<?php if(in_array('4', $login_user_permission['master_group'])) : ?>
				<li class="js-navi-sub">
					<a class="main-navi-link">财务管理</a>
					<div class="header-sub-navi">
						<ul>
<!--
							<li><a href="/admin/" class="sub-navi-link">手动进账登记</a></li>
							<li><a href="/admin/" class="sub-navi-link">手动出账登记</a></li>
							<li><a href="/admin/" class="sub-navi-link">常规进出账设定</a></li>
							<li><a href="/admin/" class="sub-navi-link">进出账明细查询</a></li>
-->
						</ul>
					</div>
				</li>
				<?php endif; ?>
				<?php if(in_array('5', $login_user_permission['master_group'])) : ?>
				<li class="js-navi-sub">
					<a class="main-navi-link">人事管理</a>
					<div class="header-sub-navi">
						<ul>
							<?php if(in_array('7', $login_user_permission['sub_group'])) : ?>
							<li><a href="/admin/permission_list/" class="sub-navi-link">系统权限管理</a></li>
							<?php endif; ?>
<!--
							<li><a href="/admin/user_list/" class="sub-navi-link">公司成员管理</a></li>
							<li><a href="/admin/" class="sub-navi-link">新员工注册</a></li>
							<li><a href="/admin/" class="sub-navi-link">业务权限管理</a></li>
-->
						</ul>
					</div>
				</li>
				<?php endif; ?>
				<?php if(in_array('6', $login_user_permission['master_group'])) : ?>
				<li class="js-navi-sub">
					<a class="main-navi-link">综合统筹</a>
					<div class="header-sub-navi">
						<ul>
<!--
							<li><a href="/admin/" class="sub-navi-link">客户量结算</a></li>
							<li><a href="/admin/" class="sub-navi-link">财务盈亏结算</a></li>
							<li><a href="/admin/" class="sub-navi-link">服务满意度管理</a></li>
							<li><a href="/admin/" class="sub-navi-link">周转资金查询</a></li>
-->
						</ul>
					</div>
				</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</header>