<!--banner-->
<div class="dz-bnr-inr" style="background-image:url( <% if SiteConfig.BannerImage %> $SiteConfig.BannerImage.URL <% else %>$themedResourceURL('theme/images/background/bg-shape.jpg') <% end_if %>);">
    <div class="container">
        <div class="dz-bnr-inr-entry">
            <h1>Manage Users</h1>
            <nav aria-label="breadcrumb" class="breadcrumb-row">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="$DashboardPageLink"> Dashboard</a></li>
                    <li class="breadcrumb-item">Manage Users</li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<div class="content-inner-1 manage-users-page">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center m-b20">
            <a href="$DashboardPageLink" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
            </a>
        </div>    
        <% if $DashboardMessage %>
            <div class="alert alert-info m-b25">$DashboardMessage</div>
        <% end_if %>        
        <div class="row shop-checkout">
            <%-- Create user --%>
            <div class="col-xl-6">
                <h4 class="title m-b15">Create User</h4>
                <form class="row" method="post" action="$Link(createUser)">

                    <div class="col-md-6">
                        <div class="form-group m-b25">
                            <label class="label-title">First Name</label>
                            <input name="FirstName" class="form-control" value="$CreateFormDataValue(FirstName)" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group m-b25">
                            <label class="label-title">Surname</label>
                            <input name="Surname" class="form-control" value="$CreateFormDataValue(Surname)" required>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group m-b25">
                            <label class="label-title">Email Address</label>
                            <input name="Email" type="email" class="form-control" value="$CreateFormDataValue(Email)" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group m-b25">
                            <label class="label-title">Password</label>
                            <input name="Password" type="password" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group m-b25">
                            <label class="label-title">Confirm Password</label>
                            <input name="ConfirmPassword" type="password" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="m-b25">
                            <label class="label-title">
                                Role
                                <i 
                                    class="fa fa-info-circle ms-1 text-muted"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Roles only apply to non admin account users. Account admins can see all products regardless of role."
                                    style="cursor:pointer;"
                                ></i>
                            </label>

                            <div class="form-select">
                                <select name="RoleID" class="default-select w-100">
                                    <option value="">Select role</option>
                                    <% loop $AvailableRoles %>
                                        <option value="$ID" <% if $Top.CreateFormDataValue(RoleID) = $ID %>selected="selected"<% end_if %>>$Title</option>
                                    <% end_loop %>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group m-b25 spend-limit-wrap">
                            <div class="custom-control custom-checkbox m-b10">
                                <input
                                    type="checkbox"
                                    class="form-check-input spend-limit-toggle"
                                    id="CreateEnableSpendLimit"
                                    name="EnableSpendLimit"
                                    value="1"
                                    <% if $CreateFormChecked(EnableSpendLimit) %>checked<% end_if %>
                                >
                                <label class="form-check-label" for="CreateEnableSpendLimit">Enable Spend Limit</label>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">£</span>
                                <input
                                    name="SpendLimit"
                                    type="text"
                                    class="form-control spend-limit-input"
                                    value="$CreateFormDataValue(SpendLimit)"
                                    placeholder="0.00"
                                    inputmode="decimal"
                                    autocomplete="off"
                                    <% if not $CreateFormChecked(EnableSpendLimit) %>disabled="disabled"<% end_if %>
                                >
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 m-b25">
                        <div class="form-group m-b5">
                            <div class="custom-control custom-checkbox">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="CreateIsAdmin"
                                    name="IsAdmin"
                                    value="1"
                                    <% if $CreateFormChecked(IsAdmin) %>checked<% end_if %>
                                >
                                <label class="form-check-label" for="CreateIsAdmin">Account Admin</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="CreateRequiresApproval"
                                    name="RequiresApproval"
                                    value="1"
                                    <% if $CreateFormChecked(RequiresApproval) %>checked<% end_if %>
                                >
                                <label class="form-check-label" for="CreateRequiresApproval">Requires Approval On Orders</label>
                            </div>
                        </div>
                        
                    </div>

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-secondary">Create User</button>
                    </div>
                </form>
            </div>

            <%-- Edit user --%>
            <div class="col-xl-6">
                <h4 class="title m-b15">Edit User</h4>

                <div class="form-group m-b25">
                    <label class="label-title">Select User</label>
                    <div class="form-select">
                        <select id="manage-user-select" class="default-select w-100">
                            <option value="">Select a user</option>
                            <% loop $ManagedUsers %>
                                <option value="$ID" <% if $Top.EditUserID = $ID %>selected="selected"<% end_if %>>
                                    $FirstName $Surname - $Email
                                    <% if $ID = $Top.CurrentMember.ID %>
                                        (currently logged in)
                                    <% end_if %>
                                </option>
                            <% end_loop %>
                        </select>
                    </div>
                </div>

                <div id="manage-user-edit-forms">
                    <% if $ManagedUsers %>
                        <% loop $ManagedUsers %>
                            <div class="manage-user-edit-card" data-user-id="$ID" style="display:none;">
                                <div class="card dz-card style-2 p-4">
                                    <h5 class="title m-b20">$FirstName $Surname</h5>

                                    <form class="row" method="post" action="$Top.Link(updateUser)/$ID">
                                        <input type="hidden" name="SecurityID" value="$Top.SecurityTokenValue.XML">

                                        <div class="col-md-6">
                                            <div class="form-group m-b25">
                                                <label class="label-title">First Name</label>
                                                <input name="FirstName" class="form-control" value="$FirstName" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group m-b25">
                                                <label class="label-title">Surname</label>
                                                <input name="Surname" class="form-control" value="$Surname" required>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group m-b25">
                                                <label class="label-title">Email Address</label>
                                                <input name="Email" type="email" class="form-control" value="$Email" required>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group m-b25">
                                                <div class="custom-control custom-checkbox">
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input"
                                                        id="Active_$ID"
                                                        name="Active"
                                                        value="1"
                                                        <% if $Active %>checked<% end_if %>
                                                    >
                                                    <label class="form-check-label" for="Active_$ID">Active</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="m-b25">
                                                <label class="label-title">
                                                    Role
                                                    <i 
                                                        class="fa fa-info-circle ms-1 text-muted"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Roles only apply to non admin account users. Account admins can see all products regardless of role."
                                                        style="cursor:pointer;"
                                                    ></i>
                                                </label>

                                                <div class="form-select">
                                                    <select name="RoleID" class="default-select w-100" <% if IsAdmin %>disabled<% end_if %>>
                                                        <option value="">Select role</option>
                                                        <% loop $Top.AvailableRoles %>
                                                            <option value="$ID" <% if $Up.RoleID = $ID %>selected<% end_if %>>$Title</option>
                                                        <% end_loop %>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group m-b25">
                                                <div class="custom-control custom-checkbox">
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input"
                                                        id="IsAdmin_$ID"
                                                        name="IsAdmin"
                                                        value="1"
                                                        <% if $IsAdmin %>checked<% end_if %>
                                                    >
                                                    <label class="form-check-label" for="IsAdmin_$ID">Account Admin</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group m-b25 spend-limit-wrap">
                                                <div class="custom-control custom-checkbox m-b10">
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input spend-limit-toggle"
                                                        id="EnableSpendLimit_$ID"
                                                        name="EnableSpendLimit"
                                                        value="1"
                                                        <% if $EnableSpendLimit %>checked<% end_if %>
                                                    >
                                                    <label class="form-check-label" for="EnableSpendLimit_$ID">Enable Spend Limit</label>
                                                </div>

                                                <div class="input-group">
                                                    <span class="input-group-text">£</span>
                                                    <input
                                                        name="SpendLimit"
                                                        type="text"
                                                        class="form-control spend-limit-input"
                                                        value="$SpendLimit"
                                                        placeholder="0.00"
                                                        inputmode="decimal"
                                                        autocomplete="off"
                                                        <% if not $EnableSpendLimit %>disabled="disabled"<% end_if %>
                                                    >
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group m-b25">
                                                <div class="custom-control custom-checkbox">
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input"
                                                        id="RequiresApproval_$ID"
                                                        name="RequiresApproval"
                                                        value="1"
                                                        <% if $RequiresApproval %>checked<% end_if %>
                                                    >
                                                    <label class="form-check-label" for="RequiresApproval_$ID">Requires Approval On Orders</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-secondary w-100">Save Changes</button>
                                        </div>
                                    </form>

                                    <form method="post" action="$Top.Link(deleteUser)/$ID" onsubmit="return confirm('Delete this user?');" class="m-t15">
                                        <input type="hidden" name="SecurityID" value="$Top.SecurityTokenValue.XML">
                                        <button type="submit" class="btn btn-danger w-100">Delete User</button>
                                    </form>
                                </div>
                            </div>
                        <% end_loop %>
                    <% else %>
                        <p>No users found.</p>
                    <% end_if %>
                </div>
            </div>

        </div>
    </div>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var userSelect = document.getElementById('manage-user-select');
            var userCards = document.querySelectorAll('.manage-user-edit-card');

            function toggleUserCard() {
                var selectedId = userSelect ? userSelect.value : '';

                userCards.forEach(function(card) {
                    card.style.display = (selectedId && card.getAttribute('data-user-id') === selectedId) ? '' : 'none';
                });
            }

            if (userSelect) {
                userSelect.addEventListener('change', toggleUserCard);
                toggleUserCard();
            }

            document.querySelectorAll('.spend-limit-toggle').forEach(function (checkbox) {
                function syncSpendLimit() {
                    var wrap = checkbox.closest('.spend-limit-wrap');
                    if (!wrap) {
                        return;
                    }

                    var input = wrap.querySelector('.spend-limit-input');
                    if (!input) {
                        return;
                    }

                    input.disabled = !checkbox.checked;
                    input.setCustomValidity('');
                }

                checkbox.addEventListener('change', syncSpendLimit);
                syncSpendLimit();
            });

            document.querySelectorAll('.spend-limit-input').forEach(function (input) {
                input.addEventListener('input', function () {
                    var value = input.value;

                    value = value.replace(/[^0-9.]/g, '');

                    var parts = value.split('.');
                    if (parts.length > 2) {
                        value = parts[0] + '.' + parts.slice(1).join('');
                        parts = value.split('.');
                    }

                    if (parts[1] !== undefined) {
                        parts[1] = parts[1].substring(0, 2);
                        value = parts[0] + '.' + parts[1];
                    }

                    input.value = value;
                    input.setCustomValidity('');
                });

                input.addEventListener('blur', function () {
                    var value = input.value.trim();

                    if (value === '') {
                        input.setCustomValidity('');
                        return;
                    }

                    var number = parseFloat(value);
                    if (!isNaN(number)) {
                        input.value = number.toFixed(2);
                    }

                    input.setCustomValidity('');
                });
            });

            document.querySelectorAll('form').forEach(function (form) {
                form.addEventListener('submit', function () {
                    var spendLimitInput = form.querySelector('.spend-limit-input');

                    if (!spendLimitInput || spendLimitInput.disabled) {
                        return;
                    }

                    var value = spendLimitInput.value.trim();

                    if (value === '') {
                        spendLimitInput.value = '';
                        spendLimitInput.setCustomValidity('');
                        return;
                    }

                    value = value.replace(/[^0-9.]/g, '');

                    var parts = value.split('.');
                    if (parts.length > 2) {
                        value = parts[0] + '.' + parts.slice(1).join('');
                    }

                    var number = parseFloat(value);

                    if (!isNaN(number)) {
                        spendLimitInput.value = number.toFixed(2);
                    } else {
                        spendLimitInput.value = '';
                    }

                    spendLimitInput.setCustomValidity('');
                });
            });
        });
    </script>

<% include FooterInfoBox %>