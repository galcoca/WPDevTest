<?php

/*--------------------------------------------------*/
/*          BEGIN PART 2 STYLESHEET ENQUEUE         */

	/* FUNCTIONS */
		function parentStyles() 
		{
			$parenthandle = 'parent-style';
			wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css' );
			wp_enqueue_style( 'child-theme',
				get_stylesheet_directory_uri() . '/style.css',
				array( $parenthandle )
			);
		}

		function adminScriptStyles()
		{
			wp_enqueue_script( 'child-script', get_stylesheet_directory_uri() . '/assets/js/scripts.js', array('jquery'), '1.0.0', true);
			wp_enqueue_style( 'admin-styles', get_stylesheet_directory_uri() . '/assets/css/style.css');
		}

	/* ACTIONS */
	add_action( 'wp_enqueue_scripts', 'parentStyles' );
	add_action( 'admin_init', 'adminScriptStyles');

/*           END PART 2 STYLESHEET ENQUEUE          */
/*--------------------------------------------------*/

/*--------------------------------------------------*/
/*            BEGIN PART 3 USER CREATION            */
	/* FUNCTIONS */

		function userCreationFunction() {
			$username = 'wp-test';
			$email = 'wptest@elementor.com';
			$password = '123456789';

			$user_id = username_exists( $username );
			if (!$user_id && email_exists($email) == false) {
				$user_id = wp_create_user( $username, $password, $email );
				if( !is_wp_error($user_id) ) {
					$user = get_user_by( 'id', $user_id );
					$user->set_role( 'editor' );
				}
			}
		}

		function adminBarDisableFor() {
			if (is_user_logged_in()) :
				$user = wp_get_current_user();
				$response = false;
				if ($user->user_login == 'wp-test') :
					$response = true;        
				endif;
				return $response;
			endif;
		}

	/* ACTIONS */

	if (adminBarDisableFor()) :
		add_filter('show_admin_bar', '__return_false');
	endif;
	add_action('init', 'userCreationFunction');

/*             END PART 3 USER CREATION             */
/*--------------------------------------------------*/

