<% with Item %>
	<div class="page-content">
		
		<div class="d-sm-flex justify-content-between container-fluid py-3">
			<nav aria-label="breadcrumb" class="breadcrumb-row">
				<ul class="breadcrumb mb-0">
					<li class="breadcrumb-item"><a href="/"> Home</a></li>
					<li class="breadcrumb-item">$Title</li>
				</ul>
			</nav>
		</div>
		
		<form method="post" action="/home/addToCart" id="product-add-to-cart-form">
			<%-- $Top.SecurityID --%>
			
			<input type="hidden" name="ProductID" value="$ID">
			<input type="hidden" name="ProductTitle" value="$Title.XML">
			<input type="hidden" name="ProductSKU" value="$SKU.XML">
			<input type="hidden" name="ProductPrice" value="$Price">

			<section class="content-inner py-0">
				<div class="container-fluid">
					<div class="row">
	                    <%-- Gallery --%>
						<div class="col-xl-4 col-md-4">
							<div class="dz-product-detail sticky-top">
								<div class="swiper-btn-center-lr">
									<div class="swiper product-gallery-swiper2">
										<div class="swiper-wrapper" id="lightgallery">
											<% if SortedImages %>
												<% loop SortedImages %>
													<div class="swiper-slide">
														<div class="dz-media DZoomImage">
															<a class="mfp-link lg-item" href="$URL" data-src="$URL">
																<i class="feather icon-maximize dz-maximize top-left"></i>
															</a>
															<img src="$URL" alt="image">
														</div>
													</div>
												<% end_loop %>											
											<% else %>
												<div class="swiper-slide">
													<div class="dz-media DZoomImage">
														<a class="mfp-link lg-item" href="$themedResourceURL('images/500x500.jpg')" data-src="$themedResourceURL('images/500x500.jpg')">
															<i class="feather icon-maximize dz-maximize top-left"></i>
														</a>
														<img src="$themedResourceURL('images/500x500.jpg')" alt="image">
													</div>
												</div>
												<div class="swiper-slide">
													<div class="dz-media DZoomImage">
														<a class="mfp-link lg-item" href="$themedResourceURL('images/500x500.jpg')" data-src="$themedResourceURL('images/500x500.jpg')">
															<i class="feather icon-maximize dz-maximize top-left"></i>
														</a>
														<img src="$themedResourceURL('images/500x500.jpg')" alt="image">
													</div>
												</div>																								
											<% end_if %>

										</div>
									</div>
									<div class="swiper product-gallery-swiper thumb-swiper-lg">
										<div class="swiper-wrapper">
	                                        <% loop SortedImages %>
	                                            <div class="swiper-slide">
	                                                <img src="$URL" alt="image">
	                                            </div>
	                                        <% end_loop %>
										</div>
									</div>
								</div>							
							</div>	
						</div>

						<div class="col-xl-8 col-md-8">
							<div class="row">
								<div class="col-xl-7">
									<div class="dz-product-detail style-2 p-t20 ps-0">
										<div class="dz-content">
	                                        <%-- Title --%>
											<div class="dz-content-footer">
												<div class="dz-content-start">
													<h4 class="title mb-1"><a href="/">$Title</a></h4>
												</div>
											</div>

	                                        <%-- Description --%>
											<p class="para-text">
												$Description
											</p>

	                                        <%-- Price  --%>
											<div class="meta-content m-b20 d-flex align-items-end">
												<div class="me-3">
													<span class="price-name">Price</span>
													<span class="price-num">$Price.Nice</span>
												</div>
											</div>

	                                        <div id="product-options-message" class="alert alert-danger m-b20" style="display:none;"></div>

	                                        <%-- QTY --%>
											<div class="product-num">
												<div class="btn-quantity light d-xl-block">
													<label class="form-label">Quantity</label>
													<input type="text" value="1" name="Quantity">
												</div>
											</div>

	                                        <%-- Options --%>
	                                        <div class="product-num">

	                                            <% loop $GroupedAttributeOptions %>

	                                                <% if $Attribute.Type == 'square_radio' %>
	                                                    <div
	                                                        class="d-block product-option-group square-radio m-b20"
	                                                        data-validation-label="$Attribute.Title.XML"
	                                                        <% if $Attribute.Required %>data-required="true"<% end_if %>
	                                                    >
	                                                        <label class="form-label">$Attribute.Title</label>

	                                                        <div class="btn-group product-size mb-0">
	                                                            <% loop $Options %>
	                                                                <input
	                                                                    type="radio"
	                                                                    class="btn-check"
	                                                                    name="attr_{$AttributeID}"
	                                                                    id="attr_{$AttributeID}_{$ID}"
	                                                                    value="$Value"
	                                                                >
	                                                                <label class="btn btn-light" for="attr_{$AttributeID}_{$ID}">
	                                                                    <% if $SquareLabel %>$SquareLabel<% else %>$Title<% end_if %>
	                                                                </label>
	                                                            <% end_loop %>
	                                                        </div>
	                                                    </div>
	                                                <% end_if %>

	                                                <% if $Attribute.Type == 'colour_radio' %>
	                                                    <div
	                                                        class="meta-content product-option-group colour-radio m-b20"
	                                                        data-validation-label="$Attribute.Title.XML"
	                                                        <% if $Attribute.Required %>data-required="true"<% end_if %>
	                                                    >
	                                                        <label class="form-label">$Attribute.Title</label>

	                                                        <div class="d-flex align-items-center color-filter">
	                                                            <% loop $Options %>
	                                                                <div class="form-check">
	                                                                    <input
	                                                                        class="form-check-input"
	                                                                        type="radio"
	                                                                        name="attr_{$AttributeID}"
	                                                                        id="attr_{$AttributeID}_{$ID}"
	                                                                        value="$Value"
	                                                                    >
	                                                                    <span style="background-color: {$HexColour};"></span>
	                                                                </div>
	                                                            <% end_loop %>
	                                                        </div>
	                                                    </div>
	                                                <% end_if %>

	                                            <% end_loop %>

	                                            <% loop $Attributes %>
	                                                <% if $Type == 'text_input' %>
	                                                    <div
	                                                        class="d-block product-option-group m-b20"
	                                                        data-validation-label="$Title.XML"
	                                                        <% if $Required %>data-required="true"<% end_if %>
	                                                    >
	                                                        <label class="form-label">$Title</label>
	                                                        <div class="btn-group product-size mb-0">
	                                                            <input
	                                                                type="text"
	                                                                class="form-control"
	                                                                name="attr_{$ID}"
	                                                                placeholder="$Placeholder"
	                                                                <% if $MaxLength %>maxlength="$MaxLength"<% end_if %>
	                                                                style="max-height:34px;"
	                                                            >
	                                                        </div>
	                                                    </div>
	                                                <% end_if %>
	                                            <% end_loop %>

	                                        </div>

											<div class="dz-info">
												<ul>
													<li>
														<strong>SKU:</strong>
														<span>$SKU</span>
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>

								<div class="col-xl-5">
									<div class="cart-detail">
										<table>
											<tbody>
												<tr class="total">
													<td>
														<h6 class="mb-0">Total</h6>
													</td>
													<td class="price">
														$Price.Nice
													</td>
												</tr>
											</tbody>
										</table>
										<% if $Top.CartMessage %>
											<div class="alert alert-info">$Top.CartMessage</div>
										<% end_if %>  										
										<button type="submit" class="btn btn-secondary w-100">ADD TO CART</button>
									</div>	
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</form>
	
		<% include FooterInfoBox %>

	</div>

	<script>
	document.addEventListener('DOMContentLoaded', function () {
	    var form = document.getElementById('product-add-to-cart-form');
	    var messageBox = document.getElementById('product-options-message');

	    if (!form || !messageBox) {
	        return;
	    }

	    function groupIsValid(group) {
	        if (group.getAttribute('data-required') !== 'true') {
	            return true;
	        }

	        var radioInputs = group.querySelectorAll('input[type="radio"]');
	        if (radioInputs.length) {
	            for (var i = 0; i < radioInputs.length; i++) {
	                if (radioInputs[i].checked) {
	                    return true;
	                }
	            }
	            return false;
	        }

	        var textInput = group.querySelector('input[type="text"]');
	        if (textInput) {
	            return textInput.value.trim() !== '';
	        }

	        return true;
	    }

	    function clearErrors() {
	        var groups = form.querySelectorAll('.product-option-group');
	        groups.forEach(function (group) {
	            group.classList.remove('option-error');
	        });

	        messageBox.style.display = 'none';
	        messageBox.textContent = '';
	    }

	    function validateOptions() {
	        var requiredGroups = form.querySelectorAll('.product-option-group[data-required="true"]');
	        var missing = [];

	        requiredGroups.forEach(function (group) {
	            group.classList.remove('option-error');

	            if (!groupIsValid(group)) {
	                group.classList.add('option-error');
	                missing.push(group.getAttribute('data-validation-label') || 'Required option');
	            }
	        });

	        if (missing.length) {
	            messageBox.textContent = 'Please select: ' + missing.join(', ') + '.';
	            messageBox.style.display = '';
	            return false;
	        }

	        messageBox.style.display = 'none';
	        messageBox.textContent = '';
	        return true;
	    }

	    form.addEventListener('submit', function (e) {
	        if (!validateOptions()) {
	            e.preventDefault();
	            window.scrollTo({
	                top: messageBox.getBoundingClientRect().top + window.pageYOffset - 120,
	                behavior: 'smooth'
	            });
	        }
	    });

	    form.querySelectorAll('.product-option-group input').forEach(function (input) {
	        input.addEventListener('change', function () {
	            if (messageBox.style.display !== 'none') {
	                validateOptions();
	            }
	        });

	        input.addEventListener('input', function () {
	            if (messageBox.style.display !== 'none') {
	                validateOptions();
	            }
	        });
	    });
	});
	</script>

	<style>
	.option-error .form-label {
	    color: #dc3545;
	}

	.option-error .btn,
	.option-error .form-control {
	    border-color: #dc3545;
	}
	</style>
<% end_with %>