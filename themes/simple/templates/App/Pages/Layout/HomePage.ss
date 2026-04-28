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

   <% if Categories %>
      <div class="container pb-4">
         <div class="d-flex align-items-center justify-content-between m-b30">
            <h6 class="title mb-0 fw-normal">
               <svg class="me-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 25" width="20" height="20"><g id="Layer_30" data-name="Layer 30"><path d="M2.54,5H15v.5A1.5,1.5,0,0,0,16.5,7h2A1.5,1.5,0,0,0,20,5.5V5h2.33a.5.5,0,0,0,0-1H20V3.5A1.5,1.5,0,0,0,18.5,2h-2A1.5,1.5,0,0,0,15,3.5V4H2.54a.5.5,0,0,0,0,1ZM16,3.5a.5.5,0,0,1,.5-.5h2a.5.5,0,0,1,.5.5v2a.5.5,0,0,1-.5.5h-2a.5.5,0,0,1-.5-.5Z"></path><path d="M22.4,20H18v-.5A1.5,1.5,0,0,0,16.5,18h-2A1.5,1.5,0,0,0,13,19.5V20H2.55a.5.5,0,0,0,0,1H13v.5A1.5,1.5,0,0,0,14.5,23h2A1.5,1.5,0,0,0,18,21.5V21h4.4a.5.5,0,0,0,0-1ZM17,21.5a.5.5,0,0,1-.5.5h-2a.5.5,0,0,1-.5-.5v-2a.5.5,0,0,1,.5-.5h2a.5.5,0,0,1,.5.5Z"></path><path d="M8.5,15h2A1.5,1.5,0,0,0,12,13.5V13H22.45a.5.5,0,1,0,0-1H12v-.5A1.5,1.5,0,0,0,10.5,10h-2A1.5,1.5,0,0,0,7,11.5V12H2.6a.5.5,0,1,0,0,1H7v.5A1.5,1.5,0,0,0,8.5,15ZM8,11.5a.5.5,0,0,1,.5-.5h2a.5.5,0,0,1,.5.5v2a.5.5,0,0,1-.5.5h-2a.5.5,0,0,1-.5-.5Z"></path></g></svg>
               Category Filter
            </h6>
         </div>      
         <div class="btn-group product-size" id="product-category-tabs">
            <input type="radio" class="btn-check" name="product-category-filter" id="product-category-filter-all" data-category-filter="all" checked>
            <label class="btn" for="product-category-filter-all">All</label>

            <% loop Categories %>
               <input type="radio" class="btn-check" name="product-category-filter" id="product-category-filter-$ID" data-category-filter="$ID">
               <label class="btn" for="product-category-filter-$ID">$Title</label>
            <% end_loop %>
         </div>
      </div>
   <% end_if %>

   <div class="container">
      <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 m-auto gx-xl-4 g-3 mb-xl-0 mb-md-0 mb-3" id="product-grid">
         <% loop VisibleProducts %>
            <div class="col m-md-b15 m-sm-b0 m-b30 product-pagination-item" data-category-ids="<% loop Categories %>$ID <% end_loop %>">
               <div class="shop-card">
                  <a href="$Link" class="dz-media">
                     <img src="$FeaturedImage.URL" alt="image" style="max-height: 210px;object-fit: contain;">			
                  </a>

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
         var itemsPerClick = 15;
         var items = document.querySelectorAll('#product-grid .product-pagination-item');
         var filteredItems = Array.prototype.slice.call(items);
         var totalItems = filteredItems.length;
         var visibleItems = 0;

         var tabs = document.querySelectorAll('#product-category-tabs input[data-category-filter]');
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
               filteredItems[i].style.display = '';
            }

            visibleItems = Math.min(nextVisibleCount, totalItems);
            updateStatus();
         }

         function itemHasCategory(item, categoryId) {
            var categoryIds = (item.getAttribute('data-category-ids') || '').trim().split(/\s+/);

            return categoryIds.indexOf(categoryId) !== -1;
         }

         function applyCategoryFilter(categoryId) {
            filteredItems = Array.prototype.filter.call(items, function (item) {
               return categoryId === 'all' || itemHasCategory(item, categoryId);
            });

            totalItems = filteredItems.length;
            visibleItems = 0;

            for (var i = 0; i < items.length; i++) {
               items[i].style.display = 'none';
            }

            showMoreItems();
         }

         for (var i = 0; i < items.length; i++) {
            items[i].style.display = 'none';
         }

         showMoreItems();

         for (var j = 0; j < tabs.length; j++) {
            tabs[j].addEventListener('change', function () {
               applyCategoryFilter(this.getAttribute('data-category-filter'));
            });
         }

         if (loadMoreButton) {
            loadMoreButton.addEventListener('click', function (e) {
               e.preventDefault();
               showMoreItems();
            });
         }
      });
   </script>
