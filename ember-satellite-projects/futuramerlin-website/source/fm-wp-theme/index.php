<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package fm-wp-theme
 */

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Futuramerlin</title>
    <meta charset="utf-8">
    <meta content="Futuramerlin" name="author">
    <meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0"
    name="viewport">
    <link href="m.css" rel="stylesheet" type="text/css">
</head>
<body>
	<div>
		<div class="headerbgwrap">
			<div class="headerbackground"></div>
		</div>
		<div class="background"></div>
		<div class="footerbgwrap">
			<div class="footerbackground"></div>
		</div>
		<div class="leftbgwrap">
			<div class="leftbackground"></div>
		</div>
		<div class="rightbgwrap">
			<div class="rightbackground"></div>
		</div>
		<input class="nav-trigger" id="nav-trigger" type="checkbox">
		<label for="nav-trigger">&nbsp;</label>
		<div class="logo">
			<div class="logo-inner">
				<span><i><a class="nodecorate logolink" href=
				"/">futuramerlin</a></i></span>
			</div>
		</div>
			<ul class="navigation">
				<li class="nav-item nav-item-inactive index">
					<a href="/">Home</a>
				</li>
				<li class="nav-item nav-item-inactive bio">
					<a href="/bio.htm">Bio</a>
				</li>
				<li class="nav-item nav-item-inactive news">
					<a href="/news.htm">News</a>
				</li>
				<li class="nav-item nav-item-inactive blog">
					<a href="/blog.htm">Blog</a>
				</li>
				<li class="nav-item nav-item-inactive contact">
					<a href="/contact.htm">Contact</a>
				</li>
				<li class="nav-item nav-item-inactive resume">
					<a href="/resume.htm">Résumé</a>
				</li>
				<li class="nav-item nav-item-inactive ember">
					<a href="/ember">Project: Ember</a>
				</li>
				<li class="nav-item nav-item-inactive music">
					<a href="/music">Music</a>
				</li>
			</ul>
		<div class="site-wrap"></div>
		<div class="contentbackground"></div>
		<div class="contentbgborder"></div>
		<div class="content">


<?php get_sidebar(); ?>
	<div id="primary" class="fm-wp-content content-area container col-lg-8 col-md-8">
		<main id="main" class="site-main">

		<?php if ( have_posts() ) : ?>

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php
					/* Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'content', get_post_format() );
				?>

			<?php endwhile; ?>

			<?php raze_nav(); ?>

		<?php else : ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

		</main>
	</div>
		</div>

		</div>
    </div>
<?php wp_footer(); ?></body>
</html>
