<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>株式会社O2H</title>
	<meta name="description" content="">
	<meta name="keywords" content="">
	<link rel="canonical" href="https://www.ltdo2h.com/">
	<?php echo Asset::css('pc/common.css'); ?>
	<?php echo Asset::css('pc/member/login.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('pc/common/google-analytics.js'); ?>
	<?php echo Asset::js('pc/common.js'); ?>
	<?php echo Asset::js('pc/member/login.js'); ?>
</head>
<body>
	<?php //echo $header; ?>
	<div class="content-area">
		<div class="div-form-area div-login-area">
			<p class="p-guide">已经注册为会员的用户请在这边登陆</p>
			<p class="p-title">登陆会员使用个人专属信息</p>
			<p class="p-title p-title-right">轻松快捷开启新旅程</p>
			<form action="" id="form-login" method="post">
				<p><input type="text" name="member_email_login" placeholder="请输入电子邮箱"></p>
				<p><input type="password" name="member_password_login" placeholder="请输入会员密码"></p>
			</form>
			<p class="p-warning"></p>
			<ul class="ul-button-group">
				<li class="btn-noactive"><div class="shine"></div>忘记密码</li>
				<li class="btn-active btn-login"><div class="shine"></div>登陆</li>
			</ul>
		</div>
		<div class="div-form-area div-access-area">
			<p class="p-guide">尚未注册为会员的用户请在这边注册</p>
			<p class="p-title">注册会员独享专属收藏空间</p>
			<p class="p-title p-title-right">只为您存在 使用更方便</p>
			<p class="p-must">*处为必填项目</p>
			<form action="" id="form-access" method="post">
				<table>
					<tr>
						<th>姓名*</th>
						<td><input type="text" name="member_name" placeholder="请输入姓名" value="<?php echo $input_member_name; ?>"></td>
					</tr>
					<tr>
						<th>电子邮箱*</th>
						<td><input type="text" name="member_email_access" placeholder="请输入电子邮箱" value="<?php echo $input_member_email_access; ?>"></td>
					</tr>
					<tr>
						<th>密码*</th>
						<td><input type="password" name="member_password_access" placeholder="请输入会员密码" value="<?php echo $input_member_password_access; ?>"></td>
					</tr>
					<tr>
						<th>确认密码*</th>
						<td><input type="password" name="member_repassword" placeholder="请再次输入会员密码" value="<?php echo $input_member_repassword; ?>"></td>
					</tr>
					<tr>
						<th>性别</th>
						<td>
							<input type="radio" name="member_gender" value="1" id="rdo-member-gender-1" <?php echo $input_member_gender == '1' ? 'checked ' : ''; ?>/>
							<label class="lbl-for-radio<?php echo $input_member_gender == '1' ? ' active' : ''; ?>" for="rdo-member-gender-1" data-for="rdo-member-gender">男</label>
							<input type="radio" name="member_gender" value="2" id="rdo-member-gender-2" <?php echo $input_member_gender == '2' ? 'checked ' : ''; ?>/>
							<label class="lbl-for-radio<?php echo $input_member_gender == '2' ? ' active' : ''; ?>" for="rdo-member-gender-2" data-for="rdo-member-gender">女</label>
						</td>
					</tr>
					<tr>
						<th>出生年</th>
						<td>
							<select name="member_birth_year" class="sel-member-birth">
								<option value="">保密</option>
								<?php for($i = intval(date('Y', time())); $i > 1929; $i--): ?>
								<option value="<?php echo $i; ?>"<?php echo $input_member_birth_year == $i ? ' selected' : ''; ?>><?php echo $i; ?></option>
								<?php endfor; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th>联系电话</th>
						<td><input type="text" name="member_tel" placeholder="请输入联系电话" value="<?php echo $input_member_tel; ?>"></td>
					</tr>
					<tr>
						<th>微信号</th>
						<td><input type="text" name="member_wechat" placeholder="请输入微信号" value="<?php echo $input_member_wechat; ?>"></td>
					</tr>
					<tr>
						<th>QQ号</th>
						<td><input type="text" name="member_qq" placeholder="请输入QQ号" value="<?php echo $input_member_qq; ?>"></td>
					</tr>
				</table>
				<input type="hidden" name="page" value="member_access" />
			</form>
			<p class="p-warning"><?php echo $error_message_access; ?></p>
			<ul class="ul-button-group">
				<li class="btn-active btn-access"><div class="shine"></div>注册</li>
			</ul>
		</div>
	</div>
	<?php echo $footer; ?>
</body>
</html>
