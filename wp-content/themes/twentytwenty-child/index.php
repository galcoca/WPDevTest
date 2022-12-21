<?php get_header(); ?>

<div class="container">
	<h1 class="entry-title">Products</h1>
	<div class="productsGrid">
		<?php
			$controller = 1;
			$titleControl = 30;

			// The Query
			$args=array (
				'post_type' => 'products',
				'posts_per_page' => -1
				);
			$the_query = new WP_Query($args);
			while ( $the_query->have_posts() && $controller < 8 ){
				$the_query->the_post();
				$metaData = [];
				$metaData = get_post_meta( get_the_ID() );
				$regularPrice = $metaData['regularPrice'][0];
				$saleChecker = $metaData['saleChecker'][0];
				$salePrice = $metaData['salePrice'][0];
				if ($saleChecker)
				{
					$badgeController = 'showBadge';
				} else {
					$badgeController = 'hideBadge';
				}
				?>
					<div class="productItem">
						<div class="postImage">
							<div class="badgeContainer">
								<?php				
									if (has_post_thumbnail()) {
									?>

										<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
											<span class="saleBadge <?php echo($badgeController); ?>">ON SALE</span>
											<?php the_post_thumbnail(); ?>
										</a>
									<?php
									} else {
									?>

										<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
											<span class="saleBadge <?php echo($badgeController); ?>">ON SALE</span>
											<img src="https://via.placeholder.com/1000/1000" alt="Image Not Assigned">
										</a>
									<?php
									}
								?>
							</div>
						</div>
						<h2 class="postImageTitle">
							<a class="productTitle" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
								<?php 
									if (mb_strlen($post->post_title) > $titleControl)
									{ 
										echo mb_substr(the_title($before = '', $after = '', FALSE), 0, $titleControl) . ' ...'; 
									} else { 
										the_title(); 
									} 
								?>
							</a>
						</h2>
						<a class="productTitle" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
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
						</a>
						<?php the_excerpt(); ?>
						<a class="moreInfoButton" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">More Info</a>
					</div>
				<?php
				$controller++;
			}

			wp_reset_postdata();
		?>
	</div>
</div>

<?php get_footer(); ?>
