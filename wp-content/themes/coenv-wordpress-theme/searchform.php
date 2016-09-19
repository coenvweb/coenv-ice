<form role="search" method="get" class="search-form" action="<?php echo home_url( '/' ); ?>">
  <div class="field-wrap">
    <input type="text" value="<?php echo get_search_query() ?>" name="s" id="s" placeholder="Search this site" />
    <button type="submit"><i class="icon-search"></i><span>Search</span></button>
  </div>
</form>