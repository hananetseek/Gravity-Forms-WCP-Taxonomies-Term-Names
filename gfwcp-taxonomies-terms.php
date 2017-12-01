<?php
/*
 * Plugin Name: 	Gravity Forms + WCP Taxonomies Term Names
 * Plugin URI: 		https://www.netseek.com.au/
 * Description: 	Display WCP Taxonomy term name instead of id's on summary page.
 * Version: 		1.0.0.2
 * Author: 			Netseek Pty Ltd
 * Author URI: 		https://www.netseek.com.au/
 * License:    		GPL2
 * License URI:		https://www.gnu.org/licenses/gpl-2.0.html
 */

function gfwcp_taxonomy_names_enqueue(){
    if ( wcp_has_gform() === true ) {
	    add_action('wp_print_footer_scripts', 'gfwcp_taxonomy_names_footer_scripts' );
    }
    add_action('wp_print_footer_scripts', 'gfwcp_taxonomy_names_ajax_footer_scripts' );
}
add_action( 'wp_enqueue_scripts', 'gfwcp_taxonomy_names_enqueue');

function gfwcp_taxonomy_names_footer_scripts(){
	/*
	?>
	<script type="text/javascript">
	jQuery(document).bind('gform_post_render', function() {
		<?php
		$taxonomies = array( 'ndf_category_1', 'ndf_category_2', 'ndf_category_3', 'ndf_category_4', 'ndf_category_5' );
		foreach( $taxonomies as $taxonomy ){
			$terms = get_terms( array( $taxonomy, 'hide_empty' => false ) );
			
			foreach ( $terms as $term ){
				?>
				jQuery(".bulleted li").filter(function() {
					return $(this).html() === "<?php echo $term->term_id; ?>";
				}).html("<?php echo $term->name; ?>");
				<?php
			}
		}
		?>
	});
	</script>
   <?php
   */
}
function gfwcp_taxonomy_names_ajax_footer_scripts(){
	?>
	<script type="text/javascript">
	var gform_exists = '';
	jQuery(document).bind('gform_post_render', function() {
		if( $('.gform_wrapper').length ){
			$.ajax({
				type : "post",
				dataType : "text",
				url : ndf_data_filter_vars.ndf_ajax,
				data : {
					action: "gfwcp_taxonomies_terms_call", 
					security : ndf_data_filter_vars.ndf_nonce
				},
				beforeSend: function () {
					console.log('get script');
	            },
				success: function(response) {
					console.log('response:');
					console.log(response);
					$('body').append(response);
				}
			});
		}
	});
	</script>
   <?php
}
function gfwcp_taxonomies_terms_request(){
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		//$nonce = $_POST['security'];

		//if ( empty($_POST) || !wp_verify_nonce( $nonce, 'ndf-nonce' ) ) die('An error occurred. Please contact the Administrator.');

		?>
		<script type="text/javascript">
		jQuery(document).bind('gform_post_render', function() {
			<?php
			$taxonomies = array( 'ndf_category_1', 'ndf_category_2', 'ndf_category_3', 'ndf_category_4', 'ndf_category_5' );
			foreach( $taxonomies as $taxonomy ){
				$terms = get_terms( array( $taxonomy, 'hide_empty' => false ) );
				
				foreach ( $terms as $term ){
					?>
					jQuery(".bulleted li").filter(function() {
						return $(this).html() === "<?php echo $term->term_id; ?>";
					}).html("<?php echo $term->name; ?>");
					<?php
				}
			}
			?>
		});
		</script>
		<?php
	}
	?>
	<?php
	die();
}
add_action( 'wp_ajax_nopriv_gfwcp_taxonomies_terms_call', 'gfwcp_taxonomies_terms_request' );
add_action( 'wp_ajax_gfwcp_taxonomies_terms_call', 'gfwcp_taxonomies_terms_request' );

function wcp_has_gform() {
    global $post;
	if( $post ){
		$all_content = $post->post_content;
		if (strpos($all_content,'[gravityform') !== false) {
			$data = preg_replace_callback("/\[(\w+) (.+?)]/", "wcp_get_gf_id", $all_content);

			$form_id = $data;
			$form = RGFormsModel::get_form_meta($form_id);
			
			if( is_array( $form ) ){
				if( array_key_exists( 'wcp_more_info_settings', $form ) ){
					if( $form['wcp_more_info_settings'] == 'Yes' ){
						return true;
					}
				}
			}
		}
	}
    return false;
}

function wcp_get_gf_id( $matches ){
	$dat= explode(" ", $matches[2]);
	$params = '';
	foreach ($dat as $d){
		list($opt, $val) = explode("=", $d);
		if( $opt == 'id' ){
			$params = trim($val, '"');
		}
	}
	switch($matches[1]){
		case "gravityform":
			return $params;        
	}
}
