<?php
/**
 * Share This Block Output.
 *
 * @package ShareThis
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 * @var array    $context    BLock context.
 */

?>
<div <?php echo get_block_wrapper_attributes(); // phpcs:ignore ?>>
	<?php if ( ! empty( $attributes['content'] ) ) { ?>
		<p class="share-this__title">
			<?php echo wp_kses_post( $attributes['content'] ); ?>
		</p>
	<?php } ?>
	<p class="share-this__code"><code><?php echo esc_html( get_permalink() ); ?></code></p>
	<div class="share-this__web" hidden>
		<button class="share-this__button" data-title="<?php echo esc_attr( get_the_title() ); ?>" data-url="<?php echo esc_attr( get_permalink() ); ?>">Share This</button>
		<p class="share-this__alert" role="alert" aria-live="polite"></p>
	</div>
</div>
