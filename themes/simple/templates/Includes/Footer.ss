<!-- Footer -->
<footer class="site-footer style-1">
	<!-- Footer Top -->
	<div class="footer-top">
		<div class="container">
			<div class="row">
				<div class="col-xl-6 col-md-6 col-sm-6">
					<div class="widget widget_about me-2">
						<div class="footer-logo logo-white">
							<a href="index.html"><img src="$themedResourceURL('images/ChampionWorkWearLogoblack.png')" alt="/"></a> 
						</div>
						<ul class="widget-address">
							<li>
								<p><span>Address</span> : Unit 3 Northfield Point, Cunliffe Drive, Kettering, Northamptonshire, NN16 9QJ</p>
							</li>
							<li>
								<p><span>E-mail</span> : <a href="mailto:sales@championworkwear.co.uk">sales@championworkwear.co.uk</a></p>
							</li>
							<li>
								<p><span>Phone</span> : <a href="tel:01536 21 22 23">01536 21 22 23</a></p>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-xl-6 col-md-6 col-sm-6">
					<div class="widget widget_services">
						<h5 class="footer-title">Quick Links</h5>
						<ul>
							<li><a href="$DashboardPageLink">Dashboard</a></li>
							<li><a href="/">Product Directory</a></li>
							<li><a href="$OrderHistoryPageLink">Order History</a></li>
							<li><a href="$AccountPageLink">My Account</a></li>
							<% if $CurrentMember && $CurrentMember.IsAdmin %>
								<li><a href="$ManageUsersPageLink">Add / Edit Users</a></li>
								<li><a href="$PendingOrdersPageLink">Pending Orders</a></li>
							<% end_if %>
							<li><a href="Security/Logout">Log Out</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Footer Top End -->
	
	<!-- Footer Bottom -->
	<div class="footer-bottom">
		<div class="container">
			<div class="row fb-inner">
				<div class="col-lg-6 col-md-12 text-start"> 
					<p class="copyright-text">© $Year <a href="https://www.championworkwear.co.uk/">Champion Workwear</a> All Rights Reserved.</p>
				</div>
				<%-- <div class="col-lg-6 col-md-12 text-end"> 
					<div class="d-flex align-items-center justify-content-center justify-content-md-center justify-content-xl-end">
						<span class="me-3">We Accept: </span>
						<img src="images/footer-img.png" alt="/">
					</div>
				</div> --%>
			</div>
		</div>
	</div>
	<!-- Footer Bottom End -->
	
</footer>
<!-- Footer End -->

<button class="scroltop" type="button"><i class="fas fa-arrow-up"></i></button>
