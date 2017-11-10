<?php
/*
 * Plugin Name: 	Gravity Forms + WCP Taxonomies Term Names
 * Plugin URI: 		https://www.netseek.com.au/
 * Description: 	Display WCP Taxonomy term name instead of id's on summary page.
 * Version: 		1.0.0.0
 * Author: 			Netseek Pty Ltd
 * Author URI: 		https://www.netseek.com.au/
 * License:    		GPL2
 * License URI:		https://www.gnu.org/licenses/gpl-2.0.html
 */

function gfwcp_taxonomy_names_enqueue(){
	add_action('wp_print_footer_scripts', 'gfwcp_taxonomy_names_footer_scripts' );
}
add_action( 'wp_enqueue_scripts', 'gfwcp_taxonomy_names_enqueue');

function gfwcp_taxonomy_names_footer_scripts(){
	?>
	<script type="text/javascript">
	jQuery(document).bind('gform_post_render', function() {
		<?php
		$taxonomies = array( 'ndf_category_1', 'ndf_category_2', 'ndf_category_3', 'ndf_category_4', 'ndf_category_5' );
		foreach( $taxonomies as $taxonomy ){
			$terms = get_terms( $taxonomy );
			
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
	//]]>
	</script>
   <?php
}