<?php
/*
Plugin Name: 3D DVD Cover
Plugin URI: http://develoteca.com/plugin-3d-dvd-wordpress/
Description: 3D DVD Cover and webgl (create featured image).
Author: Oscar Uh 
Version: 1.0
Author URI: http://www.develoteca.com/
*/
// Add Thumbnail Column to Portfolio Posts List
add_filter('manage_posts_columns', 'dvd_3d_add_post_thumb_column');
function dvd_3d_add_post_thumb_column($posts_columns) {
    $temp_arr = array_slice($posts_columns, 1);
    $posts_columns = array_slice($posts_columns, 0, 1);
    
    $posts_columns['icon'] = '';
    $posts_columns = array_merge($posts_columns, $temp_arr);

    return $posts_columns;
}
add_action('manage_posts_custom_column',  'dvd_3d_add_post_thumb');
function dvd_3d_add_post_thumb($posts_columns) {
    switch ($posts_columns) {
        case 'icon':
            echo '<div style="text-align:center;padding:0 0 2px;">';
            the_post_thumbnail(array(150,150));
            echo '</div>';
        break;
    }
}
add_filter( 'image_size_names_choose', 'jss_custom_image_sizes' );
function jss_custom_image_sizes( $sizes ){
    $custom_sizes = array(
        'dvdcoverfront'    =>        'DVD FRONT',
		'dvdcovercenter' =>     'DVD SPINE',
		'dvdcoverback'    =>        'DVD BACK'
		
    );
    return array_merge( $sizes, $custom_sizes );
}
add_action( 'after_setup_theme', 'add_all_images_dvd' );

function add_all_images_dvd(){
	if ( function_exists('add_theme_support') ) {
		set_post_thumbnail_size(140, 140, true);
		add_image_size('dvdcoverfront', 342, 469, true);
		add_image_size('dvdcoverback', 342, 469, true);
		add_image_size('dvdcovercenter',34,469, true);
		
		update_option('dvdcoverfront'.'_size_w', 342);
		update_option('dvdcoverfront'.'_size_h', 469);
		update_option('dvdcoverfront'.'_crop', 1);
		
		update_option('dvdcoverback'.'_size_w', 342);
		update_option('dvdcoverback'.'_size_h', 469);
		update_option('dvdcoverback'.'_crop', 1);
		
		update_option('dvdcovercenter'.'_size_w', 34);
		update_option('dvdcovercenter'.'_size_h', 469);
		update_option('dvdcovercenter'.'_crop', 1);
	}
}
add_action('image_save_pre', 'add_image_options');
function add_image_options($data){
	global $_wp_additional_image_sizes;
	foreach($_wp_additional_image_sizes as $size => $properties){
		update_option($size."_size_w", $properties['width']);
		update_option($size."_size_h", $properties['height']);
		update_option($size."_crop", $properties['crop']);
	}
	return $data;
}
add_action( 'add_meta_boxes', 'metabox_model3d' );  

function metabox_model3d() {  
		add_meta_box( 'metabox_model3d', '3D DVD Cover (create featured image)', 'metabox_model3d_function', null, 'normal', 'high' );  
	}  
