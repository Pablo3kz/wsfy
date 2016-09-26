<div class="search-wrapper">
    <form id="ajax-search-form" class="ajax-search-form clear" action="<?php echo esc_url( get_home_url() );?>" method="get">
        <p class="search-input">
        <input type="text" name="s" id="keyword" value="<?php the_search_query(); ?>" />
        </p>
        <button class="search-button"><i class="fa fa-search"></i></button>
    </form>
</div>