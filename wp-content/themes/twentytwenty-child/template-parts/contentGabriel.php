<style>

</style>

<?php 
if ( !post_password_required() )
{
	$imageArrayController = 0;
	$postID = get_the_ID();
	$metaData = get_post_meta( $postID );
	$category = get_the_terms( $postID, 'product_category');
	if ($metaData) 
	{
		if ($category) 
		{
			$comingCategories = count($category);
		}
		$images = $metaData['productimages_'][0];
		$regularPrice = $metaData['regularPrice'][0];
		$saleChecker = $metaData['saleChecker'][0];
		$salePrice = $metaData['salePrice'][0];
		$youtubeVideo = explode( '=', $metaData['youtubeVideo'][0] );
		$youtubeVideo = explode( '&', $youtubeVideo[1] );
		$youtubeVideoCode = $youtubeVideo[0];

		if ($saleChecker)
		{
			$badgeController = 'showBadge';
		} else {
			$badgeController = 'hideBadge';
		}

		if ($images) {
			$images = explode(',', $images);
			$imageArray = array();
			foreach ($images as $key) {
				$imageArray[]=wp_get_attachment_url($key);
				$imageArrayController++;
			}
		}

		if($comingCategories > 0) {
			$arguments = array(
				'post_type' => 'products',
				'tax_query' => array(
					array(
						'taxonomy' => 'product_category',
						'field'    => 'id',
						'terms'    => $category[0]->term_id,
					),
				),
			);
			$relatedQuery = new WP_Query( $arguments );
			$relatedPosts = $relatedQuery->posts;
			$totalPosts = count($relatedPosts);
		}
	} else {
		$images = NULL;
		$regularPrice = NULL;
		$saleChecker = NULL;
		$salePrice = NULL;
		$youtubeVideo = NULL;
		$youtubeVideoCode = NULL;
	}
	?>

	<article class="container" <?php post_class(); ?> id="post-<?php echo($postID); ?>">
		<div class="singularGrid">	
			<div class="singularItem">
				<?php
					if (has_post_thumbnail())
					{
						if($imageArrayController > 0)
						{
							$imageArrayController = $imageArrayController+1;
							?>
								<div class="slideshow-container">
									<?php
										for ($imageFor=0; $imageFor < $imageArrayController; $imageFor++) 
										{ 
											$imageNumber = $imageFor+1;
											if ($imageNumber < 7) 
											{
												?>
													<div class="singularPostImage mySlides fade badgeContainer">
														<span class="saleBadge <?php echo($badgeController); ?>">ON SALE</span>
														<img src="<?php echo($imageArray[$imageFor]) ?>" alt="Image-<?php echo ($imageNumber).'-';the_title_attribute(); ?>"  style="width: 100%;">
														<div class="numbertext"><?php echo($imageNumber); ?> / <?php echo($imageArrayController); ?></div>
													</div>
												<?php
											} else {
												?>
													<div class="singularPostImage mySlides fade badgeContainer">
														<span class="saleBadge <?php echo($badgeController); ?>">ON SALE</span>
														<?php the_post_thumbnail(); ?>
														<div class="numbertext"><?php echo($imageNumber); ?> / <?php echo($imageArrayController); ?></div>
													</div>
												<?php
											}
										}
									?>
									<a class="prev" onclick="plusSlides(-1)">&#10094;</a>
									<a class="next" onclick="plusSlides(1)">&#10095;</a>
								</div>
								<div style="text-align: center;">
									<?php
									for ($dotImageFor=0; $dotImageFor < $imageArrayController; $dotImageFor++) 
									{
										$dotimageNumber = $dotImageFor+1;
										?>
											<span class="dot" onclick="currentSlide(<?php echo($dotimageNumber); ?>)"></span>
										<?php
									}
									?>
								</div>
							<?php
						} else {
							the_post_thumbnail();
						}
					} else {
						if($imageArrayController > 0)
						{
							?>
								<div class="slideshow-container">
									<?php
										for ($imageFor=0; $imageFor < $imageArrayController; $imageFor++) 
										{ 
											$imageNumber = $imageFor+1;
											?>
												<div class="singularPostImage mySlides fade badgeContainer">
													<span class="saleBadge <?php echo($badgeController); ?>">ON SALE</span>
													<img src="<?php echo($imageArray[$imageFor]) ?>" alt="Image-<?php echo ($imageNumber).'-';the_title_attribute(); ?>"  style="width: 100%;">
													<div class="numbertext"><?php echo($imageNumber); ?> / <?php echo($imageArrayController); ?></div>
												</div>
											<?php
										}
									?>
									<a class="prev" onclick="plusSlides(-1)">&#10094;</a>
									<a class="next" onclick="plusSlides(1)">&#10095;</a>
								</div>
								<div style="text-align: center;">
									<?php
									for ($dotImageFor=0; $dotImageFor < $imageArrayController; $dotImageFor++) 
									{
										$dotimageNumber = $dotImageFor+1;
										?>
											<span class="dot" onclick="currentSlide(<?php echo($dotimageNumber); ?>)"></span>
										<?php
									}
									?>
								</div>
							<?php
						} else {
							?>
								<div class="slideshow-container">
									<div class="singularPostImage mySlides fade badgeContainer">
										<?php				
											if (has_post_thumbnail()) {
											?>
												<span class="saleBadge <?php echo($badgeController); ?>">ON SALE</span>
												<div class="numbertext">1 / 5</div>
												<?php the_post_thumbnail(); ?>
											<?php
											} else {
											?>
												<span class="saleBadge <?php echo($badgeController); ?>">ON SALE</span>
												<div class="numbertext">1 / 5</div>
												<img src="https://via.placeholder.com/1000/1000" alt="Image Not Assigned"  style="width: 100%;">
											<?php
											}
										?>
									</div>
									<div class="singularPostImage mySlides fade badgeContainer">
										<?php				
											if (has_post_thumbnail()) {
											?>
												<span class="saleBadge <?php echo($badgeController); ?>">ON SALE</span>
												<div class="numbertext">2 / 5</div>
												<?php the_post_thumbnail(); ?>
											<?php
											} else {
											?>
												<span class="saleBadge <?php echo($badgeController); ?>">ON SALE</span>
												<div class="numbertext">2 / 5</div>
												<img src="https://via.placeholder.com/1000/1000" alt="Image Not Assigned"  style="width: 100%;">
											<?php
											}
										?>
									</div>
									<div class="singularPostImage mySlides fade badgeContainer">
										<?php				
											if (has_post_thumbnail()) {
											?>
												<span class="saleBadge <?php echo($badgeController); ?>">ON SALE</span>
												<div class="numbertext">3 / 5</div>
												<?php the_post_thumbnail(); ?>
											<?php
											} else {
											?>
												<span class="saleBadge <?php echo($badgeController); ?>">ON SALE</span>
												<div class="numbertext">3 / 5</div>
												<img src="https://via.placeholder.com/1000/1000" alt="Image Not Assigned"  style="width: 100%;">
											<?php
											}
										?>
									</div>
									<div class="singularPostImage mySlides fade badgeContainer">
										<?php				
											if (has_post_thumbnail()) {
											?>
												<span class="saleBadge <?php echo($badgeController); ?>">ON SALE</span>
												<div class="numbertext">4 / 5</div>
												<?php the_post_thumbnail(); ?>
											<?php
											} else {
											?>
												<span class="saleBadge <?php echo($badgeController); ?>">ON SALE</span>
												<div class="numbertext">4 / 5</div>
												<img src="https://via.placeholder.com/1000/1000" alt="Image Not Assigned"  style="width: 100%;">
											<?php
											}
										?>
									</div>
									<div class="singularPostImage mySlides fade badgeContainer">
										<?php				
											if (has_post_thumbnail()) {
											?>
												<span class="saleBadge <?php echo($badgeController); ?>">ON SALE</span>
												<div class="numbertext">5 / 5</div>
												<?php the_post_thumbnail(); ?>
											<?php
											} else {
											?>
												<span class="saleBadge <?php echo($badgeController); ?>">ON SALE</span>
												<div class="numbertext">5 / 5</div>
												<img src="https://via.placeholder.com/1000/1000" alt="Image Not Assigned"  style="width: 100%;">
											<?php
											}
										?>
									</div>
									<a class="prev" onclick="plusSlides(-1)">&#10094;</a>
									<a class="next" onclick="plusSlides(1)">&#10095;</a>
								</div>
								<div style="text-align: center;">
									<span class="dot" onclick="currentSlide(1)"></span>
									<span class="dot" onclick="currentSlide(2)"></span>
									<span class="dot" onclick="currentSlide(3)"></span>
									<span class="dot" onclick="currentSlide(4)"></span>
									<span class="dot" onclick="currentSlide(5)"></span>
								</div>
							<?php
						}
					}
				?>
			</div>
			<div class="singularItem">
				<?php	the_title( '<h1 class="singularTitle">', '</h1>' ); ?>

				<div class="product-description">
					<?php
						if ( is_search() || ! is_singular() && 'summary' === get_theme_mod( 'blog_content', 'full' ) ) {
							the_excerpt();
						} else {
							the_content( __( 'Continue reading', 'twentytwenty' ) );
						}
					?>
				</div>
				<div class="product-price">
					<?php
					if ($saleChecker)
					{
						?>
							<span class="regularPrice saleExists">$ <?php echo($regularPrice) ?></span>
							<span class="salePrice">$ <?php echo($salePrice) ?></span>
						<?php
					} else {
						?>
							<span class="regularPrice">$ <?php echo($regularPrice) ?></span>
						<?php
					}
					?>
				</div>
			</div>
		</div>
		<div class="innerContainer">
			<h2>Youtube Video</h2>
			<iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo($youtubeVideoCode); ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		</div>
		<?php 
			if ($totalPosts > 0) 
			{
				?>
					<div class="relatedProducts">
						<h2>Related Products</h2>
						<div class="productsGrid">
							<?php
								if ($comingCategories < 2 && $comingCategories > 0) {
									$max = 3;
									$titleControl = 30;
									foreach ($relatedPosts as $key) {
										$relatedIDPost = $key->ID;
										$metaDataRelated = get_post_meta( $relatedIDPost );
										$categoryRelated = get_the_terms( $relatedIDPost, 'product_category');
										if ($metaDataRelated) 
										{
											$imagesRelated = $metaDataRelated['productimages_'][0];
											$regularPriceRelated = $metaDataRelated['regularPrice'][0];
											$saleCheckerRelated = $metaDataRelated['saleChecker'][0];
											$salePriceRelated = $metaDataRelated['salePrice'][0];
											$youtubeVideoRelated = explode( '=', $metaDataRelated['youtubeVideo'][0] );
											$youtubeVideoRelated = explode( '&', $youtubeVideoRelated[1] );
											$youtubeVideoRelatedCode = $youtubeVideoRelated[0];
									
											if ($saleCheckerRelated)
											{
												$badgeController = 'showBadge';
											} else {
												$badgeController = 'hideBadge';
											}
										} else {
											$imagesRelated = NULL;
											$regularPriceRelated = NULL;
											$saleCheckerRelated = NULL;
											$salePriceRelated = NULL;
											$youtubeVideoRelated = NULL;
											$youtubeVideoRelatedCode = NULL;
										}
										if ($relatedIDPost != $postID)
										{
											?>
											<div class="productItem">
												<div class="postImage">
													<div class="badgeContainer">
														<?php				
															if (has_post_thumbnail($relatedIDPost)) {
															?>
									
																<a href="<?php the_permalink($relatedIDPost); ?>" title="<?php echo($key->post_title); ?>">
																	<span class="saleBadge <?php echo($badgeController); ?>">ON SALE</span>
																	<?php echo (get_the_post_thumbnail($relatedIDPost)); ?>
																</a>
															<?php
															} else {
															?>
									
																<a href="<?php the_permalink($relatedIDPost); ?>" title="<?php echo($key->post_title); ?>">
																	<span class="saleBadge <?php echo($badgeController); ?>">ON SALE</span>
																	<img src="https://via.placeholder.com/1000/1000" alt="Image Not Assigned">
																</a>
															<?php
															}
														?>
													</div>
												</div>
												<h2 class="postImageTitle">
													<a class="productTitle" href="<?php the_permalink($relatedIDPost); ?>" title="<?php echo($key->post_title); ?>">
														<?php 
															if (mb_strlen($key->post_title) > $titleControl)
															{ 
																echo mb_substr($key->post_title, 0, $titleControl) . ' ...'; 
															} else { 
																echo ($key->post_title);
															} 
														?>
													</a>
												</h2>
												<a class="productTitle" href="<?php the_permalink($relatedIDPost); ?>" title="<?php echo($key->post_title); ?>">
													<?php
														if ($saleCheckerRelated)
														{
															?>
																<span class="regularPrice saleExists">$ <?php echo($regularPriceRelated) ?></span>
																<span class="salePrice">$ <?php echo($salePriceRelated) ?></span>
															<?php
														} else {
															?>
																<span class="regularPrice">$ <?php echo($regularPriceRelated) ?></span>
															<?php
														}
													?>
												</a>
												<?php the_excerpt($relatedIDPost); ?>
												<a class="moreInfoButton" href="<?php the_permalink($relatedIDPost); ?>" title="<?php echo($key->post_title); ?>">More Info</a>
											</div>
											<?php
										}
									}
								}
							?>
						</div>
					</div>
				<?php
			}
		?>
	</article>

	<!-- GALLERY JS -->

	<script type="text/javascript">
		let slideIndex = 1;
		showSlides(slideIndex);

		function plusSlides(n) {
			showSlides(slideIndex += n);
		}

		function currentSlide(n) {
			showSlides(slideIndex = n);
		}

		function showSlides(n) {
			let i;
			let slides = document.getElementsByClassName("mySlides");
			let dots = document.getElementsByClassName("dot");
			if (n > slides.length) {slideIndex = 1}
			if (n < 1) {slideIndex = slides.length}
			for (i = 0; i < slides.length; i++) {
			slides[i].style.display = "none";
			}
			for (i = 0; i < dots.length; i++) {
			dots[i].className = dots[i].className.replace(" active", "");
			}
			slides[slideIndex-1].style.display = "block";
			dots[slideIndex-1].className += " active";
		}
	</script>
<?php
}