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

$OrderSummaryHTML.RAW

<p style="margin-top:25px; margin-bottom:0;">
    Please log in to review this order.
</p>
