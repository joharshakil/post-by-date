<div class="pbd-wrap">
	<h1>Posts By Date</h1>
</div>

<?php 
if ( isset( $_POST['pbd-submit'] ) && isset( $_POST['pbd-security'] ) && wp_verify_nonce( sanitize_text_field( $_POST['pbd-security'] ), 'pbd-security' ) ) {
	$pbd_Settings = array(
		'pbd-post-category' => isset( $_POST['pbd-post-category'] ) ? sanitize_text_field( $_POST['pbd-post-category'] ) : '',
		'pbd-post-date' => isset( $_POST['pbd-post-date'] ) ? sanitize_text_field( $_POST['pbd-post-date'] ) : '',
		'pbd-post-limit' => ( isset( $_POST['pbd-post-limit'] ) && ! empty( sanitize_text_field( $_POST['pbd-post-limit'] ) ) ) ? sanitize_text_field( $_POST['pbd-post-limit'] ) : 5
	);

	foreach ( $pbd_Settings as $key => $setting ) {
		update_option( $key, $setting );
	}

	echo esc_html__('setting saved successfully', 'pbd');
} else {
	$pbd_Settings = array(
		'pbd-post-category' => ! empty( get_option('pbd-post-category') ) ? get_option('pbd-post-category') : '',
		'pbd-post-date' => ! empty( get_option('pbd-post-date') ) ? get_option('pbd-post-date') : '',
		'pbd-post-limit' => ! empty( get_option('pbd-post-limit') ) ? get_option('pbd-post-limit') : 5
	);
}
?>

<form action="#" method="POST">

	<input type="hidden" name="pbd-security" value="<?php echo wp_create_nonce( 'pbd-security' ); ?>">

	<label for="pbd-post-category">
		<strong><?php echo esc_html__( 'Post Category', 'pbd' ); ?></strong>
		<select name="pbd-post-category" id="pbd-post-category">
			<option value=""><?php echo esc_html__( '--Select Category--', 'pbd' ); ?></option>
			<?php 
			$categories = get_categories( array(
			    'orderby' => 'name',
			    'order'   => 'ASC',
			    'taxonomy' => 'category',
			    'hide_empty' => true,		    
			));

			if ( count( $categories ) > 0 ) {
				foreach ( $categories as $category ) {
					echo sprintf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $category->term_id ), selected( $pbd_Settings['pbd-post-category'], esc_attr( $category->term_id ), false ), esc_attr( $category->name ) );
				}
			}
			?>
		</select>
	</label><br><br>

	<label for="pbd-post-date">
		<strong><?php echo esc_html__( 'Post Date', 'pbd' ); ?></strong>
		<input type="date" id="pbd-post-date" name="pbd-post-date" value="<?php echo esc_attr( $pbd_Settings['pbd-post-date'] ); ?>">
	</label><br><br>

	<label for="pbd-post-limit">
		<strong><?php echo esc_html__( 'Post Limit', 'pbd' ); ?></strong>
		<input type="number" id="pbd-post-limit" name="pbd-post-limit" value="<?php echo esc_attr( $pbd_Settings['pbd-post-limit'] ); ?>">
	</label><br><br>

	<input type="submit" name="pbd-submit" value="<?php echo esc_html__( 'Save Changes', 'pbd' ); ?>">

</form>
