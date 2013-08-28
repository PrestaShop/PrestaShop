<div class="page-head">
	<h2 class="page-title">
		{l s='Dashboard'}
	</h2>
</div>

<div id="dashboard">
<!-- 		<select class="form-control pull-right" name="" id="">
			<option value="">Jan 29, 2012 - Feb 4, 2012</option>
		</select> -->
	<div class="row">
		<div class="col-lg-3">
			<div id="dash_activity" class="panel">
				<div class="panel-heading">
					<i class="icon-time"></i> {l s='Recent Activity'}
					<span class="panel-heading-action">
						<a class="list-tooolbar-btn" href="javascript:void(0);" title="configure">
							<i class="process-icon-configure"></i>
						</a>
						<a class="list-tooolbar-btn" href="javascript:location.reload();" title="refresh">
							<i class="process-icon-refresh"></i>
						</a>
					</span>
				</div>
				<section id="dash_orders">
					<ul class="data_list_large">
						<li>
							<span class="data_label size_l">
								{l s='Orders'}<br/>
								<small class="text-muted">{l s='Within the last seven days'}</small>
							</span>
							<span class="data_value size_xxl">
								365<br/>
								<small class="dash_trend dash_trend_up"><i class="icon-circle-arrow-up"></i> 0.66%</small>
							</span>
						</li>
					</ul>			
				</section>
				<section id="dash_pending">
					<header><i class="icon-time"></i> {l s='Pending'}</header>
					<ul class="data_list">
						<li>
							<span class="data_label">{l s='Pending Orders'}</span>
							<span class="data_value size_l">120</span>
						</li>
						<li>
							<span class="data_label">{l s='Return/Exchanges'}</span>
							<span class="data_value size_l">35</span>
						</li>
						<li>
							<span class="data_label">{l s='Abandonned Carts'}</span>
							<span class="data_value size_l">12</span>
						</li>
						<li>
							<span class="data_label">{l s='Products Out of Stock'}</span>
							<span class="data_value size_l">4</span>
						</li>
					</ul>
				</section>
				<section id="dash_notifications">
					<header><i class="icon-exclamation-sign"></i> {l s='Notification'}</header>
					<ul class="data_list_vertical">
						<li>
							<span class="data_label">{l s='New Messages'}</span>
							<span class="data_value size_l">42</span>
						</li>
						<li>
							<span class="data_label">{l s='Order Inquires'}</span>
							<span class="data_value size_l">13</span>
						</li>
						<li>
							<span class="data_label">{l s='Product Reviews'}</span>
							<span class="data_value size_l">56</span>
						</li>
					</ul>
				</section>
				<section id="dash_customers">
					<header><i class="icon-user"></i> {l s='Customers'}</header>
					<ul class="data_list">
						<li>
							<span class="data_label">{l s='New Customers'}</span>
							<span class="data_value size_md">42</span>
						</li>
						<li>
							<span class="data_label">{l s='Online Visitor'}</span>
							<span class="data_value size_md">200</span>
						</li>
						<li>
							<span class="data_label">
								{l s='Active Shopping Carts'}
								<small class="text-muted"><br/>
									{l s='In the last 30 minutes'}
								</small>
							</span>
							<span class="data_value size_md">36</span>
						</li>
					</ul>
				</section>
				<section id="dash_newsletter">
					<header><i class="icon-envelope"></i> {l s='Newsletter'}</header>
					<ul class="data_list">
						<li>
							<span class="data_label">{l s='New Registrations'}</span>
							<span class="data_value size_md">125</span>
						</li>
						<li>
							<span class="data_label">{l s='Total Subscribers'}</span>
							<span class="data_value size_md">13,500</span>
						</li>
					</ul>		
				</section>
				<section id="dash_traffic">
					<header><i class="icon-globe"></i> {l s='Traffic'}</header>
					<ul class="data_list">
						<li>
							<span class="data_label">{l s='Visits'}</span>
							<span class="data_value size_md">10,000</span>
						</li>
						<li>
							<span class="data_label">{l s='Unique Visitors'}</span>
							<span class="data_value size_md">3,500</span>
						</li>
						<li>
							<span class="data_label">{l s='Traffic Sources'}</span>
							<ul class="data_list_small">
								<li>
									<span class="data_label">{l s='Direct Link'}</span>
									<span class="data_value size_s">120</span>
								</li>
								<li>
									<span class="data_label">google.com</span>
									<span class="data_value size_s">75</span>
								</li>
								<li>
									<span class="data_label">facebook.com</span>
									<span class="data_value size_s">32</span>
								</li>
							</ul>
						</li>
					</ul>		
				</section>
			</div>
		</div>

		<div class="col-lg-7">

			<section id="dash_trends" class="panel">
				<header class="panel-heading">
					<i class="icon-bar-chart"></i> {l s='Score Trends'}
					<span class="panel-heading-action">
						<a class="list-tooolbar-btn" href="javascript:void(0);" title="configure">
							<i class="process-icon-configure"></i>
						</a>
						<a class="list-tooolbar-btn" href="javascript:location.reload();" title="refresh">
							<i class="process-icon-refresh"></i>
						</a>
					</span>
				</header>
				<div id="dash_trends_toolbar" class="row">
					<dl class="col-xs-4 col-lg-2 active">
						<dt>{l s='Sales'}</dt>
						<dd class="size_l">$150,365</dd>
						<dd class="dash_trend dash_trend_up"><i class="icon-circle-arrow-up"></i> 0.66%</dd>
					</dl>
					<dl class="col-xs-4 col-lg-2">
						<dt>{l s='Orders'}</dt>
						<dd class="size_l">20,432</dd>
						<dd class="dash_trend dash_trend_down"><i class="icon-circle-arrow-down"></i> 0.66%</dd>
					</dl>
					<dl class="col-xs-4 col-lg-2">
						<dt>{l s='Cart Value'}</dt>
						<dd class="size_l">$125</dd>
						<dd class="dash_trend dash_trend_up"><i class="icon-circle-arrow-up"></i> 0.66%</dd>
					</dl>
					<dl class="col-xs-4 col-lg-2">
						<dt>{l s='Visits'}</dt>
						<dd class="size_l">1,200</dd>
						<dd class="dash_trend dash_trend_down"><i class="icon-circle-arrow-down"></i> 0.66%</dd>
					</dl>
					<dl class="col-xs-4 col-lg-2">
						<dt>{l s='Converstion Rate'}</dt>
						<dd class="size_l">23%</dd>
						<dd class="dash_trend dash_trend_up"><i class="icon-circle-arrow-up"></i> 0.66%</dd>
					</dl>
					<dl class="col-xs-4 col-lg-2">
						<dt>{l s='Net Profits'}</dt>
						<dd class="size_l">$23,568</dd>
						<dd class="dash_trend dash_trend_up"><i class="icon-circle-arrow-up"></i> 0.66%</dd>
					</dl>
				</div>
			</section>

			<section id="dash_products" class="panel">
				<header class="panel-heading">
					<i class="icon-bar-chart"></i> {l s='Product and Sales'}
					<span class="panel-heading-action">
						<a class="list-tooolbar-btn" href="javascript:void(0);" title="configure">
							<i class="process-icon-configure"></i>
						</a>
						<a class="list-tooolbar-btn" href="javascript:location.reload();" title="refresh">
							<i class="process-icon-refresh"></i>
						</a>
					</span>
				</header>

				<nav>
					<ul class="nav">
						<li><a href="#dash_recent_orders" data-toggle="tab">
							<i class="icon-fire"></i> {l s='Recent Orders'}</a>
						</li>
						<li><a href="#dash_best_sellers" data-toggle="tab">
							<i class="icon-trophy"></i> {l s='Best Sellers'}</a></li>
						<li><a href="#dash_most_viewed" data-toggle="tab">
							<i class="icon-eye-open"></i>  {l s='Most Viewed'}</a></li>
						<li><a href="#dash_top_search" data-toggle="tab">
							<i class="icon-search"></i> {l s='Top Search'}</a></li>
						<li><a href="#dash_best_sales" data-toggle="tab">
							<i class="icon-thumbs-up"></i> {l s='Best Sales'}</a></li>
					</ul>
				</nav>
				
				<span>Last 10 orders: Overall | Pending</span>
				<table class="table">
	                <thead>
	                  <tr>
	                    <th>Customer</th>
	                    <th>Products</th>
	                    <th>Total</th>
	                    <th>Date</th>
	                    <th></th>
	                  </tr>
	                </thead>
	                <tbody>
	                  <tr>
	                    <td class=""><a href="javascript:void(0);">John Smith</a></td>
	                    <td>10</td>
	                    <td>$1200</td>
	                    <td>July 8th, 2013 // 10:42 am</td>
	                    <td>Today</td>
	                  </tr>
	                </tbody>
              	</table>
			</section>
		</div>
		<div class="col-lg-2">
			<section class="dash_news panel">
				<h4><i class="icon-rss"></i> PrestaShop News</h4>
				<article>
				<strong>Important it is to focus marketing efforts.</strong><br/>
				Let’s go over how to use newsletters to increase traffic to your online store and we’ll review the benefits, what to include and how to get subscribers.
				</article>
				<br/>
				<article>
				<strong>Important it is to focus marketing efforts.</strong><br/>
				Let’s go over how to use newsletters to increase traffic to your online store and we’ll review the benefits, what to include and how to get subscribers.
				</article>
				<br/>
				<article>
				<strong>Important it is to focus marketing efforts.</strong><br/>
				Let’s go over how to use newsletters to increase traffic to your online store and we’ll review the benefits, what to include and how to get subscribers.
				</article>
			</section>
			<section class="dash_links panel">
				<h4><i class="icon-link"></i> Useful Links</h4>
					<ul>
						<li><a href="#">link</a></li>
						<li><a href="#">link</a></li>
						<li><a href="#">link</a></li>
						<li><a href="#">link</a></li>
					</ul>
			</section>
		</div>
	</div>
</div>