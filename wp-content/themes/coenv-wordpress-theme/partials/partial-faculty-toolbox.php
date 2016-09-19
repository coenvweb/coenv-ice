<?php
/**
 * The Faculty Archive toolbox
 */
global $themes;
global $units;
global $faculty;

$ordered_themes = $themes;
?>

<header class="faculty-toolbox jsIsotopeItem featured visible">

	<div class="faculty-toolbox-inner jsIsotopeItemInner visible">

		<div class="faculty-toolbox-header">

			<h1><a href="<?php bloginfo('url') ?>/faculty/">Faculty</a></h1>

		</div><!-- .faculty-toolbox-header -->

		<div class="faculty-toolbox-tools">

			<div class="faculty-toolbox-tools-inner">

				<div class="faculty-toolbox-theme-roller-section">

					<p class="title">Research themes:</p>

					<div class="faculty-toolbox-theme-roller" tabindex="10">

						<!--<div class="faculty-toolbox-interaction-layer"></div>-->

						<div class="faculty-toolbox-theme-roller-inner">

							<?php if ( !empty( $ordered_themes ) ) : ?>

								<div class="faculty-toolbox-theme-roller-item origin" data-feedback="College of the Environment Faculty Profiles" data-number="<?php echo $faculty->post_count ?>" data-theme="theme-all" data-permalink="<?php bloginfo('url') ?>/faculty/#theme-all">All Research Themes</div>

								<?php foreach ( $ordered_themes as $theme ) : ?>

									<?php $origin = $theme->slug == $selected_theme->slug ? ' origin' : ''; ?>

									<div class="faculty-toolbox-theme-roller-item<?php echo $origin ?>" data-feedback="Faculty are working on theme" data-number="<?php echo $theme->count ?>" data-permalink="<?php bloginfo('url') ?>/faculty/#theme-<?php echo $theme->slug ?>" data-theme="theme-<?php echo $theme->slug ?>"><?php echo $theme->name ?></div>

								<?php endforeach ?>

							<?php endif ?>

						</div><!-- .faculty-toolbox-theme-roller-inner -->

					</div><!-- .faculty-toolbox-theme-roller -->

			</div><!-- .faculty-toolbox-theme-roller-section -->

				<div class="faculty-toolbox-form">

					<form action="">
						<div class="select-wrapper">
		          <select id="select-theme" name="theme" data-placeholder="All Research Themes">
		          	<option data-feedback="College of the Environment Faculty Profiles" data-number="<?php echo $faculty->post_count ?>" data-permalink="<?php bloginfo('url') ?>/faculty/#theme-all" value="theme-all"></option>
		              <?php foreach ( $themes as $theme ) : ?>
		              		<option data-feedback="Faculty are working on theme" data-number="<?php echo $theme->count ?>" data-permalink="<?php bloginfo('url') ?>/faculty/#theme-<?php echo $theme->slug ?>" value="theme-<?php echo $theme->slug ?>"><?php echo $theme->name ?></option>
		              <?php endforeach ?>
		          </select>
		          <label for="select-theme">Research themes</label>
		        </div><!-- .select-wrapper -->

		        <div class="select-wrapper">
		          <select id="select-unit" name="unit" data-placeholder="All schools/departments">
		              <option data-feedback="in unit" data-number="X" data-permalink="<?php bloginfo('url') ?>/faculty/#unit-all" value="unit-all"></option>
		              <?php foreach ( $units as $unit ) : ?>
		                  <option data-feedback="in unit" data-number="X" data-permalink="<?php bloginfo('url') ?>/faculty/#unit-<?php echo $unit->slug ?>" value="unit-<?php echo $unit->slug ?>"><?php echo $unit->name ?></option>
		              <?php endforeach ?>
		          </select>
		          <label for="select-unit">School/department</label>
	        	</div><!-- .select-wrapper -->

	        	<div class="hrule"></div>

	          <div class="field-wrap search-form">
	              <input id="keyword-search" name="s" type="text" placeholder="<?php echo esc_attr(__('Search all faculty')); ?>" value="<?php the_search_query(); ?>">
	              <input type="hidden" name="post_type" value="faculty" /> <!-- important! -->
	              <button type="submit"><i class="icon-search"></i><span>Search</span></button>
	          </div><!-- .field-wrap -->
	          <label>Search</label>
	      </form>

			</div><!-- .faculty-toolbox-form -->

			</div><!-- .faculty-toolbox-tools-inner -->

		</div><!-- .faculty-toolbox-tools -->

		<div class="faculty-toolbox-footer">

			<div class="faculty-toolbox-feedback">
				<div class="feedback-number"><?php echo $faculty->post_count ?></div>
				<p class="feedback-message">College of the Environment Faculty Profiles</p>
			</div><!-- .faculty-toolbox-feedback -->

			<div class="faculty-toolbox-more-search-tools">
				<a href="#"><i class="icon-search"></i>More search tools<i class="icon-arrow-right"></i></a>
			</div><!-- .faculty-toolbox-more-search-tools -->

		</div><!-- .faculty-toolbox-footer -->

	</div><!-- .faculty-toolbox-inner -->

</header><!-- .faculty-toolbox -->