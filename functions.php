<?php
	/*------------------------------------------------------------
	-------- Draw Calendar
	------------------------------------------------------------*/
	/*
		Thanks to David Walsh for the base code of the calendar https://davidwalsh.name/php-calendar
	*/
	function cwp_draw_calendar($month, $year) {
		/* table headings */
		$headings  = array(
			__("Sunday", 'calendi'),
			__("Monday", 'calendi'),
			__("Tuesday", 'calendi'),
			__("Wednesday", 'calendi'),
			__("Thursday", 'calendi'),
			__("Friday", 'calendi'),
			__("Saturday", 'calendi')
		);

		/* start table */
		$calendar = '<table class="cwp-calendar__table">';

		$calendar .= '
			<thead>
				<tr class="cwp-calendar__row cwp-calendar__row--headings">
					<th class="cwp-calendar__day cwp-calendar__day--name">
					' . implode('</th><th class="cwp-calendar__day cwp-calendar__day--name">', $headings) . '
					</th>
				</tr>
			</thead>
		';

		/* days and weeks vars now ... */
		$running_day       = date('w', mktime(0, 0, 0, $month ,1 ,$year));
		$days_in_month     = date('t', mktime(0, 0, 0, $month, 1, $year));
		$days_in_this_week = 1;
		$day_counter       = 0;

		/* row for week one */
		$calendar .= '
			<tr class="cwp-calendar__row">
		';

		/* print "blank" days until the first of the current week */
		for($x = 0; $x < $running_day; $x++) {
			$calendar .= '
				<td class="cwp-calendar__day cwp-calendar__day--item cwp-calendar__day--empty"></td>
			';

			$days_in_this_week++;
		}

		/* keep going with days.... */
		for($list_day = 1; $list_day <= $days_in_month; $list_day++) {
			$current_loop_date   = date('jS F, Y', strtotime("$year-$month-$list_day"));
			$current_actual_date = date('jS F, Y');
			$current_class       = ($current_loop_date == $current_actual_date ? 'cwp-calendar__day--item-current' : false);

			$calendar.= "
				<td class='cwp-calendar__day cwp-calendar__day--item $current_class'>
			";

			/* add in the day number */
			$calendar .= "
				<div class='cwp-calendar__day-number'>$list_day</div>
			";

			$enabled_post_types = (cwp_get_setting('enabled_post_types') ? cwp_get_setting('enabled_post_types') : array('cwp_none'));

			$todays_posts_query = new WP_Query(
				array(
					'post_type'      => $enabled_post_types,
					'posts_per_page' => -1,
					'date_query'     => array(
						array(
							'year'  => $year,
							'month' => $month,
							'day'   => $list_day,
						)
					)
				)
			);

			if($todays_posts_query->have_posts()) {
				while($todays_posts_query->have_posts()) {
					$todays_posts_query->the_post();

					// Post Type
					$post_type_obj  = get_post_type_object(get_post_type());
					$post_type_name = (isset($post_type_obj->labels->singular_name) ? $post_type_obj->labels->singular_name : false);

					// Post Status
					switch(get_post_status()) {
						case 'draft':
							$post_status = __("Draft", 'calendi');
							break;
						case 'pending':
							$post_status = __("Pending", 'calendi');
							break;
						case 'future':
							$post_status = __("Scheduled", 'calendi');
							break;
						case 'private':
							$post_status = __("Private", 'calendi');
							break;
						default:
							$post_status = false;
							break;
					}

					ob_start();

					?>
						<p class='cwp-post' href='#'>
							<span class='cwp-post__controls'>
								<span class='cwp-post__controls-inner'>
									<a class='cwp-post__controls-item' href='<?php echo get_edit_post_link(); ?>' target="_blank">
										<?php _e("Edit", 'calendi'); ?>
									</a>
									|
									<a class='cwp-post__controls-item' href='<?php the_permalink(); ?>' target="_blank">
										<?php _e("View", 'calendi'); ?>
									</a>
									|
									<a class='cwp-post__controls-item cwp-post__controls-item--danger' href='<?php echo get_delete_post_link(); ?>'>
										<?php _e("Trash", 'calendi'); ?>
									</a>
								</span>
							</span>
							<?php if($post_status) : ?>
								<span class="cwp-post__tag">
									<?php echo $post_status; ?>
								</span>
							<?php endif; ?>
							<span class='cwp-post__date'>
								<?php the_time('ga'); ?>
								<span class="cwp-post__meta">
									(<?php echo $post_type_name; ?>)
								</span>
							</span>
							<span class='cwp-post__title'>
								<?php the_title(); ?>
							</span>
						</p>
					<?php

					$post_item = ob_get_clean();

					$calendar .= $post_item;
				}

				wp_reset_postdata();
			}

			$calendar .= '
				</td>
			';

			if($running_day == 6) {
				$calendar .= '
					</tr>
				';

				if(($day_counter+1) != $days_in_month) {
					$calendar .= '
						<tr class="cwp-calendar__row">
					';
				}

				$running_day       = -1;
				$days_in_this_week = 0;
			}

			$days_in_this_week++;
			$running_day++;
			$day_counter++;
		}

		/* finish the rest of the days in the week */
		if($days_in_this_week < 8) {
			for($x = 1; $x <= (8 - $days_in_this_week); $x++) {
				$calendar.= '
					<td class="cwp-calendar__day cwp-calendar__day--empty"></td>
				';
			}
		}

		/* final row */
		$calendar .= '
			</tr>
		';

		/* end the table */
		$calendar .= '
			</table>
		';

		/* all done, return result */
		return $calendar;
	}



	/*------------------------------------------------------------
	-------- All Available Months in an Array
	------------------------------------------------------------*/
	function cwp_months_arr() {
		return array(
			'01' => __("January", 'calendi'),
			'02' => __("February", 'calendi'),
			'03' => __("March", 'calendi'),
			'04' => __("April", 'calendi'),
			'05' => __("May", 'calendi'),
			'06' => __("June", 'calendi'),
			'07' => __("July", 'calendi'),
			'08' => __("August", 'calendi'),
			'09' => __("September", 'calendi'),
			'10' => __("October", 'calendi'),
			'11' => __("November", 'calendi'),
			'12' => __("December", 'calendi'),
		);
	}



	/*------------------------------------------------------------
	-------- All Available Years in an Array
	------------------------------------------------------------*/
	function cwp_years_arr() {
		$posts_query = new WP_Query(
			array(
				'post_type'      => array('post'),
				'posts_per_page' => -1
			)
		);

		$years = array();

		if($posts_query->have_posts()) {
			while($posts_query->have_posts()) {
				$posts_query->the_post();

				$post_date_year = get_the_time('Y');

				if(!in_array($post_date_year, $years)) {
					$years[] = $post_date_year;
				}
			}

			wp_reset_postdata();
		}

		return $years;
	}