/*--------------------------------------------------*/
/*              BEGIN PART 4 POST TYPES             */
	/* FUNCTIONS */
		/* Meta Box Batch */

			function MetaBoxBatch() {
				add_meta_box( 
					'priceBox',
					__('Product Price'),
					'priceBox_content',
					'products',
					'normal',
					'default'
				);
				add_meta_box( 
					'productImageUpload',
					__('Product Images'),
					'addProductImages',
					'products',
					'normal',
					'default'
				);
				add_meta_box( 
					'youtubeURL',
					__('Youtube URL'),
					'youtubeURL_content',
					'products',
					'normal',
					'default'
				);
			}


		/* Create CPT */
			function createProductsCPT() {
				$labels = array(
				'name'               => _x( 'Products', 'txtdomain' ),
				'singular_name'      => _x( 'Product', 'txtdomain' ),
				'add_new'            => _x( 'Add New', 'txtdomain' ),
				'add_new_item'       => __( 'Add New Product' ),
				'edit_item'          => __( 'Edit Product' ),
				'new_item'           => __( 'New Product' ),
				'all_items'          => __( 'All Products' ),
				'view_item'          => __( 'View Product' ),
				'search_items'       => __( 'Search Products' ),
				'not_found'          => __( "Unable to find products or doesn't exists products" ),
				'not_found_in_trash' => __( 'Trash is empty' ), 
				'menu_name'          => 'Products'
				);
				$args = array(
					'labels'        => $labels,
					'description'   => 'Holds our products and product specific data',
					'public'        => true,
					'menu_icon' 	=> 'dashicons-products',
					'taxonomies' 	=> ['product_category'],
					'menu_position' => 5,
					'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'custom-fields' ),
					'has_archive'   => true,
					'rewrite' => array('slug' => 'product'),
					'show_in_rest' => true,
				);
				register_post_type( 'products', $args ); 
			}
		/* Price and Sale Price */
			function priceBox_content( $post ) {
				wp_nonce_field( plugin_basename(__FILE__), 'priceBox_content_nonce' );
				$regularPrice = get_post_meta($post->ID, "regularPrice", true);
				$salePrice = get_post_meta($post->ID, "salePrice", true);
				$saleChecker = get_post_meta($post->ID, "saleChecker", true);
				?>
					<label for="regularPrice">Regular Price</label>
					<input type="number" step=0.01 min="0" oninput="this.value = Math.abs(this.value)" value="<?php echo ($regularPrice); ?>" id="regularPrice" name="regularPrice" placeholder="0.00" /><br>
					<?php
						if($saleChecker == "") 
						{
							?>
								<br>Is on sale? <input type="checkbox" name="saleChecker" id="saleChecker" value="false"><br>
							<?php
						} else {
							?>
								<br>Is on sale? <input type="checkbox" name="saleChecker" id="saleChecker" value="true" checked><br>
							<?php
						}
					?>
					<br><label for="salePrice">Sale Price</label>
					<input type="number" step=0.01 min="0" oninput="this.value = Math.abs(this.value)" value="<?php echo ($salePrice); ?>" id="salePrice" name="salePrice" placeholder="0.00" />
				<?php

			}

		/*Youtube Video*/

			function youtubeURL_content( $post ) {
				wp_nonce_field( plugin_basename(__FILE__), 'youtubeURL_content_nonce' );
				$youtubeVideo = get_post_meta($post->ID, "youtubeVideo", true);
				?>
					<label for="youtubeVideo">Regular Price</label>
					<input type="text" id="youtubeVideo" name="youtubeVideo" value="<?php echo ($youtubeVideo); ?>" placeholder="https://youtube.com/" /><br>
				<?php
			}

		/* Gallery */

			function addProductImages($post) {
				$productImages = get_post_meta($post->ID,'productimages_', true);
				?>
				<table cellspacing="10" cellpadding="10">
					<tr>
						<td>Product Images</td>
						<td>
							<?php 
							echo multi_media_uploader_field( 'productimages_', $productImages ); ?>
						</td>
					</tr>
				</table>
				<?php
			}

			function multi_media_uploader_field($name, $value = '') {
				$image = '">Add Image';
				$image_str = '';
				$image_size = 'full';
				$display = 'none';
				$controller = 0;
				$value = explode(',', $value);
				if (!empty($value)) {
					foreach ($value as $values) {
						if ($image_attributes = wp_get_attachment_image_src($values, $image_size)) {
							$image_str .= '<li data-attechment-id=' . $values . '><a href="' . $image_attributes[0] . '" target="_blank"><img src="' . $image_attributes[0] . '" /></a><i class="dashicons dashicons-no delete-img"></i></li>';
							$controller++;
						}
						if ($controller > 5) {
							break;
						}
					}
				}
				if($image_str){
					$display = 'inline-block';
				}
				if ($controller > 5) {
					return '<div class="multiImageUpload"><ul>' . $image_str . '</ul><a href="#" style="display:none;" class="multiImageUploadButton button' . $image . '</a><input type="hidden" class="attechments-ids ' . $name . '" name="' . $name . '" id="' . $name . '" value="' . esc_attr(implode(',', $value)) . '" /><input type="hidden" class="maxUploadsController" value="'.$controller.'" /><a href="#" class="multiImageUploadRemove button" style="display:inline-block;display:' . $display . '">Delete Gallery</a><div class="maxImageUploadFailed" style="display:none;"><h3>Unable to add more images (MAX 6)</h3></div></div>';
				} else {
					return '<div class="multiImageUpload"><ul>' . $image_str . '</ul><a href="#" class="multiImageUploadButton button' . $image . '</a><input type="hidden" class="attechments-ids ' . $name . '" name="' . $name . '" id="' . $name . '" value="' . esc_attr(implode(',', $value)) . '" /><input type="hidden" class="maxUploadsController" value="'.$controller.'" /><a href="#" class="multiImageUploadRemove button" style="display:inline-block;display:' . $display . '">Delete Gallery</a><div class="maxImageUploadFailed" style="display:none;"><h3>Unable to add more images (MAX 6)</h3></div></div>';
				}
			}

		/* Custom Taxonomy */
			function customTaxonomyProducts() {
				$labels = array(
					'name' => _x( 'Categories', 'txtdomain' ),
					'singular_name' => _x( 'Category', 'txtdomain' ),
					'search_items' =>  __( 'Search Categories' ),
					'all_items' => __( 'All Categories' ),
					'parent_item' => __( 'Parent Category' ),
					'parent_item_colon' => __( 'Parent Category:' ),
					'edit_item' => __( 'Edit Category' ), 
					'update_item' => __( 'Update Category' ),
					'add_new_item' => __( 'Add New Category' ),
					'new_item_name' => __( 'New Category Name' ),
					'menu_name' => __( 'Categories' ),
				);    
				
				register_taxonomy('product_category',array('products'), array(
					'hierarchical' => true,
					'labels' => $labels,
					'show_ui' => true,
					'show_in_rest' => true,
					'show_admin_column' => true,
					'query_var' => true,
					'rewrite' => array( 'slug' => 'category' ),
				));

				register_taxonomy_for_object_type('product_category', 'products');

				wp_insert_term(
					'Clothes', 
					'product_category', 
					array(
					'slug' => 'clothes',  
				));
				
				wp_insert_term(
					'Pants', 
					'product_category', 
					array(
					'slug' => 'pants',  
				));
				
				wp_insert_term(
					'Helmets', 
					'product_category', 
					array(
					'slug' => 'helmets',  
				));
			}
		/* Products Creation */

			function productCreator() {
				global $user_ID;
				$controllerCAT = 0;
				$idController = 500;
				$categoryAssign = 2;
				$dataCategories = get_terms( 'product_category', array('hide_empty' => false) );

				for ($maxForm=0; $maxForm < 7; $maxForm++) { 
					if (!get_post_status( $idController)){
						if ($controllerCAT < 2 ) {
							$newPostCreation = array(
								'import_id' => $idController,
								'post_title' => 'Product '.$idController,
								'post_content' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
								'post_excerpt' => "Lorem Ipsum is simply dummy text of the printing",
								'post_status' => 'publish',
								'post_date' => date('2022-12-10 08:00:00'),
								'post_author' => $user_ID,
								'post_type' => 'products',
								'tax_input' => array('product_category' => array( $dataCategories[0]->term_id ))
							);
							$youtubeVideo = 'https://www.youtube.com/watch?v=CgJOydhJfxY&ab_channel=Elementor';
							$regularPrice = rand(1000,5000);
							$salePrice = NULL;
							$saleChecker = false;
						}
						if ($controllerCAT > 1 && $controllerCAT < 4) {
							$newPostCreation = array(
								'import_id' => $idController,
								'post_title' => 'Product '.$idController,
								'post_content' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
								'post_excerpt' => "Lorem Ipsum is simply dummy text of the printing",
								'post_status' => 'publish',
								'post_date' => date('2022-12-10 08:00:00'),
								'post_author' => $user_ID,
								'post_type' => 'products',
								'tax_input' => array('product_category' => array( $dataCategories[1]->term_id ))
							);
							$youtubeVideo = 'https://www.youtube.com/watch?v=CgJOydhJfxY&ab_channel=Elementor';
							$regularPrice = rand(1000,5000);
							$salePrice = NULL;
							$saleChecker = false;
						}
						if ($controllerCAT > 3) {
							$newPostCreation = array(
								'import_id' => $idController,
								'post_title' => 'Product '.$idController,
								'post_content' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
								'post_excerpt' => "Lorem Ipsum is simply dummy text of the printing",
								'post_status' => 'publish',
								'post_date' => date('2022-12-10 08:00:00'),
								'post_author' => $user_ID,
								'post_type' => 'products',
								'tax_input' => array('product_category' => array( $dataCategories[2]->term_id ))
							);
							$youtubeVideo = 'https://www.youtube.com/watch?v=CgJOydhJfxY&ab_channel=Elementor';
							$regularPrice = rand(1000,5000);
							$salePrice = $regularPrice*0.7;
							$saleChecker = true;
						}

						$post_id = wp_insert_post($newPostCreation);
						update_post_meta( $idController, 'saleChecker', $saleChecker );
						update_post_meta( $idController, 'regularPrice', $regularPrice );
						update_post_meta( $idController, 'salePrice', $salePrice );
						update_post_meta( $idController, 'youtubeVideo', $youtubeVideo );
						$idController++;
						$controllerCAT++;
					}
				}
			}

		/* Display Products */
			
			function queryProducts( $query ) {
				if ( is_home() && $query->is_main_query() )
					$query->set( 'post_type', array( 'post', 'products' ) );
				return $query;
			}

		/* Save Data */

			function saveProductMetaBox( $post_id ) {
				if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
				if ('page' == $_POST['post_type']) 
				{
					if (!current_user_can('edit_page', $post_id ) ) return;
				} else {
					if (!current_user_can('edit_post', $post_id ) ) return;
				}
				$regularPrice = $_POST['regularPrice'];
				$salePrice = $_POST['salePrice'];
				$saleChecker = $_POST['saleChecker'];
				$youtubeVideo = $_POST['youtubeVideo'];
				$productImageDB = $_POST['productimages_'];
				if( isset($productImageDB) ){
					update_post_meta( $post_id, 'productimages_', $productImageDB );
					update_post_meta( $post_id, 'saleChecker', $saleChecker );
					update_post_meta( $post_id, 'regularPrice', $regularPrice );
					update_post_meta( $post_id, 'salePrice', $salePrice );
					update_post_meta( $post_id, 'youtubeVideo', $youtubeVideo );
				} else {
					update_post_meta( $post_id, 'productimages_', $productImageDB );
					update_post_meta( $post_id, 'saleChecker', $saleChecker );
					update_post_meta( $post_id, 'regularPrice', $regularPrice );
					update_post_meta( $post_id, 'salePrice', $salePrice );
					update_post_meta( $post_id, 'youtubeVideo', $youtubeVideo );
				}
			}

		/* PERMALINK DEPENDENCY */
			function set_permalink(){
				global $wp_rewrite;
				$wp_rewrite->set_permalink_structure("/%postname%/");
			}
	/* ACTIONS */
		add_action( 'init', 'set_permalink');
		add_action( 'init', 'createProductsCPT' );
		add_action( 'init', 'customTaxonomyProducts');
		add_action( 'add_meta_boxes', 'MetaBoxBatch' );
		add_action( 'save_post', 'saveProductMetaBox' );
		add_action( 'admin_init', 'productCreator');
		add_action( 'pre_get_posts', 'queryProducts' );

