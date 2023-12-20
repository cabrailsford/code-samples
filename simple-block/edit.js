/**
 * WordPress dependencies
 */
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

/**
 * Edit component.
 *
 * @returns {Function} Render the edit screen
 */
export default function Edit() {
	const TEMPLATE = [ [ 'core/paragraph',
		{
			'content': '<strong>Thank you for your support!</strong> We can\'t say it enough.'
		}
	] ];
	const blockProps = useBlockProps( { className: 'cab-contributor-thanks' } );
    const combinedBlockProps = useInnerBlocksProps(
        blockProps,
        {
            allowedBlocks: [ 'core/paragraph' ],
            template: TEMPLATE
        }
    );

    return (
        <div {...combinedBlockProps} />
    );
}
