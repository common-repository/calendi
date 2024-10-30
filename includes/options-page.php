<?php
	//---- Plugin Options
	$enabled_post_types = (cwp_get_setting('enabled_post_types') ? cwp_get_setting('enabled_post_types') : array());

	//---- Defaults
	$months = cwp_months_arr();
	$years  = cwp_years_arr();

	//---- Month and Year view
	$month_num = date('m');
	$year_num  = date('Y');

	if(isset($_GET['view_month'])) {
		$month_num = strip_tags($_GET['view_month']);
	}

	if(isset($_GET['view_year'])) {
		$year_num = strip_tags($_GET['view_year']);
	}

	//---- Get month name
	$month_date_obj = DateTime::createFromFormat('!m', $month_num);
	$month_name     = $month_date_obj->format('F');

	//---- Today Button
	$today_url = remove_query_arg('view_month', $_SERVER['REQUEST_URI']);
	$today_url = remove_query_arg('view_year', $today_url);

	$today_btn_class = 'disabled';

	if(isset($_GET['view_month']) || isset($_GET['view_year'])) {
		$today_btn_class = false;
	}

	//---- Post Types
	$post_types         = get_post_types(array(), 'objects');
	$exclude_post_types = array(
		'attachment',
		'revision',
		'nav_menu_item',
		'custom_css',
		'customize_changeset',
		'oembed_cache',
		'user_request'
	);
?>

<div class="wrap">
	<?php if(isset($_GET['ids'])) : ?>
		<br>
		<div class="notice notice-success inline">
			<p>
				<?php _e("Content has been successfully deleted.", 'calendi'); ?>
			</p>
		</div>
	<?php endif; ?>

	<div class="cwp-header">
		<h1 class="cwp-header__title">
			<?php echo $month_name; ?>
			<?php echo $year_num; ?>
		</h1>

		<div class="cwp-header__section cwp-header__section--1">
			<a class="cwp-btn <?php echo $today_btn_class; ?>" href="<?php echo $today_url; ?>">
				<?php _e("Today", 'calendi'); ?>
			</a>
		</div>

		<?php if($months || $years) : ?>
			<div class="cwp-header__section cwp-header__section--2">
				<form class="cwp-header__actions" method="get" action="">
					<input type="hidden" name="page" value="calendi">

					<?php if(isset($_GET['view_month'])) : ?>
						<input type="hidden" name="view_month" value="<?php echo $month_num; ?>">
					<?php endif; ?>

					<?php if(isset($_GET['view_year'])) : ?>
						<input type="hidden" name="view_year" value="<?php echo $year_num; ?>">
					<?php endif; ?>

					<?php if($months) : ?>
						<div class="cwp-header__actions-item">
							<select onchange="this.form.submit()" name="view_month" class="jcf-enabled">
								<?php foreach($months as $month_key => $month) : ?>
									<option value="<?php echo $month_key; ?>" <?php echo ($month_key == $month_num ? 'selected' : false); ?>><?php echo $month; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					<?php endif; ?>

					<?php if($years) : ?>
						<div class="cwp-header__actions-item">
							<select onchange="this.form.submit()" name="view_year" class="jcf-enabled">
								<?php foreach($years as $year) : ?>
									<option value="<?php echo $year; ?>" <?php echo ($year == $year_num ? 'selected' : false); ?>><?php echo $year; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					<?php endif; ?>
				</form>
			</div>
		<?php endif; ?>

		<?php if($post_types) : ?>
			<div class="cwp-header__section cwp-header__section--3">
				<form class="cwp-inline-form" method="post" action="options.php">
					<?php settings_fields('cwp_settings_group'); ?>

					<select class="jcf-enabled" multiple placeholder="<?php echo esc_attr_x("Post Types...", 'calendi'); ?>" name="cwp_settings[enabled_post_types][]">
						<?php foreach($post_types as $post_type) : ?>
							<?php if(!in_array($post_type->name, $exclude_post_types)) : ?>
								<option value="<?php echo $post_type->name; ?>" <?php selected(in_array($post_type->name, $enabled_post_types)); ?>><?php echo $post_type->label; ?></option>
							<?php endif; ?>
						<?php endforeach; ?>
					</select>
					<input class="cwp-btn" type="submit" value="<?php echo esc_attr_x("Enable", 'calendi'); ?>" />
				</form>
			</div>
		<?php endif; ?>
	</div>

	<div class="cwp-calendar">
		<div class="cwp-calendar__inner">
			<?php echo cwp_draw_calendar($month_num, $year_num); ?>
		</div>
	</div>
</div>

