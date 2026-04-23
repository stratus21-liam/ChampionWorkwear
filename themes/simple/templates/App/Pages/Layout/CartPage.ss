<!--banner-->
<div class="dz-bnr-inr" style="background-image:url( <% if SiteConfig.BannerImage %> $SiteConfig.BannerImage.URL <% else %>$themedResourceURL('images/background/bg-shape.jpg') <% end_if %>);">
    <div class="container">
        <div class="dz-bnr-inr-entry">
            <h1>Cart</h1>
            <nav aria-label="breadcrumb" class="breadcrumb-row">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/"> Home</a></li>
                    <li class="breadcrumb-item">Cart</li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- contact area -->
<section class="content-inner shop-account">
    <!-- Product -->
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="table-responsive">
                    <table class="table check-tbl">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th></th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <% if $CartItems %>
                                <% loop $CartItems %>
                                    <tr>
                                        <td class="product-item-img">
                                            <% if $ImageURL %>
                                                <img src="$ImageURL" alt="$Title.XML">
                                            <% else %>
                                                <img src="$ThemeDir/images/shop/shop-cart/pic1.jpg" alt="$Title.XML">
                                            <% end_if %>
                                        </td>

                                        <td class="product-item-name">
                                            <% if $ProductLink %>
                                                <a href="$ProductLink">$Title</a>
                                            <% else %>
                                                $Title
                                            <% end_if %>

                                            <% if $Attributes %>
                                                <div class="cart-meta mt-1">
                                                    <% loop $Attributes %>
                                                        <small class="d-block">$Label.XML: $Value.XML</small>
                                                    <% end_loop %>
                                                </div>
                                            <% end_if %>
                                        </td>

                                        <td class="product-item-price">$Price.Nice</td>

                                        <td class="product-item-quantity">
                                            <form method="post" action="$Top.Link(updateCartItem)/$Key" class="cart-update-form">
                                                <input type="hidden" name="SecurityID" value="$Top.SecurityTokenValue.XML">

                                                <div class="quantity btn-quantity style-1 me-3">
                                                    <input type="text" value="$Qty" name="Quantity" class="cart-qty-input">
                                                </div>
                                            </form>
                                        </td>

                                        <td class="product-item-totle">$LineTotal.Nice</td>

                                        <td class="product-item-close">
                                            <a href="$Top.Link(removeCartItem)/$Key">
                                                <i class="ti-close"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <% end_loop %>
                            <% else %>
                                <tr>
                                    <td colspan="6" class="text-center">Your cart is empty</td>
                                </tr>
                            <% end_if %>
                        </tbody>
                    </table>
                </div>
                <% if $CartMessage %>
                    <div class="alert alert-info">$CartMessage</div>
                <% end_if %>                
            </div>

            <div class="col-lg-4">
                <h4 class="title mb15">Cart Total</h4>
                <div class="cart-detail">

                    <div class="save-text">
                        <i class="icon feather icon-check-circle"></i>
                        <span class="m-l10">You have $CartCount item(s) in your cart</span>
                    </div>

                    <table>
                        <tbody>
                            <tr class="total">
                                <td>
                                    <h6 class="mb-0">Total</h6>
                                </td>
                                <td class="price">
                                    $CartTotal.Nice
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <% if $CheckoutPageLink %>
                        <a href="$CheckoutPageLink" class="btn btn-secondary w-100">PLACE ORDER</a>
                    <% else %>
                        <a href="javascript:void(0);" class="btn btn-secondary w-100">PLACE ORDER</a>
                    <% end_if %>
                </div>
            </div>
        </div>
    </div>
    <!-- Product END -->
</section>
<!-- contact area End-->

<% include FooterInfoBox %>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.cart-qty-input').forEach(function (input) {
                var form = input.closest('form');
                if (!form) {
                    return;
                }

                var lastValue = input.value;

                input.addEventListener('change', function () {
                    form.submit();
                });

                setInterval(function () {
                    if (input.value !== lastValue) {
                        lastValue = input.value;
                        form.submit();
                    }
                }, 300);
            });
        });
    </script>