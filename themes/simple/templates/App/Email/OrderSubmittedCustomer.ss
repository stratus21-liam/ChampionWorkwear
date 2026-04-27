<p style="margin-top:0;">
    Hi
    <% if $Customer.FirstName %>
        $Customer.FirstName.XML
    <% else_if $Customer.Name %>
        $Customer.Name.XML
    <% else %>
        there
    <% end_if %>,
</p>

<p>
    Thank you for your order. Your order
    <strong>$Order.OrderNumber</strong>
    has been received successfully.
</p>

<% if $Order.RequiresApproval %>
    <p>
        Your order is currently awaiting approval within your company before it can be processed.
    </p>
<% else %>
    <p>
        Your order has been submitted and is now with Champion for processing.
    </p>
<% end_if %>

$OrderSummaryHTML.RAW

<p style="margin-top:25px;">
    If you need to discuss this order, please quote your order number:
    <strong>$Order.OrderNumber</strong>.
</p>

<p style="margin-top:25px; margin-bottom:0;">
    Thank you,<br>
    Champion Workwear
</p>