/*               END PART 4 POST TYPES              */
/*--------------------------------------------------*/

/*--------------------------------------------------*/
/*              BEGIN PART 5 SHORTCODE              */

	/* FUNCTIONS */
		function productBoxShortcodeFunction( $atts ) {

			$html_out = '';

			$attributes = shortcode_atts(
				array(
					'id' => null,
					'bgcolor' => null
				), $atts );
				
				if ($attributes['id'] != null && $attributes['bgcolor'] != null) {
					$query = new WP_Query( array( 'p' => $attributes['id'], 'post_type' => 'products' ) );
					$postData = $query->posts;
					$metaData = get_post_meta( $attributes['id'] );
					$category = get_the_terms( $attributes['id'], 'product_category');
					$regularPrice = $metaData['regularPrice'][0];
					$saleChecker = $metaData['saleChecker'][0];
					$salePrice = $metaData['salePrice'][0];
					$titleControl = 30;

					if ($saleChecker)
					{
						$badgeController = 'showBadge';
					} else {
						$badgeController = 'hideBadge';
					}

					if(strpos($attributes['bgcolor'], '#') !== false) {
						$attributes['bgcolor'] = explode( '#', $attributes['bgcolor'] );
						$attributes['bgcolor'] = $attributes['bgcolor'][1];
					}

					$html_out .= '<div class="container">';
						$html_out .= '<div class="box" style="background:#'.$attributes['bgcolor'].'; padding: 10%;">';
							$html_out .= '<div class="shortcodePostImage">';
								$html_out .= '<div class="badgeContainer">';
												if (has_post_thumbnail($attributes['id'])) 
												{
										$html_out .= '<a href="'.$postData[0]->guid.'" title="'.$postData[0]->post_title.'">';
											$html_out .= '<span class="saleBadge '.$badgeController.'">ON SALE</span>';
											$html_out .= get_the_post_thumbnail($attributes['id']);
										$html_out .= '</a>';
												} else {
										$html_out .= '<a href="'.$postData[0]->guid.'" title="'.$postData[0]->post_title.'">';
											$html_out .= '<span class="saleBadge '.$badgeController.'">ON SALE</span>';
											$html_out .= '<img src="https://via.placeholder.com/1000/1000" alt="Image Not Assigned">';
										$html_out .= '</a>';
												}
								$html_out .= '</div>';
							$html_out .= '</div>';
							$html_out .= '<h2 class="postImageTitle">';
								$html_out .= '<a class="productTitle" href="'.$postData[0]->guid.'" title="'.$postData[0]->post_title.'">';
												if (mb_strlen($postData[0]->post_title) > $titleControl)
												{ 
													$html_out.= mb_substr($postData[0]->post_title, 0, $titleControl) . ' ...';
												} else { 
													$html_out.= $postData[0]->post_title;
												} 
								$html_out .= '</a>';
							$html_out .= '</h2>';
							$html_out .= '<a class="productTitle" href="'.$postData[0]->guid.'" title="'.$postData[0]->post_title.'">';
											if ($saleChecker)
											{
									$html_out .= '<span class="regularPrice saleExists">$ '.$regularPrice.'</span>';
									$html_out .= '&nbsp;&nbsp;<span class="salePrice">$ '.$salePrice.'</span>';
											} else {
									$html_out .= '<span class="regularPrice">$ '.$regularPrice.'</span>';
											}
							$html_out .= '</a>';
							$html_out .= '</br>';
							$html_out .= '<span>Background Color Applied: #'.$attributes['bgcolor'].'</span>';
						$html_out .= '</div>';
					$html_out .= '</div">';
				} else {
					$html_out = "<h1>SHORTCODE DATA INCOMPLETE</h1>";
				}
		

		
			return $html_out;
		}

	/* ACTIONS */
		add_shortcode( 'productshortcode', 'productBoxShortcodeFunction' );


