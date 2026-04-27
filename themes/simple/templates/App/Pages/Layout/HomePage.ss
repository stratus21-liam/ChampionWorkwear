<!--banner-->
<div class="dz-bnr-inr style-1" style="background-image:url( <% if SiteConfig.BannerImage %> $SiteConfig.BannerImage.URL <% else %>$themedResourceURL('images/background/bg-shape.jpg') <% end_if %>);">
   <div class="container">
      <div class="dz-bnr-inr-entry">
         <% if $CurrentMember.CustomerAccount.StoreTitle %>
            <h1>$CurrentMember.CustomerAccount.StoreTitle</h1>
         <% else %>
            <h1>Home</h1>
         <% end_if %>
      </div>
   </div>
</div>

<% with CurrentMember.CustomerAccount %>
<section class="content-inner-1 z-index-unset">
   <div class="container">
      <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 m-auto gx-xl-4 g-3 mb-xl-0 mb-md-0 mb-3" id="product-grid">
         <% loop VisibleProducts %>
            <div class="col m-md-b15 m-sm-b0 m-b30 product-pagination-item">
               <div class="shop-card">
                  <div class="dz-media">
                     <img src="$FeaturedImage.URL" alt="image" style="max-height: 225px;object-fit: contain;">			
                     <div class="shop-meta">
                        <% if Active %>
                           <form method="post" action="$Top.Link(addToCart)" class="d-inline">
                              <input type="hidden" name="ProductID" value="$ID">
                              <input type="hidden" name="Quantity" value="1">

                              <button type="submit" class="btn btn-primary meta-icon dz-carticon">
                                 <svg class="dz-cart-check" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.9144 3.73438L5.49772 10.151L2.58105 7.23438" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                 </svg>

                                 <svg class="dz-cart-out" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.6033 10.4092C9.70413 10.4083 8.97452 11.1365 8.97363 12.0357C8.97274 12.9348 9.70097 13.6644 10.6001 13.6653C11.4993 13.6662 12.2289 12.938 12.2298 12.0388C12.2298 12.0383 12.2298 12.0378 12.2298 12.0373C12.2289 11.1391 11.5014 10.4109 10.6033 10.4092Z" fill="white"/>
                                    <path d="M13.4912 2.6132C13.4523 2.60565 13.4127 2.60182 13.373 2.60176H3.46022L3.30322 1.55144C3.20541 0.853911 2.60876 0.334931 1.90439 0.334717H0.627988C0.281154 0.334717 0 0.61587 0 0.962705C0 1.30954 0.281154 1.59069 0.627988 1.59069H1.90595C1.9858 1.59011 2.05338 1.64957 2.06295 1.72886L3.03004 8.35727C3.16263 9.19953 3.88712 9.8209 4.73975 9.82363H11.2724C12.0933 9.8247 12.8015 9.24777 12.9664 8.44362L13.9884 3.34906C14.0543 3.00854 13.8317 2.67909 13.4912 2.6132Z" fill="white"/>
                                    <path d="M6.61539 11.9676C6.57716 11.0948 5.85687 10.4077 4.98324 10.4108C4.08483 10.4471 3.38595 11.2048 3.42225 12.1032C3.45708 12.9653 4.15833 13.6505 5.02092 13.6653H5.06017C5.95846 13.626 6.65474 12.8658 6.61539 11.9676Z" fill="white"/>
                                    <clipPath id="clip0_502_36">
                                       <rect width="14" height="14" fill="white"/>
                                    </clipPath>
                                 </svg>
                              </button>
                           </form>
                        <% end_if %>
                     </div>	
                  </div>

                  <div class="dz-content">
                     <h5 class="title">
                        <a href="$Link">$Title</a>
                     </h5>
                     <h6 class="price">
                        $Price.Nice
                     </h6>
                  </div>
               </div>	
            </div>
         <% end_loop %>
      </div>

      <div class="row page mt-0 align-items-center">
         <div class="col-md-6">
            <p class="page-text mb-md-0" id="product-pagination-status">
               Showing 0 of 0 Results
            </p>
         </div>

         <div class="col-md-6 text-md-end">
            <nav aria-label="Product Pagination">
               <ul class="pagination style-1 justify-content-md-end justify-content-start mb-0">
                  <li class="page-item" id="load-more-products-wrap">
                     <a class="page-link next" href="javascript:void(0);" id="load-more-products">
                        Load More
                     </a>
                  </li>
               </ul>
            </nav>
         </div>
      </div>
   </div>
</section>
<% end_with %>

<% include FooterInfoBox %>


   <script>
      document.addEventListener('DOMContentLoaded', function () {
         var itemsPerClick = 12;
         var items = document.querySelectorAll('#product-grid .product-pagination-item');
         var totalItems = items.length;
         var visibleItems = 0;

         var status = document.getElementById('product-pagination-status');
         var loadMoreWrap = document.getElementById('load-more-products-wrap');
         var loadMoreButton = document.getElementById('load-more-products');

         function updateStatus() {
            if (status) {
                  status.textContent = 'Showing ' + visibleItems + ' of ' + totalItems + ' Results';
            }

            if (loadMoreWrap) {
                  loadMoreWrap.style.display = visibleItems >= totalItems ? 'none' : '';
            }
         }

         function showMoreItems() {
            var nextVisibleCount = visibleItems + itemsPerClick;

            for (var i = visibleItems; i < nextVisibleCount && i < totalItems; i++) {
                  items[i].style.display = '';
            }

            visibleItems = Math.min(nextVisibleCount, totalItems);
            updateStatus();
         }

         for (var i = 0; i < totalItems; i++) {
            items[i].style.display = 'none';
         }

         showMoreItems();

         if (loadMoreButton) {
            loadMoreButton.addEventListener('click', function (e) {
                  e.preventDefault();
                  showMoreItems();
            });
         }
      });
   </script>