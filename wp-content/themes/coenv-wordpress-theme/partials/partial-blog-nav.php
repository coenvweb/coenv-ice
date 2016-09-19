<?php
$ancestor_id = coenv_get_ancestor();

$ancestor = array(
	'id' => $ancestor_id,
	'permalink' => get_permalink( $ancestor_id ),
	'title' => get_the_title( $ancestor_id )
);

?>


<ul id="menu-secondary" class="menu">
		<!--<li class="pagenav"><a href="#">News</a></li>-->
    <?php wp_list_pages( array(
    		'child_of' => $ancestor['id'],
        'depth' => 3,
        'title_li' => '<a href="' . $ancestor['permalink'] . '">' . $ancestor['title'] . '</a>',
        'link_after' => '<i class="icon-arrow-right"></i>',
        'walker' => new CoEnv_Secondary_Menu_Walker,
        'sort_column' => 'menu_order'
    ) ) ?>
</ul>