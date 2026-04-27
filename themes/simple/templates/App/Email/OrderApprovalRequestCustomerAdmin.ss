<p style="margin-top:0;">
    Hi
    <% if $Admin.FirstName %>
        $Admin.FirstName.XML
    <% else_if $Admin.Name %>
        $Admin.Name.XML
    <% else %>
        there
    <% end_if %>,
</p>

<p>
    An order has been placed within your company account and is awaiting your approval.
</p>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin:20px 0;">
    <tr>
        <td style="padding:6px 0; width:180px;"><strong>Order Number:</strong></td>
        <td style="padding:6px 0;">$Order.OrderNumber</td>
    </tr>
    <tr>
        <td style="padding:6px 0;"><strong>Placed By:</strong></td>
        <td style="padding:6px 0;">
            <% if $Customer %>
                <% if $Customer.Name %>
                    $Customer.Name.XML
                <% else %>
                    $Customer.Email.XML
                <% end_if %>
            <% end_if %>
        </td>
    </tr>
    <tr>
        <td style="padding:6px 0;"><strong>Email:</strong></td>
        <td style="padding:6px 0;">
            <% if $Customer %>$Customer.Email.XML<% end_if %>
        </td>
    </tr>
    <tr>
        <td style="padding:6px 0;"><strong>Customer Account:</strong></td>
        <td style="padding:6px 0;">
            <% if $Order.CustomerAccount %>$Order.CustomerAccount.Title.XML<% end_if %>
        </td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin-top:20px;">
    <tr>
        <td colspan="2" style="padding:0 0 12px;">
            <h3 style="margin:0; font-size:20px; color:#006892;">Order summary</h3>
        </td>
    </tr>
    <tr>
        <td style="padding:6px 0; width:180px;"><strong>Order Number:</strong></td>
        <td style="padding:6px 0;">$Order.OrderNumber</td>
    </tr>
    <tr>
        <td style="padding:6px 0;"><strong>Status:</strong></td>
        <td style="padding:6px 0;">$Order.Status</td>
    </tr>
    <tr>
        <td style="padding:6px 0;"><strong>Fulfilment:</strong></td>
        <td style="padding:6px 0;">$Order.FulfilmentMethodNice</td>
    </tr>
    <tr>
        <td style="padding:6px 0;"><strong>Total Quantity:</strong></td>
        <td style="padding:6px 0;">$Order.TotalQuantity</td>
    </tr>
    <tr>
        <td style="padding:6px 0;"><strong>Total:</strong></td>
        <td style="padding:6px 0;"><strong>$Order.Total.Nice</strong></td>
    </tr>
    <% if $Order.PONumber %>
        <tr>
            <td style="padding:6px 0;"><strong>PO Number:</strong></td>
            <td style="padding:6px 0;">$Order.PONumber.XML</td>
        </tr>
    <% end_if %>
    <% if $Order.OrderNotes %>
        <tr>
            <td style="padding:6px 0; vertical-align:top;"><strong>Order Notes:</strong></td>
            <td style="padding:6px 0;">$Order.OrderNotes</td>
        </tr>
    <% end_if %>
</table>

<% if $ShowCustomerDetails && $Customer %>
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin-top:20px;">
        <tr>
            <td colspan="2" style="padding:0 0 12px;">
                <h3 style="margin:0; font-size:20px; color:#006892;">Customer details</h3>
            </td>
        </tr>
        <tr>
            <td style="padding:6px 0; width:180px;"><strong>Name:</strong></td>
            <td style="padding:6px 0;">
                <% if $Customer.Name %>
                    $Customer.Name.XML
                <% else %>
                    $Customer.Email.XML
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td style="padding:6px 0;"><strong>Email:</strong></td>
            <td style="padding:6px 0;">$Customer.Email.XML</td>
        </tr>
        <% if $Order.CustomerAccount %>
            <tr>
                <td style="padding:6px 0;"><strong>Customer Account:</strong></td>
                <td style="padding:6px 0;">$Order.CustomerAccount.Title.XML</td>
            </tr>
        <% end_if %>
    </table>
<% end_if %>

<% if $ShowDeliveryDetails && $Order.IsDelivery %>
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin-top:20px;">
        <tr>
            <td colspan="2" style="padding:0 0 12px;">
                <h3 style="margin:0; font-size:20px; color:#006892;">Delivery details</h3>
            </td>
        </tr>
        <tr>
            <td style="padding:6px 0; width:180px;"><strong>Company:</strong></td>
            <td style="padding:6px 0;">$Order.DeliveryCompany.XML</td>
        </tr>
        <tr>
            <td style="padding:6px 0;"><strong>Contact Name:</strong></td>
            <td style="padding:6px 0;">$Order.DeliveryContactName.XML</td>
        </tr>
        <tr>
            <td style="padding:6px 0;"><strong>Phone:</strong></td>
            <td style="padding:6px 0;">$Order.DeliveryPhone.XML</td>
        </tr>
        <tr>
            <td style="padding:6px 0;"><strong>Email:</strong></td>
            <td style="padding:6px 0;">$Order.DeliveryEmail.XML</td>
        </tr>
        <tr>
            <td style="padding:6px 0; vertical-align:top;"><strong>Address:</strong></td>
            <td style="padding:6px 0;">
                $Order.DeliveryAddressLine1.XML<br>
                <% if $Order.DeliveryAddressLine2 %>$Order.DeliveryAddressLine2.XML<br><% end_if %>
                $Order.DeliveryCity.XML<br>
                $Order.DeliveryCounty.XML<br>
                $Order.DeliveryPostcode.XML
            </td>
        </tr>
    </table>
<% else_if $ShowDeliveryDetails && $Order.IsCollection %>
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin-top:20px;">
        <tr>
            <td colspan="2" style="padding:0 0 12px;">
                <h3 style="margin:0; font-size:20px; color:#006892;">Collection</h3>
            </td>
        </tr>
        <tr>
            <td style="padding:6px 0;" colspan="2">
                Collection from Champion Workwear.
            </td>
        </tr>
    </table>
<% end_if %>

<% if $Items %>
    <table width="100%" cellpadding="8" cellspacing="0" border="1" style="margin-top:20px; border-collapse:collapse; border-color:#dddddd;">
        <thead>
            <tr style="background:#f7f7f7;">
                <th align="left">Product</th>
                <th align="left">SKU</th>
                <th align="left">Qty</th>
                <th align="left">Unit Price</th>
                <th align="left">Line Total</th>
            </tr>
        </thead>
        <tbody>
            <% loop $Items %>
                <tr>
                    <td style="vertical-align:top;">
                        <strong>$ProductTitle.XML</strong>
                        <% if $ShowItemOptions && $OptionsNice %>
                            <br>
                            <small>$OptionsNice.XML</small>
                        <% end_if %>
                    </td>
                    <td style="vertical-align:top;">$SKU.XML</td>
                    <td style="vertical-align:top;">$Quantity</td>
                    <td style="vertical-align:top;">$UnitPrice.Nice</td>
                    <td style="vertical-align:top;">$LineTotal.Nice</td>
                </tr>
            <% end_loop %>
            <tr>
                <td colspan="4" align="right"><strong>Total</strong></td>
                <td><strong>$Order.Total.Nice (Inc VAT)</strong></td>
            </tr>
        </tbody>
    </table>
<% end_if %>
<p style="margin-top:25px; margin-bottom:0;">
    Please log in to review this order.
</p>