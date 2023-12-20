/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { content } = attributes;

	return (
		<div { ...useBlockProps() }>
			<RichText
				className="share-this__title"
				tagName="p"
				placeholder={__('Message here…', 'share-this')}
				value={content}
				onChange={( content ) => setAttributes({ content })}
			/>
			<p class="share-this__code"><code>{ __( 'This will output the full permalink on the frontend.', 'share-this' ) }</code></p>
		</div>
	);
}
