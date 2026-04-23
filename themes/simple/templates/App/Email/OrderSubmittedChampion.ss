<p style="margin-top:0;">
    Hi
    <% if $Recipient && $Recipient.Title %>
        $Recipient.Title.XML
    <% else %>
        Champion Team
    <% end_if %>,
</p>

<p>
    A new order has been submitted through the B2B ordering portal.
</p>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin:20px 0;">
    <tr>
        <td style="padding:6px 0; width:180px;"><strong>Order Number:</strong></td>
        <td style="padding:6px 0;">$Order.OrderNumber</td>
    </tr>
    <tr>
        <td style="padding:6px 0;"><strong>Status:</strong></td>
        <td style="padding:6px 0;">$Order.Status</td>
    </tr>
    <tr>
        <td style="padding:6px 0;"><strong>Requires Approval:</strong></td>
        <td style="padding:6px 0;">
            <% if $Order.RequiresApproval %>Yes<% else %>No<% end_if %>
        </td>
    </tr>
    <tr>
        <td style="padding:6px 0;"><strong>Customer Account:</strong></td>
        <td style="padding:6px 0;">
            <% if $Order.CustomerAccount %>
                $Order.CustomerAccount.Title.XML
            <% end_if %>
        </td>
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
        <td style="padding:6px 0;"><strong>Customer Email:</strong></td>
        <td style="padding:6px 0;">
            <% if $Customer %>$Customer.Email.XML<% end_if %>
        </td>
    </tr>
    <tr>
        <td style="padding:6px 0;"><strong>Fulfilment:</strong></td>
        <td style="padding:6px 0;">$Order.FulfilmentMethodNice</td>
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
    <tr>
        <td style="padding:6px 0;"><strong>Submitted:</strong></td>
        <td style="padding:6px 0;">
            <% if $Order.SubmittedAt %>$Order.SubmittedAt.Nice<% end_if %>
        </td>
    </tr>
</table>

<% include OrderSummary Order=$Order Items=$Items ShowCustomerDetails=true ShowDeliveryDetails=true ShowItemOptions=true %>

<p style="margin-top:25px; margin-bottom:0;">
    Please review and process this order.
</p>