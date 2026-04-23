<!-- Header -->
<header class="<% if ControllerClass == "App\Pages\HomePage" %>site-header mo-left header header-transparent <% else %> site-header mo-left header border-bottom <% end_if %>">			
	<!-- Main Header -->
	<div class="sticky-header main-bar-wraper navbar-expand-lg">
		<div class="main-bar clearfix">
			<div class="container-fluid clearfix">
				<!-- Website Logo -->
				<div class="logo-header logo-dark me-md-5">
					<a href="/"><img src="$themedResourceURL('champion/ChampionWorkwearHorizontal.png')" alt="logo"></a>
				</div>
				
				<!-- Nav Toggle Button -->
				<button class="navbar-toggler collapsed navicon justify-content-end" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
					<span></span>
					<span></span>
					<span></span>
				</button>
				
				<!-- Navigation top right -->
				<div class="extra-nav">
					<div class="extra-cell">						
						<ul class="header-right">
	        				<% if CurrentMember %>
								<%-- CMS access for champion --%>
								<% if $CurrentMember && $CurrentMember.inGroup('administrators') %>
									<li class="nav-item login-link" style="background: #FEEC00;color: #24262B;">
										<a class="nav-link" href="/admin/pages">
											CHAMPION CMS ACCESS
										</a>
									</li>
								<% end_if %>	

								<%-- Logout --%>
								<li class="nav-item login-link">
									<a class="nav-link" href="Security/logout">
										LOGOUT
									</a>
								</li>
								
								<%-- If member is customer account admin show dashboard --%>
								<% if CurrentMember.IsAdmin %>
									<li class="nav-item login-link">
										<a class="nav-link" href="$DashboardPageLink">
											DASHBOARD
										</a>
									</li>	
								<% else %>
									<li class="nav-item login-link">
										<a class="nav-link" href="$DashboardPageLink">
											MY ACCOUNT
										</a>
									</li>	
								<% end_if %>	

							<% else %>
								<li class="nav-item login-link">
									<a class="nav-link" href="/Security/Login">
										LOGIN
									</a>
								</li>
							<% end_if %>			

								
							<%-- Search maybe in future --%>
							<%-- <li class="nav-item search-link">
								<a class="nav-link"  href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTop" aria-controls="offcanvasTop">
									<svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
										<circle cx="10.0535" cy="10.55" r="7.49047" stroke="var(--white)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M15.2632 16.1487L18.1999 19.0778" stroke="var(--white)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</a>
							</li> --%>
							<li class="nav-item cart-link">
								<a href="javascript:void(0);" class="nav-link cart-btn"  data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
									<svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path fill-rule="evenodd" clip-rule="evenodd" d="M1.08374 2.61947C1.08374 2.27429 1.36356 1.99447 1.70874 1.99447H3.29314C3.91727 1.99447 4.4722 2.39163 4.67352 2.98239L5.06379 4.1276H15.4584C17.6446 4.1276 19.4168 5.89981 19.4168 8.08593V11.5379C19.4168 13.7241 17.6446 15.4963 15.4584 15.4963H9.22182C7.30561 15.4963 5.66457 14.1237 5.32583 12.2377L4.00967 4.90953L3.49034 3.3856C3.46158 3.30121 3.3823 3.24447 3.29314 3.24447H1.70874C1.36356 3.24447 1.08374 2.96465 1.08374 2.61947ZM5.36374 5.3776L6.55614 12.0167C6.78791 13.3072 7.91073 14.2463 9.22182 14.2463H15.4584C16.9542 14.2463 18.1668 13.0337 18.1668 11.5379V8.08593C18.1668 6.59016 16.9542 5.3776 15.4584 5.3776H5.36374Z" fill="var(--white)"/>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M8.16479 17.8278C8.16479 17.1374 8.72444 16.5778 9.4148 16.5778H9.42313C10.1135 16.5778 10.6731 17.1374 10.6731 17.8278C10.6731 18.5182 10.1135 19.0778 9.42313 19.0778H9.4148C8.72444 19.0778 8.16479 18.5182 8.16479 17.8278Z" fill="var(--white)"/>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M14.8315 17.8278C14.8315 17.1374 15.3912 16.5778 16.0815 16.5778H16.0899C16.7802 16.5778 17.3399 17.1374 17.3399 17.8278C17.3399 18.5182 16.7802 19.0778 16.0899 19.0778H16.0815C15.3912 19.0778 14.8315 18.5182 14.8315 17.8278Z" fill="var(--white)"/>
									</svg>
									<span class="badge badge-circle">$CartCount</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Main Header End -->
	
	<!-- Sidebar cart -->
	<div class="offcanvas dz-offcanvas offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight">
		<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
			&times;
		</button>
		<div class="offcanvas-body">
			<div class="product-description">
				<div class="dz-tabs">
					<%-- Tab Title --%>
					<ul class="nav nav-tabs center" id="myTab" role="tablist">
						<li class="nav-item" role="presentation">
							<button class="nav-link active" id="shopping-cart" data-bs-toggle="tab" data-bs-target="#shopping-cart-pane" type="button" role="tab" aria-controls="shopping-cart-pane" aria-selected="true">
								Shopping Cart
								<span class="badge badge-light">$CartCount</span>
							</button>
						</li>
					</ul>
					<%-- Tab Content --%>
					<div class="tab-content pt-4" id="dz-shopcart-sidebar">
						<div class="tab-pane fade show active" id="shopping-cart-pane" role="tabpanel" aria-labelledby="shopping-cart" tabindex="0">
							<div class="shop-sidebar-cart">

								<% if $CartItems %>
									<ul class="sidebar-cart-list">
										<% loop $CartItems %>
											<li>
												<div class="cart-widget">
													<div class="dz-media me-3">
														<% if $ImageURL %>
															<img src="$ImageURL" alt="$Title.XML">
														<% else %>
															<img src="$ThemeDir/images/shop/shop-cart/pic1.jpg" alt="$Title.XML">
														<% end_if %>
													</div>

													<div class="cart-content">
														<h6 class="title">
															<% if $ProductLink %>
																<a href="$ProductLink">$Title</a>
															<% else %>
																<a href="javascript:void(0);">$Title</a>
															<% end_if %>
														</h6>

														<% if $Attributes %>
															<div class="cart-meta mb-1">
																<% loop $Attributes %>
																	<small class="d-block">$Label.XML: $Value.XML</small>
																<% end_loop %>
															</div>
														<% end_if %>

														<div class="d-flex align-items-center">
															<form method="post" action="$Top.Link(updateCartItem)/$Key">
																<input type="hidden" name="SecurityID" value="$Top.SecurityTokenValue.XML">

																<div class="btn-quantity light quantity-sm me-3">
																	<input 
																		type="text" 
																		value="$Qty" 
																		name="Quantity"
																		class="cart-qty-input"
																	>
																</div>
															</form>

															<h6 class="dz-price text-primary mb-0">$LineTotal.Nice</h6>
														</div>
													</div>

													<a href="home/removeCartItem/$Key" class="dz-close">
														<i class="ti-close"></i>
													</a>
												</div>
											</li>
										<% end_loop %>
									</ul>

									<div class="cart-total">
										<h5 class="mb-0">Subtotal:</h5>
										<h5 class="mb-0">$CartTotal.Nice</h5>
									</div>
								<% else %>
									<ul class="sidebar-cart-list">
										<li>
											<div class="cart-widget">
												<div class="cart-content">
													<h6 class="title mb-0">Your cart is empty</h6>
												</div>
											</div>
										</li>
									</ul>

									<div class="cart-total">
										<h5 class="mb-0">Subtotal:</h5>
										<h5 class="mb-0">£0.00</h5>
									</div>
								<% end_if %>

								<div class="mt-auto">
									<%-- <div class="shipping-time">
										<div class="dz-icon">
											<i class="flaticon flaticon-ship"></i>
										</div>
										<div class="shipping-content">
											<h6 class="title pe-4">Congratulations , you've got free shipping!</h6>
											<div class="progress">
												<div class="progress-bar progress-animated border-0" style="width: 75%;" role="progressbar">
													<span class="sr-only">75% Complete</span>
												</div>
											</div>
										</div>
									</div> --%>
									<a href="$Top.CheckoutPageLink" class="btn btn-light btn-block m-b20">Checkout</a>
									<a href="$Top.CartPageLink" class="btn btn-secondary btn-block">View Cart</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Sidebar cart -->

</header>
<!-- Header End -->

	<script>
	document.addEventListener('DOMContentLoaded', function () {

		document.querySelectorAll('.cart-qty-input').forEach(function (input) {

			const form = input.closest('form');

			if (!form) return;

			// Trigger on manual change
			input.addEventListener('change', function () {
				form.submit();
			});

			// Trigger after +/- buttons update value
			let lastValue = input.value;

			setInterval(function () {
				if (input.value !== lastValue) {
					lastValue = input.value;
					form.submit();
				}
			}, 300);

		});

	});
	</script>

<%-- <% include Navigation %> --%>
