<!--banner-->
<div class="dz-bnr-inr" style="background-image:url( <% if SiteConfig.BannerImage %> $SiteConfig.BannerImage.URL <% else %>$themedResourceURL('theme/images/background/bg-shape.jpg') <% end_if %>);">
    <div class="container">
        <div class="dz-bnr-inr-entry">
            <h1>Dashboard</h1>
            <nav aria-label="breadcrumb" class="breadcrumb-row">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/"> Home</a></li>
                    <li class="breadcrumb-item">Cart</li>
                </ul>
            </nav>
        </div>
    </div>
</div>


<%-- Dashboard here --%>
<section class="content-inner-1 z-index-unset">
	<div class="container">
		<div class="row g-4">

			<div class="<% if $CurrentMember.IsAdmin %>col-xl-3 col-lg-3 col-md-6 col-sm-6 <% else %> col-xl-6<% end_if %> wow fadeInUp" data-wow-delay="0.1s">
				<a href="/" class="dashboard-link">
					<div class="dz-card style-2 dashboard-card h-100">
						<div class="dz-media dashboard-icon-wrap">
							<i class="flaticon-online-shop"></i>
						</div>
						<div class="dz-info">
							<div class="dz-meta">
								<ul>
									<li class="post-date">Products</li>
									<li>View</li>
								</ul>
							</div>
							<h4 class="dz-title mb-0">View Products</h4>
						</div>
					</div>
				</a>
			</div>

			<div class="<% if $CurrentMember.IsAdmin %>col-xl-3 col-lg-3 col-md-6 col-sm-6 <% else %> col-xl-6<% end_if %> wow fadeInUp" data-wow-delay="0.2s">
				<a href="$OrderHistoryPageLink" class="dashboard-link">
					<div class="dz-card style-2 dashboard-card h-100">
						<div class="dz-media dashboard-icon-wrap">
							<i class="flaticon-list" style="font-size: 60px;!important"></i>
						</div>
						<div class="dz-info">
							<div class="dz-meta">
								<ul>
									<li class="post-date">Orders</li>
									<li>History</li>
								</ul>
							</div>
							<h4 class="dz-title mb-0">View Order History</h4>
						</div>
					</div>
				</a>
			</div>

			<% if $CurrentMember && $CurrentMember.IsAdmin %>

				<div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
					<a href="$ManageUsersPageLink" class="dashboard-link">
						<div class="dz-card style-2 dashboard-card h-100">
							<div class="dz-media dashboard-icon-wrap">
								<i class="flaticon-user"></i>
							</div>
							<div class="dz-info">
								<div class="dz-meta">
									<ul>
										<li class="post-date">Users</li>
										<li>Manage</li>
									</ul>
								</div>
								<h4 class="dz-title mb-0">Manage Users</h4>
							</div>
						</div>
					</a>
				</div>

				<div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 wow fadeInUp" data-wow-delay="0.4s">
					<a href="$PendingOrdersPageLink" class="dashboard-link">
						<div class="dz-card style-2 dashboard-card h-100 dashboard-has-badge">

							<span class="dashboard-badge">$PendingOrdersCountForMember</span>

							<div class="dz-media dashboard-icon-wrap">
								<i class="flaticon-calendar"></i>
							</div>

							<div class="dz-info">
								<div class="dz-meta">
									<ul>
										<li class="post-date">Approvals</li>
										<li>Pending</li>
									</ul>
								</div>
								<h4 class="dz-title mb-0">View Pending Orders</h4>
							</div>

						</div>
					</a>
				</div>

			<% end_if %>

			<div class="<% if $CurrentMember.IsAdmin %>col-xl-3 col-lg-3 col-md-6 col-sm-6 <% else %> col-xl-6<% end_if %> wow fadeInUp" data-wow-delay="0.1s">
				<a href="$AccountPageLink" class="dashboard-link">
					<div class="dz-card style-2 dashboard-card h-100">
						<div class="dz-media dashboard-icon-wrap">
							<i class="flaticon-setting"></i>
						</div>
						<div class="dz-info">
							<div class="dz-meta">
								<ul>
									<li class="post-date">Account</li>
									<li>Manage</li>
								</ul>
							</div>
							<h4 class="dz-title mb-0">Edit Account</h4>
						</div>
					</div>
				</a>
			</div>			

		</div>
	</div>
</section>


<% include FooterInfoBox %>
