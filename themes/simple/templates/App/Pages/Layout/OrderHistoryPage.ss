<!--banner-->
<div class="dz-bnr-inr" style="background-image:url( <% if SiteConfig.BannerImage %> $SiteConfig.BannerImage.URL <% else %>$themedResourceURL('theme/images/background/bg-shape.jpg') <% end_if %>);">
    <div class="container">
        <div class="dz-bnr-inr-entry">
            <h1>Order History</h1>
            <nav aria-label="breadcrumb" class="breadcrumb-row">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="$DashboardPageLink"> Dashboard</a></li>
                    <li class="breadcrumb-item">Order History</li>
                </ul>
            </nav>
        </div>
    </div>
</div>


<div class="content-inner-1">
    <div class="container">
        <div class="row shop-checkout">
            <div class="col-xl-12">
                <h4 class="title m-b15">Order History</h4>

                <div class="table-responsive">
                    <table class="table check-tbl">
                        <thead>
                            <tr>
                                <th>Order Number</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <% if $MyOrders.Exists %>
                                <% loop $MyOrders %>
                                    <tr>
                                        <td>
                                            <a href="$OrderHistoryLink">$OrderNumber</a>
                                        </td>
                                        <td>
                                            <% if $SubmittedAt %>
                                                $SubmittedAt.Format(d/m/Y)
                                            <% else %>
                                                $Created.Format(d/m/Y)
                                            <% end_if %>
                                        </td>
                                        <td>$StatusNice</td>
                                        <td>$Total.Nice</td>
                                        <td><a href="$OrderHistoryLink">View</a></td>
                                    </tr>
                                <% end_loop %>
                            <% else %>
                                <tr>
                                    <td colspan="4">You have not placed any orders yet.</td>
                                </tr>
                            <% end_if %>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<% include FooterInfoBox %>