<!--banner-->
<div class="dz-bnr-inr" style="background-image:url( <% if SiteConfig.BannerImage %> $SiteConfig.BannerImage.URL <% else %>$themedResourceURL('theme/images/background/bg-shape.jpg') <% end_if %>);">
    <div class="container">
        <div class="dz-bnr-inr-entry">
            <h1><% if $Order %> Order: $Order.OrderNumber <% else %> My Order <% end_if %></h1>
            <nav aria-label="breadcrumb" class="breadcrumb-row">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="$OrderHistoryPageLink"> Order History</a></li>
                    <li class="breadcrumb-item">My Order</li>
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
                                    <h6 class="title mb-1">
                                        $ProductTitle
                                    </h6>

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

                    <a href="$Top.Link" class="btn btn-secondary w-100">BACK TO ORDER HISTORY</a>
                </div>
            </div>
        </div>
    </div>
</div>
<% include FooterInfoBox %>