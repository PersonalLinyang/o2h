<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>株式会社O2H</title>
	<meta name="description" content="">
	<meta name="keywords" content="">
	<link rel="canonical" href="https://www.ltdo2h.com/">
	<?php echo Asset::css('pc/common/common.css'); ?>
	<?php echo Asset::css('pc/common/header.css'); ?>
	<?php echo Asset::css('pc/index.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('pc/common/google-analytics.js'); ?>
	<?php echo Asset::js('pc/common/common.js'); ?>
</head>
<body>
	<?php echo $header; ?>
	<div class="mainv-area">
		<div class="mainv-text">
			<img src="/assets/img/pc/index/mainv-text.png" />
			<p>我们将是一个让您感受幸福的机会</p>
		</div>
		<img class="mainv-image" src="/assets/img/pc/index/mainv-image.png" />
	</div>
	<div class="form-slide <?php echo $season; ?>">
		<div class="form-title">
			<div class="form-title-burst">
				<p>最短不到<br><span>1</span>分钟<br>轻松搞定</p>
			</div>
			<p class="form-title-text">来一场说走就走的旅行吧!!</p>
		</div>
		<div class="form-inner">
			<div class="form-head">
				<p class="form-head-text">为了提供最优质的服务，请允许我们更多了解您</p>
				<ul class="ul-form-head-step" >
					<li class="active" id="form-head-step-1"><p>Step 1</p>关于旅行</li>
					<li id="form-head-step-2"><p>Step 2</p>关于行程</li>
					<li id="form-head-step-3"><p>Step 3</p>关于餐宿</li>
					<li id="form-head-step-4"><p>Step 4</p>关于您</li>
				</ul>
			</div>
			<div class="form-body">
				<form>
					<div class="form-body-slide">
						<div class="form-body-step" id="form-body-step-1" data-step="1">
							<div class="form-body-row">
								<div class="form-body-content form-body-half">
									<p class="question">请问您是第一次使用敝公司服务吗？</p>
									<div class="div-for-radio">
										<input type="radio" name="first_flag" value="1" id="rdo-first-flag-1" />
										<label class="lbl-for-radio lbl-for-radio-slide active" for="rdo-first-flag-1" data-for="rdo-first-flag">是</label>
										<input type="radio" name="first_flag" value="0" id="rdo-first-flag-0" />
										<label class="lbl-for-radio lbl-for-radio-slide" for="rdo-first-flag-0" data-for="rdo-first-flag">否</label>
									</div>
									<p class="question">请问您要享受一次为期几天的旅行？</p>
									<div class="div-for-number">
										<p class="minus-for-number btn-for-number" data-for="num-travel-days"></p>
										<input class="num-number" type="number" name="travel_days" id="num-travel-days" value="0" />
										<p class="plus-for-number btn-for-number" data-for="num-travel-days"></p>天
									</div>
									<p class="question">请问您计划何时动身出发？</p>
									<select name="start_at_year">
										<option>-未定-</option>
										<?php for($i = intval(date('Y', time())); $i < intval(date('Y', time())) + 2; $i++): ?>
											<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
										<?php endfor; ?>
									</select>年
									<select name="start_at_month">
										<option>-未定-</option>
										<?php for($i = 1; $i < 13; $i++): ?>
											<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
										<?php endfor; ?>
									</select>月
									<select name="start_at_day">
										<option>-未定-</option>
										<?php for($i = 1; $i < 32; $i++): ?>
											<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
										<?php endfor; ?>
									</select>日
									<p class="question">如果您已经预定了机票，请告诉我们航班号</p>
									<input type="text" name="airplane_num" />
								</div>
								<div class="form-body-content form-body-half">
									<p class="question">请问本次参加旅行的成员有？</p>
									<table>
										<tr>
											<th class="th-age"></th>
											<th class="th-men">男性</th>
											<th class="th-women">女性</th>
										</tr>
										<tr>
											<th class="th-age">15岁以内</th>
											<td>
												<p class="minus-for-number btn-for-number" data-for="num-people-0-0"></p>
												<input class="num-number" type="number" name="people_0_0" id="num-people-0-0" value="0" />
												<p class="plus-for-number btn-for-number" data-for="num-people-0-0"></p>
											</td>
											<td>
												<p class="minus-for-number btn-for-number" data-for="num-people-1-0"></p>
												<input class="num-number" type="number" name="people_1_0" id="num-people-1-0" value="0" />
												<p class="plus-for-number btn-for-number" data-for="num-people-1-0"></p>
											</td>
										</tr>
										<tr>
											<th class="th-age">15~30岁</th>
											<td>
												<p class="minus-for-number btn-for-number" data-for="num-people-0-1"></p>
												<input class="num-number" type="number" name="people_0_1" id="num-people-0-1" value="0" />
												<p class="plus-for-number btn-for-number" data-for="num-people-0-1"></p>
											</td>
											<td>
												<p class="minus-for-number btn-for-number" data-for="num-people-1-1"></p>
												<input class="num-number" type="number" name="people_1_1" id="num-people-1-1" value="0" />
												<p class="plus-for-number btn-for-number" data-for="num-people-1-1"></p>
											</td>
										</tr>
										<tr>
											<th class="th-age">30~45岁</th>
											<td>
												<p class="minus-for-number btn-for-number" data-for="num-people-0-2"></p>
												<input class="num-number" type="number" name="people_0_2" id="num-people-0-2" value="0" />
												<p class="plus-for-number btn-for-number" data-for="num-people-0-2"></p>
											</td>
											<td>
												<p class="minus-for-number btn-for-number" data-for="num-people-1-2"></p>
												<input class="num-number" type="number" name="people_1_2" id="num-people-1-2" value="0" />
												<p class="plus-for-number btn-for-number" data-for="num-people-1-2"></p>
											</td>
										</tr>
										<tr>
											<th class="th-age">45~60岁</th>
											<td>
												<p class="minus-for-number btn-for-number" data-for="num-people-0-3"></p>
												<input class="num-number" type="number" name="people_0_3" id="num-people-0-3" value="0" />
												<p class="plus-for-number btn-for-number" data-for="num-people-0-3"></p>
											</td>
											<td>
												<p class="minus-for-number btn-for-number" data-for="num-people-1-3"></p>
												<input class="num-number" type="number" name="people_1_3" id="num-people-1-3" value="0" />
												<p class="plus-for-number btn-for-number" data-for="num-people-1-3"></p>
											</td>
										</tr>
										<tr>
											<th class="th-age">60岁以上</th>
											<td>
												<p class="minus-for-number btn-for-number" data-for="num-people-0-4"></p>
												<input class="num-number" type="number" name="people_0_4" id="num-people-0-4" value="0" />
												<p class="plus-for-number btn-for-number" data-for="num-people-0-4"></p>
											</td>
											<td>
												<p class="minus-for-number btn-for-number" data-for="num-people-1-4"></p>
												<input class="num-number" type="number" name="people_1_4" id="num-people-1-4" value="0" />
												<p class="plus-for-number btn-for-number" data-for="num-people-1-4"></p>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<ul class="ul-form-body-button-group">
								<li class="btn-next active"><div class="shine"></div>下一步</li>
							</ul>
						</div>
						<div class="form-body-step" id="form-body-step-2" data-step="2">
							<div class="form-body-content">
								<p class="question">请问您对于本次旅行的行程作何打算？</p>
								<div class="div-for-tab">
									<input type="radio" name="route_flag" value="1" id="tab-route-flag-1" />
									<label class="lbl-for-tab active" for="tab-route-flag-1" data-index="tab-route-flag-1" data-for="tab-route-flag">选择我们为您准备的旅行线路</label>
									<input type="radio" name="route_flag" value="0" id="tab-route-flag-0" />
									<label class="lbl-for-tab" for="tab-first-route-0" data-index="tab-route-flag-0" data-for="tab-route-flag">自由组合想要游览的景点</label>
								</div>
								<div class="div-content-tab active" data-index="tab-route-flag-1" data-for="tab-route-flag"></div>
								<div class="div-content-tab" data-index="tab-route-flag-0" data-for="tab-route-flag"></div>
							</div>
							<ul class="ul-form-body-button-group">
								<li class="btn-return"><div class="shine"></div>返回</li>
								<li class="btn-next active"><div class="shine"></div>下一步</li>
							</ul>
						</div>
						<div class="form-body-step" id="form-body-step-3" data-step="3">
							<div class="form-body-content">
								<p class="question">请问在饮食方面有什么忌讳(食材、口味、烹调方式等)吗？</p>
								<input type="text" name="dinner_demand" />
								<p class="question">请问需要帮您预定酒店吗？</p>
								<div class="div-for-radio">
									<input type="radio" name="hotel_reserve_flag" value="1" id="rdo-hotel-reserve-flag-1" />
									<label class="lbl-for-radio lbl-for-radio-slide" for="rdo-hotel-reserve-flag-1" data-for="rdo-hotel-reserve-flag">是</label>
									<input type="radio" name="hotel_reserve_flag" value="0" id="rdo-hotel-reserve-flag-0" />
									<label class="lbl-for-radio lbl-for-radio-slide active" for="rdo-hotel-reserve-flag-0" data-for="rdo-hotel-reserve-flag">否</label>
								</div>
								<div id="div-hotel-reserve-area">
									<table>
										<tr>
											<th class="th-hotel-type">酒店类型</th>
											<th class="th-room-type">房型</th>
											<th class="th-people-num">人数</th>
											<th class="th-room-num">间数</th>
											<th class="th-day-num">天数</th>
											<th class="th-hotel-comment">备注</th>
										</tr>
									</table>
									<div class="div-scroll">
										<table>
											<tr>
												<td class="th-hotel-type">
													<select>
														<option>温泉酒店</option>
													</select>
												</td>
												<td class="th-room-type">
													<select>
														<option>三人间</option>
													</select>
												</td>
												<td class="th-people-num">
													<p class="minus-for-number btn-for-number" data-for="num-people-0-2"></p>
													<input class="num-number" type="number" name="people_0_2" id="num-people-0-2" value="0" />
													<p class="plus-for-number btn-for-number" data-for="num-people-0-2"></p>
												</td>
												<td class="th-room-num">
													<p class="minus-for-number btn-for-number" data-for="num-people-0-2"></p>
													<input class="num-number" type="number" name="people_0_2" id="num-people-0-2" value="0" />
													<p class="plus-for-number btn-for-number" data-for="num-people-0-2"></p>
												</td>
												<td class="th-day-num">
													<p class="minus-for-number btn-for-number" data-for="num-people-0-2"></p>
													<input class="num-number" type="number" name="people_0_2" id="num-people-0-2" value="0" />
													<p class="plus-for-number btn-for-number" data-for="num-people-0-2"></p>
												</td>
												<td class="th-hotel-comment">
													<input type="text">
												</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
							<ul class="ul-form-body-button-group">
								<li class="btn-return"><div class="shine"></div>返回</li>
								<li class="btn-next active"><div class="shine"></div>下一步</li>
							</ul>
						</div>
						<div class="form-body-step" id="form-body-step-4" data-step="4">
							<div class="form-body-row">
								<div class="form-body-content form-body-half">
									<p class="question">请让我们认识您</p>
									<table>
										<tr>
											<th>姓名</th>
											<td><input type="text" name="customer_name" /></td>
										</tr>
										<tr>
											<th>性别</th>
											<td>
												<input type="radio" name="customer_gender" value="1" id="rdo-customer-gender-1" />
												<label class="lbl-for-radio lbl-for-radio-slide active" for="rdo-customer-gender-1" data-for="rdo-customer-gender">男</label>
												<input type="radio" name="customer_gender" value="0" id="customer-gender-0" />
												<label class="lbl-for-radio lbl-for-radio-slide" for="rdo-customer-gender-0" data-for="rdo-customer-gender">女</label>
											</td>
										</tr>
										<tr>
											<th>年龄</th>
											<td>
												<select name="customer_age">
													<option>保密</option>
													<option value="0">15岁以内</option>
													<option value="1">15~30岁</option>
													<option value="2">30~45岁</option>
													<option value="3">45~60岁</option>
													<option value="4">60岁以上</option>
												</select>
											</td>
										</tr>
										<tr>
											<th>旅行目标</th>
											<td>
												<select>
													<option>保密</option>
												</select>
											</td>
										</tr>
									</table>
								</div>
								<div class="form-body-content form-body-half">
									<p class="question">为了方便与您取得联系，请至少为我们留下一种您的联系方式</p>
									<table>
										<tr>
											<th>手机号码</th>
											<td><input type="text" name="customer_tel" /></td>
										</tr>
										<tr>
											<th>电子邮箱</th>
											<td><input type="text" name="customer_mail" /></td>
										</tr>
										<tr>
											<th>微信号</th>
											<td><input type="text" name="customer_wechat" /></td>
										</tr>
										<tr>
											<th>QQ号</th>
											<td><input type="text" name="custoemr_qq" /></td>
										</tr>
									</table>
									<p class="question">除了之前记入的内容外，您还希望我们在服务中注意些什么</p>
									<input type="text" name="comment" />
								</div>
							</div>
							<ul class="ul-form-body-button-group">
								<li class="btn-return"><div class="shine"></div>返回</li>
								<li class="btn-next active"><div class="shine"></div>发送</li>
							</ul>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="mission-area">
		<div class="div-image"><img src="/assets/img/pc/index/bg-mission.jpg" alt="Mission" /></div>
		<div class="div-content">
			<p class="p-item">Mission</p>
			<p class="p-sub-title">We create Opportunities to Happiness</p>
			<p class="p-title">我们将提供感受幸福的机会</p>
			<p class="p-content">
				人们对于幸福的定义总是由于周遭的环境差异而有所不同。<br>
				在人生中感受到“啊，真是幸福啊”的瞬间多如繁星。<br>
				您现在所追求的“幸福”又是什么呢？<br>
				“二老身体健康，不必太多担心”<br>
				“在能看到漫山樱花的富士山的地方与好友开怀畅饮”<br>
				“带上公司的同事拍下可以看成绝境的纪念照片”<br>
				等等……
			</p>
			<p class="p-content">
				我们作为驻留日本10年的中国人，将会担负起连接中日的桥梁，<br>
				注重人与人的联系，在医疗，旅游，乃至商业的方方面面，<br>
				为您提供一个感受幸福的机会。
			</p>
		</div>
	</div>
	<div class="project-area">
		<div class="div-image"><img src="/assets/img/pc/index/bg-project.jpg" alt="Mission" /></div>
		<div class="div-content">
			<p class="p-item">Project</p>
			<p class="p-title">我们将提供感受幸福的机会</p>
			<p class="p-content center">
				O2H将通过医疗与旅游，发挥出桥梁的效果，<br>
				使人与人，组织与组织，国与国之间建立起联系。
			</p>
			<p class="p-content center">
				我们将把让全员HAPPY作为我们的任务，<br>
				通过彼此传达使各位切实感受到各国的优点，<br>
				加深彼此之间的理解与交流，<br>
				为改变这个世界尽我们的绵薄之力。
			</p>
			<p class="p-title"><span>Only for you</span> 医疗和旅行定制行程</p>
			<p class="p-content">
				随着人对于“健康”的认识的提高，治疗与健康检查也与旅游紧密联系在了一起。与传统的旅游公司不同，本公司提供的将不是面向大众的旅行套餐或是多人团体旅游，而是认真倾听每一位顾客的需求，为您奉上独一无二的感受幸福计划。
			</p>
			<p class="p-title">面向中国旅客的商务旅游咨询</p>
			<p class="p-content">
				相信随着2020年东京奥运会的临近，全世界的游客集结东京的机会，也一定能够产生全新的商机。<br>
				我们也将作为各位顾客的伙伴与搭档，伴随各位一同向未知的世界不断挑战。
			</p>
		</div>
	</div>
	<?php echo $footer; ?>
</body>
</html>
