<!--banner-->
<div class="dz-bnr-inr" style="background-image:url( <% if SiteConfig.BannerImage %> $SiteConfig.BannerImage.URL <% else %>$themedResourceURL('images/background/bg-shape.jpg') <% end_if %>);">
    <div class="container">
        <div class="dz-bnr-inr-entry">
            <h1>Checkout</h1>
            <nav aria-label="breadcrumb" class="breadcrumb-row">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/"> Home</a></li>
                    <li class="breadcrumb-item">Checkout</li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- inner page banner End-->
<div class="content-inner-1">
    <div class="container">

        <% if $HasSubmittedOrderConfirmation && $SubmittedOrder %>
            <div class="row shop-checkout">
                <div class="col-xl-8">
                    <h4 class="title m-b15">Order submitted</h4>

                    <% if $CheckoutMessage %>
                        <div class="alert alert-info">$CheckoutMessage</div>
                    <% end_if %>

                    <div class="shop-form">
                        <div class="alert alert-success">
                            <strong>Order number:</strong> $SubmittedOrder.OrderNumber
                        </div>

                        <% if $SubmittedOrder.RequiresApproval %>
                            <div class="alert alert-warning">
                                This order is currently pending approval.
                            </div>
                        <% end_if %>
                        <p>
                            Thank you. Your order has been submitted successfully.
                        </p>
                    </div>
                </div>

                <div class="col-xl-4 side-bar">
                    <h4 class="title m-b15">Your Order</h4>
                    <div class="order-detail sticky-top">
                        <% if $SubmittedOrder.Items %>
                            <% loop $SubmittedOrder.Items %>
                                <div class="cart-item style-1 <% if Last %>mb-0<% end_if %>">
                                    <div class="dz-content" style="padding-left:0;">
                                        <h6 class="title mb-0">
                                            $ProductTitle
                                            <br>
                                            <small>Qty: $Quantity</small>
                                        </h6>

                                        <% if $OptionsNice %>
                                            <small>$OptionsNice</small><br>
                                        <% end_if %>

                                        <span class="price">$LineTotal.Nice</span>
                                    </div>
                                </div>
                            <% end_loop %>
                        <% else %>
                            <div class="cart-item style-1 mb-0">
                                <div class="dz-content">
                                    <h6 class="title mb-0">No order items found</h6>
                                </div>
                            </div>
                        <% end_if %>

                        <table>
                            <tbody>
                                <tr class="subtotal">
                                    <td>Total inc VAT</td>
                                    <td class="price">$SubmittedOrder.TotalIncludingVAT.Nice</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <% else %>

            <div class="row shop-checkout">
                <div class="col-xl-8">
                    <h4 class="title m-b15">Order details</h4>

                    <% if $CheckoutMessage %>
                        <div class="alert alert-info">$CheckoutMessage</div>
                    <% end_if %>

                    <form class="row" method="post" action="$Link(placeOrder)" id="checkout-form">
                        <input type="hidden" name="SecurityID" value="$SecurityTokenValue.XML">

                        <div class="col-md-12">
                            <div class="form-group m-b25">
                                <label class="label-title">How would you like to receive this order? *</label>

                                <div class="custom-control custom-checkbox m-b10">
                                    <input class="form-check-input radio fulfilment-method" type="radio" name="FulfilmentMethod" id="fulfilment_collection" value="collection" checked>
                                    <label class="form-check-label" for="fulfilment_collection">
                                        Collection from Champion Workwear
                                    </label>
                                </div>

                                <div class="custom-control custom-checkbox">
                                    <input class="form-check-input radio fulfilment-method" type="radio" name="FulfilmentMethod" id="fulfilment_delivery" value="delivery">
                                    <label class="form-check-label" for="fulfilment_delivery">
                                        Delivery to chosen address
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group m-b25">
                                <label class="label-title">PO Number (optional)</label>
                                <input name="PONumber" class="form-control">
                            </div>
                        </div>

                        <div id="delivery-fields" style="display:none;">
                            <div class="col-md-12">
                                <h4 class="title m-b15">Delivery address</h4>
                            </div>

                            <%-- Pre Saved Addresses Development: select an existing customer address and populate the normal delivery fields. --%>
                            <div class="col-md-12">
                                <div class="form-group m-b25">
                                    <label class="label-title">Saved Address</label>
                                    <div class="form-select">
                                        <select name="SavedDeliveryAddressID" id="saved-delivery-address" class="default-select w-100">
                                            <option value="">Enter a new delivery address</option>
                                            <% loop $SavedAddresses %>
                                                <option
                                                    value="$ID"
                                                    data-delivery-company="$DeliveryCompany.ATT"
                                                    data-delivery-contact-name="$DeliveryContactName.ATT"
                                                    data-delivery-phone="$DeliveryPhone.ATT"
                                                    data-delivery-email="$DeliveryEmail.ATT"
                                                    data-delivery-address-line1="$DeliveryAddressLine1.ATT"
                                                    data-delivery-address-line2="$DeliveryAddressLine2.ATT"
                                                    data-delivery-city="$DeliveryCity.ATT"
                                                    data-delivery-county="$DeliveryCounty.ATT"
                                                    data-delivery-postcode="$DeliveryPostcode.ATT"
                                                >
                                                    $DropdownTitle
                                                </option>
                                            <% end_loop %>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group m-b25">
                                    <label class="label-title">Company name *</label>
                                    <input name="DeliveryCompany" class="form-control delivery-required">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group m-b25">
                                    <label class="label-title">Contact name *</label>
                                    <input name="DeliveryContactName" class="form-control delivery-required">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group m-b25">
                                    <label class="label-title">Phone *</label>
                                    <input name="DeliveryPhone" class="form-control delivery-required">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group m-b25">
                                    <label class="label-title">Email address *</label>
                                    <input name="DeliveryEmail" class="form-control delivery-required">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group m-b25">
                                    <label class="label-title">Address line 1 *</label>
                                    <input name="DeliveryAddressLine1" class="form-control delivery-required" placeholder="House number and street name">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group m-b25">
                                    <label class="label-title">Address line 2</label>
                                    <input name="DeliveryAddressLine2" class="form-control" placeholder="Apartment, suite, unit, etc. (optional)">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group m-b25">
                                    <label class="label-title">Town / City *</label>
                                    <input name="DeliveryCity" class="form-control delivery-required">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group m-b25">
                                    <label class="label-title">County *</label>
                                    <input name="DeliveryCounty" class="form-control delivery-required">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group m-b25">
                                    <label class="label-title">Postcode *</label>
                                    <input name="DeliveryPostcode" class="form-control delivery-required">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 m-b25">
                            <div class="form-group">
                                <label class="label-title">Order notes (optional)</label>
                                <textarea id="comments" placeholder="Notes about your order, delivery, or collection." class="form-control" name="OrderNotes" cols="90" rows="5"></textarea>
                            </div>
                        </div>

                        <%-- Pre Saved Addresses Development: checkout can save only newly entered delivery addresses, not modified dropdown selections. --%>
                        <div class="col-md-12 m-b25" id="save-delivery-address-wrap" style="display:none;">
                            <div class="form-group m-b15">
                                <div class="custom-control custom-checkbox">
                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        id="save_delivery_address"
                                        name="SaveDeliveryAddress"
                                        value="1"
                                    >
                                    <label class="form-check-label" for="save_delivery_address">Save this delivery address to my account</label>
                                </div>
                            </div>

                            <div class="form-group" id="save-delivery-address-title-wrap" style="display:none;">
                                <label class="label-title">Address Name *</label>
                                <input
                                    name="SavedDeliveryAddressTitle"
                                    id="saved-delivery-address-title"
                                    class="form-control"
                                    placeholder="e.g. Main Office"
                                >
                            </div>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-secondary">SUBMIT ORDER</button>
                        </div>
                    </form>
                </div>

                <div class="col-xl-4 side-bar">
                    <h4 class="title m-b15">Your Order</h4>
                    <div class="order-detail sticky-top">
                        <% if $CartItems %>
                            <% loop $CartItems %>
                                <div class="cart-item style-1 <% if Last %>mb-0<% end_if %>">
                                    <div class="dz-media">
                                        <% if $ImageURL %>
                                            <img src="$ImageURL" alt="$Title.XML">
                                        <% else %>
                                            <img src="$ThemeDir/images/shop/shop-cart/pic1.jpg" alt="$Title.XML">
                                        <% end_if %>
                                    </div>
                                    <div class="dz-content">
                                        <h6 class="title mb-0">
                                            $Title
                                            <br>
                                            <small>Qty: $Qty</small>
                                        </h6>
                                        <span class="price">$LineTotal.Nice</span>
                                    </div>
                                </div>
                            <% end_loop %>
                        <% else %>
                            <div class="cart-item style-1 mb-0">
                                <div class="dz-content">
                                    <h6 class="title mb-0">Your cart is empty</h6>
                                </div>
                            </div>
                        <% end_if %>

                        <table>
                            <tbody>
                                <tr class="subtotal">
                                    <td>Subtotal</td>
                                    <td class="price">$CartTotal.Nice</td>
                                </tr>
                                <tr class="title">
                                    <td><h6 class="title font-weight-500">Fulfilment</h6></td>
                                    <td></td>
                                </tr>
                                <tr class="shipping">
                                    <td>
                                        Collection from Champion Workwear or delivery to your chosen address
                                    </td>
                                    <td class="price">On account</td>
                                </tr>
                                <tr class="total">
                                    <td>Total inc VAT</td>
                                    <td class="price">$CartTotalIncludingVAT.Nice</td>
                                </tr>
                            </tbody>
                        </table>

                        <p class="text">
                            No payment is taken on the website. Orders are submitted on account and processed outside of the website.
                        </p>

                        <button type="submit" form="checkout-form" class="btn btn-secondary w-100">PLACE ORDER</button>
                    </div>
                </div>
            </div>

        <% end_if %>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const collectionRadio = document.getElementById('fulfilment_collection');
        const deliveryRadio = document.getElementById('fulfilment_delivery');
        const deliveryFields = document.getElementById('delivery-fields');
        const savedAddressSelect = document.getElementById('saved-delivery-address');
        const saveAddressWrap = document.getElementById('save-delivery-address-wrap');
        const saveAddressCheckbox = document.getElementById('save_delivery_address');
        const saveAddressTitleWrap = document.getElementById('save-delivery-address-title-wrap');
        const saveAddressTitleInput = document.getElementById('saved-delivery-address-title');

        function toggleDeliveryFields() {
            if (!collectionRadio || !deliveryRadio || !deliveryFields) {
                return;
            }

            const isDelivery = deliveryRadio.checked;

            deliveryFields.style.display = isDelivery ? '' : 'none';

            deliveryFields.querySelectorAll('.delivery-required').forEach(function (field) {
                field.required = isDelivery;
            });

            syncSaveAddressControls();
        }

        // Pre Saved Addresses Development: saving is only available for new manually entered checkout addresses.
        function syncSaveAddressControls() {
            const isDelivery = deliveryRadio && deliveryRadio.checked;
            const hasSelectedSavedAddress = savedAddressSelect && savedAddressSelect.value;
            const canSaveNewAddress = isDelivery && !hasSelectedSavedAddress;

            if (saveAddressWrap) {
                saveAddressWrap.style.display = isDelivery ? '' : 'none';
            }

            if (saveAddressCheckbox) {
                saveAddressCheckbox.disabled = !canSaveNewAddress;

                if (!canSaveNewAddress) {
                    saveAddressCheckbox.checked = false;
                }
            }

            const showTitle = canSaveNewAddress && saveAddressCheckbox && saveAddressCheckbox.checked;

            if (saveAddressTitleWrap) {
                saveAddressTitleWrap.style.display = showTitle ? '' : 'none';
            }

            if (saveAddressTitleInput) {
                saveAddressTitleInput.required = !!showTitle;
                saveAddressTitleInput.disabled = !showTitle;

                if (!showTitle) {
                    saveAddressTitleInput.value = '';
                }
            }
        }

        if (collectionRadio && deliveryRadio && deliveryFields) {
            collectionRadio.addEventListener('change', toggleDeliveryFields);
            deliveryRadio.addEventListener('change', toggleDeliveryFields);
            toggleDeliveryFields();
        }

        function getSavedAddressFieldMap() {
            return {
                DeliveryCompany: 'deliveryCompany',
                DeliveryContactName: 'deliveryContactName',
                DeliveryPhone: 'deliveryPhone',
                DeliveryEmail: 'deliveryEmail',
                DeliveryAddressLine1: 'deliveryAddressLine1',
                DeliveryAddressLine2: 'deliveryAddressLine2',
                DeliveryCity: 'deliveryCity',
                DeliveryCounty: 'deliveryCounty',
                DeliveryPostcode: 'deliveryPostcode'
            };
        }

        // Pre Saved Addresses Development: clear prefilled values if the customer moves back to entering a new address.
        function clearDeliveryAddressFields() {
            if (!deliveryFields) {
                return;
            }

            Object.keys(getSavedAddressFieldMap()).forEach(function (fieldName) {
                const input = deliveryFields.querySelector('[name="' + fieldName + '"]');

                if (input) {
                    input.value = '';
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
        }

        // Pre Saved Addresses Development: fill checkout delivery inputs from the chosen saved address so native validation still passes.
        function applySavedAddress() {
            if (!savedAddressSelect || !deliveryFields) {
                return;
            }

            const option = savedAddressSelect.options[savedAddressSelect.selectedIndex];

            if (!option || !option.value) {
                clearDeliveryAddressFields();
                return;
            }

            const fieldMap = getSavedAddressFieldMap();

            Object.keys(fieldMap).forEach(function (fieldName) {
                const input = deliveryFields.querySelector('[name="' + fieldName + '"]');

                if (input) {
                    input.value = option.dataset[fieldMap[fieldName]] || '';
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
        }

        if (savedAddressSelect) {
            savedAddressSelect.addEventListener('change', function () {
                applySavedAddress();
                syncSaveAddressControls();
            });
        }

        if (saveAddressCheckbox) {
            saveAddressCheckbox.addEventListener('change', syncSaveAddressControls);
        }

        syncSaveAddressControls();
    });
</script>

<% include FooterInfoBox %>
