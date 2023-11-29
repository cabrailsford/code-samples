<?php
/**
 * Contributor Thank You Block Output.
 *
 * @package ContributorThanks
 */

printf(
	'<div %s>',
	wp_kses_data(
		get_block_wrapper_attributes(
			array(
				'class' => 'cab-contributor-thanks',
			)
		)
	)
);
echo $content; //phpcs:disable
echo '</div>';
