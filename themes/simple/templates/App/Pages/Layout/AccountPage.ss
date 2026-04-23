<!--banner-->
<div class="dz-bnr-inr" style="background-image:url( <% if SiteConfig.BannerImage %> $SiteConfig.BannerImage.URL <% else %>$themedResourceURL('theme/images/background/bg-shape.jpg') <% end_if %>);">
    <div class="container">
        <div class="dz-bnr-inr-entry">
            <h1>My Account</h1>
            <nav aria-label="breadcrumb" class="breadcrumb-row">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="$DashboardPageLink"> Dashboard</a></li>
                    <li class="breadcrumb-item">My Account</li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<div class="content-inner-1">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center m-b20">
            <a href="$DashboardPageLink" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
            </a>
        </div>

        <% if $AccountMessage %>
            <div class="alert alert-info m-b25">$AccountMessage</div>
        <% end_if %>

        <%-- Pre Saved Addresses Development: open Edit Addresses when redirected with ?editAddress=ID. --%>
        <ul class="nav nav-tabs m-b30" id="account-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link <% if not $EditAddressID %>active<% end_if %>"
                    id="edit-profile-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#edit-profile"
                    type="button"
                    role="tab"
                    aria-controls="edit-profile"
                    aria-selected="<% if not $EditAddressID %>true<% else %>false<% end_if %>"
                >
                    Edit Profile
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link <% if $EditAddressID %>active<% end_if %>"
                    id="edit-addresses-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#edit-addresses"
                    type="button"
                    role="tab"
                    aria-controls="edit-addresses"
                    aria-selected="<% if $EditAddressID %>true<% else %>false<% end_if %>"
                >
                    Edit Addresses
                </button>
            </li>
        </ul>

        <div class="tab-content" id="account-tabs-content">
            <div
                class="tab-pane fade <% if not $EditAddressID %>show active<% end_if %>"
                id="edit-profile"
                role="tabpanel"
                aria-labelledby="edit-profile-tab"
            >
                <div class="row shop-checkout">
                    <div class="col-xl-12">
                        <h4 class="title m-b15">Edit Account</h4>

                        <form class="row" method="post" action="$Link(saveAccount)">
                            <div class="col-md-6">
                                <div class="form-group m-b25">
                                    <label class="label-title">First Name</label>
                                    <input name="FirstName" class="form-control" value="$CurrentMember.FirstName" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group m-b25">
                                    <label class="label-title">Surname</label>
                                    <input name="Surname" class="form-control" value="$CurrentMember.Surname" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group m-b25">
                                    <label class="label-title">
                                        Email Address
                                        <i
                                            class="fa fa-info-circle ms-1 text-muted"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Changing this will update your login credentials."
                                            style="cursor:pointer;"
                                        ></i>
                                    </label>
                                    <input name="Email" type="email" class="form-control" value="$CurrentMember.Email" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group m-b25">
                                    <label class="label-title">Password</label>
                                    <input name="Password" type="password" class="form-control" placeholder="Leave blank to keep current password">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group m-b25">
                                    <label class="label-title">Confirm Password</label>
                                    <input name="ConfirmPassword" type="password" class="form-control" placeholder="Repeat new password">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-secondary">
                                    Save Account
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div
                class="tab-pane fade <% if $EditAddressID %>show active<% end_if %>"
                id="edit-addresses"
                role="tabpanel"
                aria-labelledby="edit-addresses-tab"
            >
                <div class="row shop-checkout">
                    <%-- Pre Saved Addresses Development: customers can create reusable delivery addresses for checkout. --%>
                    <div class="col-xl-6">
                        <h4 class="title m-b15">Create Address</h4>

                        <form class="row" method="post" action="$Link(createAddress)">
                            <div class="col-md-12">
                                <div class="form-group m-b25">
                                    <label class="label-title">Address Name *</label>
                                    <input name="Title" class="form-control" placeholder="e.g. Main Office" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group m-b25">
                                    <label class="label-title">Company Name *</label>
                                    <input name="DeliveryCompany" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group m-b25">
                                    <label class="label-title">Contact Name *</label>
                                    <input name="DeliveryContactName" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group m-b25">
                                    <label class="label-title">Phone *</label>
                                    <input name="DeliveryPhone" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group m-b25">
                                    <label class="label-title">Email Address *</label>
                                    <input name="DeliveryEmail" type="email" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group m-b25">
                                    <label class="label-title">Address Line 1 *</label>
                                    <input name="DeliveryAddressLine1" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group m-b25">
                                    <label class="label-title">Address Line 2</label>
                                    <input name="DeliveryAddressLine2" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group m-b25">
                                    <label class="label-title">Town / City *</label>
                                    <input name="DeliveryCity" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group m-b25">
                                    <label class="label-title">County *</label>
                                    <input name="DeliveryCounty" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group m-b25">
                                    <label class="label-title">Postcode *</label>
                                    <input name="DeliveryPostcode" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-secondary">Create Address</button>
                            </div>
                        </form>
                    </div>

                    <%-- Pre Saved Addresses Development: customers can select and edit their existing reusable delivery addresses. --%>
                    <div class="col-xl-6">
                        <h4 class="title m-b15">Edit Address</h4>

                        <div class="form-group m-b25">
                            <label class="label-title">Select Address</label>
                            <div class="form-select">
                                <select id="account-address-select" class="default-select w-100">
                                    <option value="">Select an address</option>
                                    <% loop $SavedAddresses %>
                                        <option value="$ID" <% if $Top.EditAddressID = $ID %>selected="selected"<% end_if %>>$DropdownTitle</option>
                                    <% end_loop %>
                                </select>
                            </div>
                        </div>

                        <div id="account-address-edit-forms">
                            <% if $SavedAddresses %>
                                <% loop $SavedAddresses %>
                                    <div class="account-address-edit-card" data-address-id="$ID" style="display:none;">
                                        <div class="card dz-card style-2 p-4">
                                            <h5 class="title m-b20">$Title</h5>

                                            <form class="row" method="post" action="$Top.Link(updateAddress)/$ID">
                                                <div class="col-md-12">
                                                    <div class="form-group m-b25">
                                                        <label class="label-title">Address Name *</label>
                                                        <input name="Title" class="form-control" value="$Title" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group m-b25">
                                                        <label class="label-title">Company Name *</label>
                                                        <input name="DeliveryCompany" class="form-control" value="$DeliveryCompany" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group m-b25">
                                                        <label class="label-title">Contact Name *</label>
                                                        <input name="DeliveryContactName" class="form-control" value="$DeliveryContactName" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group m-b25">
                                                        <label class="label-title">Phone *</label>
                                                        <input name="DeliveryPhone" class="form-control" value="$DeliveryPhone" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group m-b25">
                                                        <label class="label-title">Email Address *</label>
                                                        <input name="DeliveryEmail" type="email" class="form-control" value="$DeliveryEmail" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group m-b25">
                                                        <label class="label-title">Address Line 1 *</label>
                                                        <input name="DeliveryAddressLine1" class="form-control" value="$DeliveryAddressLine1" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group m-b25">
                                                        <label class="label-title">Address Line 2</label>
                                                        <input name="DeliveryAddressLine2" class="form-control" value="$DeliveryAddressLine2">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group m-b25">
                                                        <label class="label-title">Town / City *</label>
                                                        <input name="DeliveryCity" class="form-control" value="$DeliveryCity" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group m-b25">
                                                        <label class="label-title">County *</label>
                                                        <input name="DeliveryCounty" class="form-control" value="$DeliveryCounty" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group m-b25">
                                                        <label class="label-title">Postcode *</label>
                                                        <input name="DeliveryPostcode" class="form-control" value="$DeliveryPostcode" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <button type="submit" class="btn btn-secondary w-100">Save Address</button>
                                                </div>
                                            </form>

                                            <form method="post" action="$Top.Link(deleteAddress)/$ID" onsubmit="return confirm('Delete this address?');" class="m-t15">
                                                <button type="submit" class="btn btn-danger w-100">Delete Address</button>
                                            </form>
                                        </div>
                                    </div>
                                <% end_loop %>
                            <% else %>
                                <p>No addresses found.</p>
                            <% end_if %>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<%-- Pre Saved Addresses Development: show the selected address edit form, following the Manage Users interaction pattern. --%>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var addressSelect = document.getElementById('account-address-select');
        var addressCards = document.querySelectorAll('.account-address-edit-card');

        function toggleAddressCard() {
            var selectedId = addressSelect ? addressSelect.value : '';

            addressCards.forEach(function (card) {
                card.style.display = (selectedId && card.getAttribute('data-address-id') === selectedId) ? '' : 'none';
            });
        }

        if (addressSelect) {
            addressSelect.addEventListener('change', toggleAddressCard);
            toggleAddressCard();
        }
    });
</script>

<% include FooterInfoBox %>
