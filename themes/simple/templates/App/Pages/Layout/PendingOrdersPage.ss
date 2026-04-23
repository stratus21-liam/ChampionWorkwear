<!--banner-->
<div class="dz-bnr-inr" style="background-image:url( <% if SiteConfig.BannerImage %> $SiteConfig.BannerImage.URL <% else %>$themedResourceURL('theme/images/background/bg-shape.jpg') <% end_if %>);">
    <div class="container">
        <div class="dz-bnr-inr-entry">
            <h1>Pending Orders</h1>
            <nav aria-label="breadcrumb" class="breadcrumb-row">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="$DashboardPageLink"> Dashboard</a></li>
                    <li class="breadcrumb-item">Pending Orders</li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<div class="content-inner-1">
    <div class="container">
        <div class="row shop-checkout">
            <div class="col-xl-12">
                <h4 class="title m-b15">Order Approvals</h4>

                <% if $Message %>
                    <div class="alert alert-info">$Message</div>
                <% end_if %>

                <ul class="nav nav-tabs m-b25" id="order-approval-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-orders-tab-pane" type="button" role="tab" aria-controls="pending-orders-tab-pane" aria-selected="true">
                            Pending Orders
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected-orders-tab-pane" type="button" role="tab" aria-controls="rejected-orders-tab-pane" aria-selected="false">
                            Rejected Orders
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="order-approval-tabs-content">
                    <div class="tab-pane fade show active" id="pending-orders-tab-pane" role="tabpanel" aria-labelledby="pending-tab" tabindex="0">
                        <div class="table-responsive">
                            <table class="table check-tbl">
                                <thead>
                                    <tr>
                                        <th>Order Number</th>
                                        <th>User</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <% if $PendingOrders.Exists %>
                                        <% loop $PendingOrders %>
                                            <tr>
                                                <td>
                                                    <a href="$PendingOrderLink">$OrderNumber</a>
                                                </td>
                                                <td>
                                                    <% if $Customer %>
                                                        <% if $Customer.Name %>
                                                            $Customer.Name
                                                        <% else %>
                                                            $Customer.Email
                                                        <% end_if %>
                                                    <% end_if %>
                                                </td>
                                                <td>
                                                    <% if $SubmittedAt %>
                                                        $SubmittedAt.Format(d/m/Y)
                                                    <% else %>
                                                        $Created.Format(d/m/Y)
                                                    <% end_if %>
                                                </td>
                                                <td>$Total.Nice</td>
                                                <td>$StatusNice</td>
                                                <td><a href="$PendingOrderLink">View</a></td>
                                            </tr>
                                        <% end_loop %>
                                    <% else %>
                                        <tr>
                                            <td colspan="6">There are no pending orders to approve.</td>
                                        </tr>
                                    <% end_if %>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="rejected-orders-tab-pane" role="tabpanel" aria-labelledby="rejected-tab" tabindex="0">
                        <div class="table-responsive">
                            <table class="table check-tbl">
                                <thead>
                                    <tr>
                                        <th>Order Number</th>
                                        <th>User</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Reason</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <% if $RejectedOrders.Exists %>
                                        <% loop $RejectedOrders %>
                                            <tr>
                                                <td>
                                                    <a href="$PendingOrderLink">$OrderNumber</a>
                                                </td>
                                                <td>
                                                    <% if $Customer %>
                                                        <% if $Customer.Name %>
                                                            $Customer.Name
                                                        <% else %>
                                                            $Customer.Email
                                                        <% end_if %>
                                                    <% end_if %>
                                                </td>
                                                <td>
                                                    <% if $RejectedAt %>
                                                        $RejectedAt.Format(d/m/Y)
                                                    <% else_if $SubmittedAt %>
                                                        $SubmittedAt.Format(d/m/Y)
                                                    <% else %>
                                                        $Created.Format(d/m/Y)
                                                    <% end_if %>
                                                </td>
                                                <td>$Total.Nice</td>
                                                <td>$StatusNice</td>
                                                <td>
                                                    <% if $RejectionReason %>
                                                        $RejectionReason
                                                    <% else %>
                                                        -
                                                    <% end_if %>
                                                </td>
                                                <td><a href="$PendingOrderLink">View</a></td>
                                            </tr>
                                        <% end_loop %>
                                    <% else %>
                                        <tr>
                                            <td colspan="7">There are no rejected orders.</td>
                                        </tr>
                                    <% end_if %>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<% include FooterInfoBox %>