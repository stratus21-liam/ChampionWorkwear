<p>Hi <% if $Customer.FirstName %>$Customer.FirstName.XML<% else %>$Customer.Name.XML<% end_if %>,</p>

<p>
    Your order <strong>$Order.OrderNumber</strong> has been approved.
</p>

$OrderSummaryHTML.RAW

<p style="margin-top:25px;">
    Thank you,<br>
    $SiteConfig.Title.XML
</p>
