<?php
/**
 * Manga Release filter template part.
 *
 * @package MangaRelease
 */

namespace CAB\MangaRelease;

$total      = $args['total'] ?? 0;
$dates      = $args['dates'] ?? array();
$publishers = $args['publishers'] ?? array();
?>


<details class="release-filters" open>
	<summary><?php esc_html__( 'Filter Releases', 'cab-manga-release' ); ?></summary>
	<form class="release-filters__form" action="<?php the_permalink(); ?>">
		<p><?php esc_html__( 'Select how you would like to filter the list, and then press the Filter button', 'cab-manga-release' ); ?></p>
		<div class="release-filters__inputs">
			<div class="release-filters__select">
				<label for="releasePublishers"><?php esc_html__( 'By Publisher', 'cab-manga-release' ); ?></label>
				<select class="release-publishers" id="releasePublishers" name="publisher">
					<option value=""><?php echo esc_html__( 'See All', 'cab-manga-release' ) . ' (' . esc_html( $total ) . ')'; ?></option>
					<?php
					ksort( $publishers, SORT_NATURAL | SORT_FLAG_CASE );
					foreach ( $publishers as $name => $count ) {
						echo '<option value="' . esc_attr( sanitize_title_with_dashes( $name ) ) . '">' . esc_html( $name ) . ' (' . esc_html( $count ) . ')</option>';
					}
					?>
				</select>
			</div>
			<div class="release-filters__select">
				<label for="releaseDates"><?php esc_html__( 'By Date', 'cab-manga-release' ); ?></label>
				<select id="releaseDates" name="date">
					<option value=""><?php esc_html__( 'All Dates', 'cab-manga-release' ); ?></option>
					<?php
					foreach ( $dates as $slug => $date ) {
						echo '<option value="' . esc_attr( sanitize_title_with_dashes( $slug ) ) . '">' . esc_html( $date ) . '</option>';
					}
					?>
				</select>
			</div>
			<div class="release-filters__options">
				<fieldset>
					<legend><?php esc_html__( 'Hide Format(s):', 'cab-manga-release' ); ?></legend>
					<div class="release-checkboxes">
						<div class="release-checkbox__wrap">
							<input type="checkbox" id="hideDigital" name="hideFormat" value="digital" class="release-type__filter" />
							<label for="hideDigital"><span class="screen-reader-text"><?php esc_html__( 'Hide', 'cab-manga-release' ); ?> </span><?php esc_html__( 'Digital', 'cab-manga-release' ); ?></label>
						</div>
						<div class="release-checkbox__wrap">
							<input type="checkbox" id="hidePhysical" name="hideFormat" value="physical" class="release-type__filter" />
							<label for="hidePhysical"><span class="screen-reader-text"><?php esc_html__( 'Hide', 'cab-manga-release' ); ?> </span><?php esc_html__( 'Physical', 'cab-manga-release' ); ?></label>
						</div>
					</div>
				</fieldset>
			</div>
			<div class="release-filters__buttons">
				<button class="filter-btn" type="submit" id="releaseFilter"><?php esc_html__( 'Filter', 'cab-manga-release' ); ?></button>
				<button class="filter-btn filter-btn__clear" type="button" id="releaseClear" disabled><?php esc_html__( 'Clear All', 'cab-manga-release' ); ?></button>
			</div>
		</div>
	</form>
</details>
