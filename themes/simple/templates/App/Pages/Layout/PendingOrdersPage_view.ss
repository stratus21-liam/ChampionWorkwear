<!--banner-->
<div class="dz-bnr-inr" style="background-image:url( <% if SiteConfig.BannerImage %> $SiteConfig.BannerImage.URL <% else %>$themedResourceURL('images/background/bg-shape.jpg') <% end_if %>);">
    <div class="container">
        <div class="dz-bnr-inr-entry">
            <h1><% if $Order %>Order: $Order.OrderNumber<% else %>Pending Order<% end_if %></h1>
            <nav aria-label="breadcrumb" class="breadcrumb-row">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="$Top.Link">Pending Orders</a></li>
                    <li class="breadcrumb-item">Order</li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<div class="content-inner-1">
    <div class="container">
        <div class="row shop-checkout">
            <div class="col-xl-8">
                <h4 class="title m-b15">Order Details</h4>

                <% if $Message %>
                    <div class="alert alert-info">$Message</div>
                <% end_if %>

                <% if $Order %>
                    <div class="table-responsive m-b30">
                        <table class="table check-tbl">
                            <tbody>
                                <tr>
                                    <th>Order Number</th>
                                    <td>$Order.OrderNumber</td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <td>
                                        <% if $Order.SubmittedAt %>
                                            $Order.SubmittedAt.Format(d/m/Y)
                                        <% else %>
                                            $Order.Created.Format(d/m/Y)
                                        <% end_if %>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>$Order.StatusNice</td>
                                </tr>
                                <tr>
                                    <th>User</th>
                                    <td>
                                        <% if $Order.Customer %>
                                            <% if $Order.Customer.Name %>
                                                $Order.Customer.Name
                                            <% else %>
                                                $Order.Customer.Email
                                            <% end_if %>
                                        <% end_if %>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><% if $Order.Customer %>$Order.Customer.Email<% end_if %></td>
                                </tr>
                                <% if $Order.CustomerAccount %>
                                    <tr>
                                        <th>Customer Account</th>
                                        <td>$Order.CustomerAccount.Title</td>
                                    </tr>
                                <% end_if %>
                                <tr>
                                    <th>Fulfilment</th>
                                    <td>$Order.FulfilmentMethodNice</td>
                                </tr>
                                <% if $Order.PONumber %>
                                    <tr>
                                        <th>PO Number</th>
                                        <td>$Order.PONumber</td>
                                    </tr>
                                <% end_if %>
                                <% if $Order.OrderNotes %>
                                    <tr>
                                        <th>Order Notes</th>
                                        <td>$Order.OrderNotes</td>
                                    </tr>
                                <% end_if %>
                                <tr>
                                    <th>Total</th>
                                    <td>$Order.Total.Nice</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <% if $Order.IsDelivery %>
                        <h5 class="title m-b15">Delivery Details</h5>
                        <div class="table-responsive m-b30">
                            <table class="table check-tbl">
                                <tbody>
                                    <tr>
                                        <th>Company</th>
                                        <td>$Order.DeliveryCompany</td>
                                    </tr>
                                    <tr>
                                        <th>Contact Name</th>
                                        <td>$Order.DeliveryContactName</td>
                                    </tr>
                                    <tr>
                                        <th>Phone</th>
                                        <td>$Order.DeliveryPhone</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>$Order.DeliveryEmail</td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td>
                                            $Order.DeliveryAddressLine1<br>
                                            <% if $Order.DeliveryAddressLine2 %>$Order.DeliveryAddressLine2<br><% end_if %>
                                            $Order.DeliveryCity<br>
                                            $Order.DeliveryCounty<br>
                                            $Order.DeliveryPostcode
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <% else %>
                        <div class="alert alert-info m-b30">
                            This order is for collection from Champion Workwear.
                        </div>
                    <% end_if %>

                    <% if $Order.Status = 'PendingApproval' %>
                        <div class="m-b30">
                            <form method="post" action="$Order.PendingOrderLink(approve)">
                                <input type="hidden" name="SecurityID" value="$SecurityTokenValue.XML">

                                <div class="form-group m-b15">
                                    <label class="label-title">PO Number *</label>
                                    <input
                                        type="text"
                                        name="PONumber"
                                        class="form-control"
                                        value="$Order.PONumber.XML"
                                        placeholder="Enter the PO number for this order."
                                        required
                                    >
                                </div>

                                <button type="submit" class="btn btn-secondary">APPROVE ORDER</button>
                            </form>
                        </div>

                        <form method="post" action="$Order.PendingOrderLink(reject)" class="m-b30">
                            <input type="hidden" name="SecurityID" value="$SecurityTokenValue.XML">

                            <div class="form-group m-b15">
                                <label class="label-title">Rejection reason *</label>
                                <textarea
                                    name="RejectionReason"
                                    class="form-control"
                                    rows="5"
                                    placeholder="Explain why this order is being rejected."
                                    required
                                ></textarea>
                            </div>

                            <button type="submit" class="btn btn-danger">REJECT ORDER</button>
                        </form>
                    <% end_if %>
                <% end_if %>
            </div>

            <div class="col-xl-4 side-bar">
                <h4 class="title m-b15">Order Items</h4>
                <div class="order-detail sticky-top">
                    <% if $Order.Items %>
                        <% loop $Order.Items %>
                            <% if $OptionsNice %>
                                <small class="d-block mb-1">$OptionsNice</small>
                            <% end_if %>
                            <div class="cart-item style-1 d-flex align-items-start <% if Last %>mb-0<% end_if %>">
                                <div class="dz-media me-3">
                                    <% if $Product.FeaturedImage %>
                                        <img src="$Product.FeaturedImage.Fill(80,80).URL" alt="$ProductTitle.XML">
                                    <% end_if %>
                                </div>

                                <div class="dz-content" style="padding-left:0;">
                                    <h6 class="title mb-1">$ProductTitle</h6>
                                    <small class="d-block mb-1">Qty: $Quantity</small>
                                    <span class="price">$LineTotal.Nice</span>
                                </div>
                            </div>
                        <% end_loop %>
                    <% else %>
                        <div class="cart-item style-1 mb-0">
                            <div class="dz-content">
                                <h6 class="title mb-0">No items found</h6>
                            </div>
                        </div>
                    <% end_if %>

                    <table>
                        <tbody>
                            <tr class="total">
                                <td>Total</td>
                                <td class="price">$Order.Total.Nice</td>
                            </tr>
                        </tbody>
                    </table>

                    <a href="$Top.Link" class="btn btn-secondary w-100">BACK TO PENDING ORDERS</a>
                </div>
            </div>
        </div>
    </div>
</div>
<% include FooterInfoBox %>
