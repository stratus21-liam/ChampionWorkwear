<p>Hi <% if $Customer.FirstName %>$Customer.FirstName.XML<% else %>$Customer.Name.XML<% end_if %>,</p>

<p>
    Your order <strong>$Order.OrderNumber</strong> has been rejected.
</p>

<% if $Order.RejectionReason %>
    <p>
        <strong>Reason for rejection:</strong><br>
        $Order.RejectionReason
    </p>
<% end_if %>

<% include OrderSummary Order=$Order Items=$Items ShowCustomerDetails=false ShowDeliveryDetails=true ShowItemOptions=true %>

<p style="margin-top:25px;">
    Please contact your account administrator if you need further information.
</p>