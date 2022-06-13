<ol id="pbd-posts">
	
</ol>
<?php
$date = explode( '-', $post_date );
if ( isset( $date[0] ) && isset( $date[1] ) && isset( $date[2] ) ) {
    $date_array = array(
        array(
            'year'  => $date[0],
            'month' => $date[1],
            'day'   => $date[2],
        ),
    );
}

$args = array(    
    'post_type' => 'post',
    'posts_per_page' => -1,
    'cat'    => $post_category,        
    'date_query' => @$date_array,
);

$loop = new WP_Query( $args );

if ( $loop->found_posts > $post_limit ) {
	?>
	<a href="#" id="more_posts"><?php echo esc_html__('Load More', 'pbd'); ?></a>
	<?php
}