function metabox_model3d_function()  {

		$values = get_post_custom( $post->ID );
		$image_1 = isset( $values['image_1'] ) ? esc_attr( $values['image_1'][0] ) : '';
		$image_2 = isset( $values['image_2'] ) ? esc_attr( $values['image_2'][0] ) : '';
		$image_3 = isset( $values['image_3'] ) ? esc_attr( $values['image_3'][0] ) : '';
		$mv_cr_section_color = isset( $values['mv_cr_section_color'] ) ? esc_attr( $values['mv_cr_section_color'][0] ) : '';
		$grados_3dvd= isset( $values['grados_3dvd'] ) ? esc_attr( $values['grados_3dvd'][0] ) :56;
		$animate3d = $values['animate3d'][0];
	?>
	<div style="margin:10px;">
		<div id="loader-ajax" style="display:none;text-align:center"><img src="<?php echo plugins_url( 'images/ajax-loader.gif' , __FILE__ )?>"/></div>
		<table>
			<tr>
				<td>
					<div class="button_dvds">
					<input type="hidden" id="image_1" name="image_1" value="<?php echo $image_1;?>" style="width: 550px; float:left; margin:0 5px;"/>
					<img   style="cursor:pointer" class=" upload_image_button"  src="<?php echo plugins_url( 'images/dvdfront.png' , __FILE__ )?>" >Front
					</div> 
				</td>
				<td>
					<div class="button_dvds">
					<input type="hidden" id="image_2" name="image_2" value="<?php echo $image_2;?>" style="width: 550px; float:left; margin:0 5px;"/>
					<img   style="cursor:pointer"  class=" upload_image_button"  src="<?php echo plugins_url( 'images/dvdcenter.png' , __FILE__ )?>" >Spine
					</div>
				</td>
				<td>
					<div class="button_dvds">
					<input type="hidden" id="image_3" name="image_3" value="<?php echo $image_3;?>" style="width: 550px; float:left; margin:0 5px;"/>
					<img  style="cursor:pointer;"  class=" upload_image_button"  src="<?php echo plugins_url( 'images/dvdback.png' , __FILE__ )?>" >Back
					</div>
				</td>
				<td>
					<div class="button_dvds">
					<img  id="btn_create" style="cursor:pointer;padding-bottom: 3px;padding-top: 4px;"  src="<?php echo plugins_url( 'images/prin_icon.png' , __FILE__ )?>" >
					<br>
					img 3D
					</div>
				</td>
				<td>
					<div class="button_dvds">
					<img  id="btn_print" style="cursor:pointer;padding-bottom: 3px;padding-top: 4px;"  src="<?php echo plugins_url( 'images/prin_icon2.png' , __FILE__ )?>" >
					<br>
						View
					</div>
				</td>
				
				<td>
				<div style="position: absolute;">
				<input type="checkbox" 	<?php if( $animate3d) { ?>checked="checked"<?php } ?> id="animate3d" name="animate3d">Animate&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input name="mv_cr_section_color" type="text" id="mv_cr_section_color" value="<?php echo $mv_cr_section_color;?>" data-default-color="#ffffff">&nbsp;0-360 (<span id="valueofrange"><?php echo $grados_3dvd;?></span>) &nbsp;<input type="range" value="<?php echo $grados_3dvd;?>" id="grados_3dvd" name="grados_3dvd"  style="position: absolute;" min="0" max="360">
				</div>
				</td>
			</tr>
	    </table>
	</div>
	<a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=oscar%40sorcode%2ecom&lc=US&item_name=Develoteca&item_number=2014&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHostedGuest">
<img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" />
</a>
	<hr>
	 <div id="render"></div>
	<?php 
	wp_enqueue_style('style_model_metabox',plugins_url('css/style_model_metabox.css', __FILE__),	array());
	wp_enqueue_script('threejs',plugins_url('js/three.min.js', __FILE__),	array());
	wp_enqueue_script('OrbitControls',plugins_url('js/OrbitControls.js', __FILE__),	array()	);
	wp_enqueue_script('models3d',plugins_url('js/models3d.js', __FILE__),	array());
	$url3dmodel = array( 
					'url3dmodel' => plugins_url('js/3dmodel.js', __FILE__),
					'url3dtexture' => plugins_url('changecolordvd.php', __FILE__) 
					);
	wp_localize_script( 'models3d', 'dvdexternaldata', $url3dmodel );
	if ( ! did_action( 'wp_enqueue_media' ) )wp_enqueue_media();
	wp_enqueue_script('wp-color-picker');
    wp_enqueue_style( 'wp-color-picker' );
	wp_nonce_field( 'meta_box_3dvd', 'meta_box_3dvd' );
}
// save data dvd 3d
add_action( 'save_post', 'dvd_3d_meta_box_save' );
function dvd_3d_meta_box_save( $post_id ){
	// Bail if we're doing an auto save
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	// if our nonce isn't there, or we can't verify it, bail
	if( !isset( $_POST['meta_box_3dvd'] ) || !wp_verify_nonce( $_POST['meta_box_3dvd'], 'meta_box_3dvd' ) ) return;
	// if our current user can't edit this post, bail
	if( !current_user_can( 'edit_post' ) ) return;
	// now we can actually save the data
	$allowed = array( 
		'a' => array( // on allow a tags
			'href' => array() // and those anchords can only have href attribute
		)
	);
	// Probably a good idea to make sure your data is set
	if( isset( $_POST['image_1'] ) ){
	if($_POST['image_1']!=''){
		update_post_meta( $post_id, 'image_1', wp_kses( $_POST['image_1'], $allowed ) );
		}
		else{
		delete_post_meta( $post_id, 'image_1','');
		}
	}
	if( isset( $_POST['image_2'] ) ){
	if($_POST['image_2']!=''){
		update_post_meta( $post_id, 'image_2', wp_kses( $_POST['image_2'], $allowed ) );
		}
		else{
		delete_post_meta( $post_id, 'image_2','');
		}
	}
	if( isset( $_POST['image_3'] ) ){
	if($_POST['image_3']!=''){
		update_post_meta( $post_id, 'image_3', wp_kses( $_POST['image_3'], $allowed ) );
		}
		else{
		delete_post_meta( $post_id, 'image_3','');
		}
	}
	if( isset( $_POST['mv_cr_section_color'] ) ){
	if($_POST['mv_cr_section_color']!=''){
		update_post_meta( $post_id, 'mv_cr_section_color', wp_kses( $_POST['mv_cr_section_color'], $allowed ) );
		}
		else{
		delete_post_meta( $post_id, 'mv_cr_section_color','');
		}
	}
	if( isset( $_POST['grados_3dvd'] ) ){
	if($_POST['grados_3dvd']!=0){
		update_post_meta( $post_id, 'grados_3dvd', wp_kses( $_POST['grados_3dvd'], $allowed ) );
		}
		else{
		delete_post_meta( $post_id, 'grados_3dvd','');
		}
	}
	update_post_meta( $post_id, 'animate3d',  $_POST['animate3d']);
}
// ajax feacture image
add_action( 'wp_ajax_save_image_3dvd', 'save_image_3dvd' );
function save_image_3dvd() {
// The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
        $image_3dvd = $_REQUEST['image_3dvd'];
		$wp_upload_dir = wp_upload_dir();
		$featured_upload_filename = $wp_upload_dir[ 'path' ] . '/'.md5(date("Y-m-d H:i:s")).'.png';
		$image_3dvd = subStr( $image_3dvd, strLen('data:image/png;base64,') );
		$image_3dvd = base64_decode($image_3dvd);
		file_put_contents($featured_upload_filename,$image_3dvd);
		$wp_filetype = wp_check_filetype(basename($featured_upload_filename), null );
		$post=$_REQUEST['post_id'];
		$attachment = array(
		 'guid' => $wp_upload_dir[ 'url' ] . '/' . basename( $featured_upload_filename ), 
		 'post_mime_type' => $wp_filetype['type'],
		 'post_title' => preg_replace('/\.[^.]+$/', '', basename($featured_upload_filename)),
		 'post_content' => '',
		 'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment( $attachment,$featured_upload_filename,$post );
		require_once(ABSPATH . 'wp-admin/includes/image.php');

		$attach_data = wp_generate_attachment_metadata( $attach_id, $featured_upload_filename);
		wp_update_attachment_metadata( $attach_id, $attach_data );
		delete_post_meta($post, '_thumbnail_id');
		add_post_meta( $post, '_thumbnail_id', $attach_id );
	}
	echo "ok";
    die();
}
?>