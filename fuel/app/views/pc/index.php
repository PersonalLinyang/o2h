<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>株式会社O2H</title>
	<meta name="description" content="">
	<meta name="keywords" content="">
	<link rel="canonical" href="https://www.ltdo2h.com/">
	<?php echo Asset::css('pc/common.css'); ?>
	<?php echo Asset::css('pc/index.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('pc/common/google-analytics.js'); ?>
	<?php echo Asset::js('pc/common.js'); ?>
	<?php echo Asset::js('pc/index.js'); ?>
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
	<div class="form-slide">
		<div class="form-title">
			<div class="form-title-burst">
				<p>只需不到<br><span>1</span>分钟<br>轻松搞定</p>
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
							<div class="form-body-main">
								<div class="form-body-row">
									<div class="form-body-content form-body-half">
										<p class="question">请问您是首次享受我们的服务吗？</p>
										<div class="div-for-answer">
											<input type="radio" name="first_flag" value="1" id="rdo-first-flag-1" />
											<label class="lbl-for-radio lbl-for-radio-slide active" for="rdo-first-flag-1" data-for="rdo-first-flag">是</label>
											<input type="radio" name="first_flag" value="0" id="rdo-first-flag-0" />
											<label class="lbl-for-radio lbl-for-radio-slide" for="rdo-first-flag-0" data-for="rdo-first-flag">不是</label>
										</div>
										<p class="question">请问您要度过为期几天的旅程？</p>
										<div class="div-for-answer">
											<p class="minus-for-number" data-for="num-travel-days"></p>
											<input class="num-number" type="number" name="travel_days" id="num-travel-days" value="" />
											<p class="plus-for-number" data-for="num-travel-days"></p>天
										</div>
										<p class="question">请问您计划何时动身出发？</p>
										<div class="div-for-answer">
											<select name="start_at_year" class="sel-start-at-year">
												<option value="">未定</option>
												<?php for($i = intval(date('Y', time())); $i < intval(date('Y', time())) + 2; $i++): ?>
												<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
												<?php endfor; ?>
											</select>年
											<select name="start_at_month" class="sel-start-at-month">
												<option value="">未定</option>
												<?php for($i = 1; $i < 13; $i++): ?>
												<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
												<?php endfor; ?>
											</select>月
											<select name="start_at_day" class="sel-start-at-day">
												<option value="">未定</option>
												<?php for($i = 1; $i < 32; $i++): ?>
												<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
												<?php endfor; ?>
											</select>日
										</div>
										<p class="question">如果您已经预定了机票，请告诉我们航班号</p>
										<div class="div-for-answer">
											<input type="text" name="airplane_num" />
										</div>
									</div>
									<div class="form-body-content form-body-half">
										<p class="question">参加本次旅行的成员人数为</p>
										<table class="tb-people-num">
											<tr>
												<td><img src="/assets/img/pc/index/icon-men-num.png" alt="男性" /></td>
												<td><img src="/assets/img/pc/index/icon-women-num.png" alt="女性" /></td>
												<td><img src="/assets/img/pc/index/icon-children-num.png" alt="儿童" /></td>
											</tr>
											<tr>
												<td>先生</td>
												<td>女士</td>
												<td>小朋友</td>
											</tr>
											<tr>
												<td>
													<p class="minus-for-number" data-for="num-men-num"></p>
													<input class="num-number" type="number" name="men_num" id="num-men-num" value="" />
													<p class="plus-for-number" data-for="num-men-num"></p>
												</td>
												<td>
													<p class="minus-for-number" data-for="num-women-num"></p>
													<input class="num-number" type="number" name="women_num" id="num-women-num" value="" />
													<p class="plus-for-number" data-for="num-women-num"></p>
												</td>
												<td>
													<p class="minus-for-number" data-for="num-children-num"></p>
													<input class="num-number" type="number" name="children_num" id="num-children-num" value="" />
													<p class="plus-for-number" data-for="num-children-num"></p>
												</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
							<ul class="ul-button-group">
								<li class="btn-active btn-next active"><div class="shine"></div>下一步</li>
							</ul>
						</div>
						<div class="form-body-step" id="form-body-step-2" data-step="2">
							<div class="form-body-main">
								<div class="form-body-content">
									<p class="question">请问您对于本次旅行的行程作何打算？</p>
									<div class="div-for-tab">
										<input type="radio" name="route_flag" value="1" id="tab-route-flag-1" />
										<label class="lbl-for-tab active" for="tab-route-flag-1" data-index="tab-route-flag-1" data-for="tab-route-flag">选择我们为您准备的旅行线路</label>
										<input type="radio" name="route_flag" value="0" id="tab-route-flag-0" />
										<label class="lbl-for-tab" for="tab-first-route-0" data-index="tab-route-flag-0" data-for="tab-route-flag">自由组合想要游览的景点</label>
									</div>
									<div class="div-content-tab active" data-index="tab-route-flag-1" data-for="tab-route-flag">
										<div class="div-for-sel-route-id">
											<p class="p-for-sel-route-id">请选择旅游路线</p>
											<select name="route_id" class="sel-route-id">
												<option value=""></option>
												<?php foreach($route_list as $route): ?>
												<option value="<?php echo $route['route_id']; ?>"><?php echo $route['route_name']; ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="div-scroll-route-info">
											<p class="route-title"></p>
											<p class="route-price">参考价：<span class="price"></span>元</p>
											<p class="route-description"></p>
											<p class="font-bold">关联景点：</p>
											<p class="spot-list p-spot-list-route"></p>
											<p class="route-detail-link"><a href="" target="_blank">了解更多详细内容</a></p>
										</div>
									</div>
									<div class="div-content-tab" data-index="tab-route-flag-0" data-for="tab-route-flag">
										<div class="div-for-spot-search">
											<input type="text" class="txt-spot-search" id="txt-spot-search" placeholder="请输入想要查找的景点名" />
											<p class="btn-checked-spot">查看已选中的景点</p>
										</div>
										<ul>
											<?php foreach($area_list as $area): ?>
											<li class="li-area-spot-search" data-areaid="<?php echo $area['area_id']; ?>"><?php echo $area['area_name']; ?></li>
											<?php endforeach; ?>
										</ul>
										<div class="div-scroll-spot-list">
											<ul>
												<?php foreach($spot_list as $spot): ?>
												<li class="li-spot" data-spotid="<?php echo $spot['spot_id']; ?>" data-spotname="<?php echo $spot['spot_name']; ?>" data-spotarea="<?php echo $spot['spot_area']; ?>">
													<input type="checkbox" name="customer_spot[]" value="<?php echo $spot['spot_id']; ?>" id="chk-customer-spot-<?php echo $spot['spot_id']; ?>" />
													<label class="lbl-for-check lbl-for-check-spot" for="chk-customer-spot-<?php echo $spot['spot_id']; ?>"><?php echo $spot['spot_name']; ?></label>
												</li>
												<?php endforeach; ?>
											</ul>
										</div>
										<div class="div-checked-spot">
											<p class="spot-list p-spot-list-checked"><span>您尚未选中任何景点</span></p>
										</div>
										<div class="div-for-spot-hope-other">除以上景点外，您还有什么希望游览的景点吗？</div>
										<div class="div-for-answer">
											<textarea name="spot-hope-other" class="txt-spot-hope-other"></textarea>
										</div>
									</div>
								</div>
							</div>
							<ul class="ul-button-group">
								<li class="btn-noactive btn-return"><div class="shine"></div>返回</li>
								<li class="btn-active btn-next active"><div class="shine"></div>下一步</li>
							</ul>
						</div>
						<div class="form-body-step" id="form-body-step-3" data-step="3">
							<div class="form-body-main">
								<div class="form-body-content">
									<p class="question">请问在饮食方面(食材、口味、烹调方式等)有什么忌讳吗？</p>
									<div class="div-for-answer">
										<input type="text" name="dinner_demand" class="txt-dinner-demand" />
									</div>
									<p class="question">请问需要帮您预定酒店吗？</p>
									<div class="div-for-answer">
										<input type="radio" name="hotel_reserve_flag" value="1" id="rdo-hotel-reserve-flag-1" />
										<label class="lbl-for-radio lbl-for-radio-slide" id="btn-hotel-reserve-flag-1" for="rdo-hotel-reserve-flag-1" data-for="rdo-hotel-reserve-flag">需要</label>
										<input type="radio" name="hotel_reserve_flag" value="0" id="rdo-hotel-reserve-flag-0" />
										<label class="lbl-for-radio lbl-for-radio-slide active" id="btn-hotel-reserve-flag-0" for="rdo-hotel-reserve-flag-0" data-for="rdo-hotel-reserve-flag">不需要</label>
									</div>
									<div id="div-hotel-reserve-area" data-row="1">
										<table>
											<tr>
												<th class="td-button"></th>
												<th class="td-button"></th>
												<th class="td-select">酒店类型</th>
												<th class="td-select">房型</th>
												<th class="td-number">人数</th>
												<th class="td-number">间数</th>
												<th class="td-number">天数</th>
												<th class="td-comment">备注</th>
											</tr>
										</table>
										<div class="div-scroll">
											<table>
												<tr>
													<td class="td-button">
														<p class="btn-hotel-reserve btn-plus-hotel-reserve">＋</p>
													</td>
													<td class="td-button"></td>
													<td class="td-select">
														<select name="hotel_type_0" id="sel-hotel-type-0">
															<option></option>
															<?php foreach($hotel_type_list as $hotel_type): ?>
															<option value="<?php echo $hotel_type['hotel_type_id']; ?>"><?php echo $hotel_type['hotel_type_name']; ?></option>
															<?php endforeach; ?>
														</select>
													</td>
													<td class="td-select">
														<select>
															<option></option>
															<?php foreach($room_type_list as $room_type): ?>
															<option value="<?php echo $room_type['room_type_id']; ?>"><?php echo $room_type['room_type_name']; ?></option>
															<?php endforeach; ?>
														</select>
													</td>
													<td class="td-number">
														<p class="minus-for-number btn-for-number" data-for="num-people-0"></p>
														<input class="num-number" type="number" name="people_num_0" id="num-people-0" value="0" />
														<p class="plus-for-number btn-for-number" data-for="num-people-0"></p>
													</td>
													<td class="td-number">
														<p class="minus-for-number btn-for-number" data-for="num-room-0"></p>
														<input class="num-number" type="number" name="room_num_0" id="num-room-0" value="0" />
														<p class="plus-for-number btn-for-number" data-for="num-room-0"></p>
													</td>
													<td class="td-number">
														<p class="minus-for-number btn-for-number" data-for="num-day-0"></p>
														<input class="num-number" type="number" name="day_num_0" id="num-day-0" value="0" />
														<p class="plus-for-number btn-for-number" data-for="num-day-0"></p>
													</td>
													<td class="td-comment">
														<input name="comment_0" type="text">
													</td>
												</tr>
											</table>
										</div>
									</div>
								</div>
							</div>
							<ul class="ul-button-group">
								<li class="btn-noactive btn-return"><div class="shine"></div>返回</li>
								<li class="btn-active btn-next active"><div class="shine"></div>下一步</li>
							</ul>
						</div>
						<div class="form-body-step" id="form-body-step-4" data-step="4">
							<div class="form-body-main">
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
														<option></option>
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
														<option></option>
														<?php foreach($travel_reason_list as $travel_reason): ?>
														<option value="<?php echo $travel_reason['travel_reason_id']; ?>"><?php echo $travel_reason['travel_reason_name']; ?></option>
														<?php endforeach; ?>
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
									</div>
								</div>
								<div class="form-body-content">
									<p class="question">除了之前记入的内容外，您还希望我们在服务中注意些什么</p>
									<textarea name="comment"></textarea>
								</div>
							</div>
							<ul class="ul-button-group">
								<li class="btn-noactive btn-return"><div class="shine"></div>返回</li>
								<li class="btn-active btn-submit active"><div class="shine"></div>发送</li>
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
			<p class="p-title">让全员<span>HAPPY</span>！</p>
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
			<p class="p-sub-title"><span>Only for you</span> 医疗和旅行定制行程</p>
			<p class="p-content">
				随着人对于“健康”的认识的提高，治疗与健康检查也与旅游紧密联系在了一起。与传统的旅游公司不同，本公司提供的将不是面向大众的旅行套餐或是多人团体旅游，而是认真倾听每一位顾客的需求，为您奉上独一无二的感受幸福计划。
			</p>
			<p class="p-sub-title">面向中国旅客的商务旅游咨询</p>
			<p class="p-content">
				相信随着2020年东京奥运会的临近，全世界的游客集结东京的机会，也一定能够产生全新的商机。<br>
				我们也将作为各位顾客的伙伴与搭档，伴随各位一同向未知的世界不断挑战。
			</p>
		</div>
	</div>
	<?php echo $footer; ?>
</body>
</html>