/*               END PART 5 SHORTCODE               */
/*--------------------------------------------------*/

/*--------------------------------------------------*/
/*            BEGIN PART 6 Filters/Hooks            */

	/* FUNCTIONS */
		function updateShortCodeAttr( $output, $tag, $attr, $m ) {
			$attr['id'] = 500;
			$attr['bgcolor'] = 'fef84c';
			global $shortcode_tags;
			$content = isset( $m[5] ) ? $m[5] : null;
			$output  = $m[1] . call_user_func( $shortcode_tags[ $tag ], $attr, $content, $tag ) . $m[6];
			return $output;
		}

	/* FILTERS */
		add_filter( 'do_shortcode_tag', 'updateShortCodeAttr', 1, 4 );

/*             END PART 6 Filters/Hooks             */
/*--------------------------------------------------*/

/*--------------------------------------------------*/
/*               BEGIN PART 7 API REST              */

	/* FUNCTIONS */

	function getProductsEndPoint($request){
		$arguments = array(
			'post_type' => 'products',
			'tax_query' => array(
				array(
					'taxonomy' => 'product_category',
					'field'    => 'id',
					'terms'    => $request['category_id'],
				),
			),
		);

		$posts = get_posts($arguments);

		$newArray = array();
		foreach ($posts as $key) {
			$newPostDataID = $key->ID;
			$metaNewPostData = get_post_meta( $newPostDataID );
			$imagesNewPostData = $metaNewPostData['productimages_'][0];
			$regularPriceNewPostData = $metaNewPostData['regularPrice'][0];
			$salePriceNewPostData = $metaNewPostData['salePrice'][0];
			$saleCheckerNewPostData = $metaNewPostData['saleChecker'][0];
			$saleChecker = false;
			if ($saleCheckerNewPostData)
			{
				$saleChecker = true;
			}

			if ($imagesNewPostData) {
				$imagesNewPostData = explode(',', $imagesNewPostData);
				$imagesNewArray = array();
				foreach ($imagesNewPostData as $imageKey) {
					$imagesNewArray[]=wp_get_attachment_url($imageKey);
				}
			} else {
				$imagesNewArray = null;
			}

			$tempArray = array(
				'title'=>$key->post_title,
				'description'=>$key->post_content,
				'featuredImage'=>get_the_post_thumbnail($newPostDataID),
				'galleryImages'=> $imagesNewArray,
				'price' => $regularPriceNewPostData,
				'isonsale' => $saleChecker,
				'salePrice' => $salePriceNewPostData
			);
			array_push($newArray, $tempArray);
		}

		if (empty($newArray)) { return new WP_Error( 'empty_category', 'There are no products to display', array('status' => 404) ); }
	
		$response = new WP_REST_Response($newArray);
		$response->set_status(200);
		return $response;
	}

	//ENDPOINT: https://domain.com/wp-json/twentytwenty-child/v1/products/CATEGORYID

	/* REGISTER ROUTE */
	add_action('rest_api_init', function () {
		register_rest_route( 'twentytwenty-child/v1', 'products/(?P<category_id>\d+)',
		array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => 'getProductsEndPoint',
			'permission_callback' => '__return_true'
		),);
	});

/*                END PART 7 API REST               */
/*--------------------------------------------------*/
?>