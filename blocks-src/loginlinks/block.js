import { registerBlockType } from '@wordpress/blocks';
import { 
    useBlockProps,
    InspectorControls
} from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import { ToggleControl } from '@wordpress/components';

import metadata from './block.json';

// import scss to make script build compile to css otherwise it won't work.
import './style.scss';

registerBlockType( metadata.name, {
    edit: ({attributes, setAttributes}) => {
        const {
            displayLinkToAdmin
        } = attributes;

        console.log(__('Display link to admin dashboard', 'okv-oauth'));
        return <p {...useBlockProps()}>
            <ServerSideRender
                block="rd-oauth/blocks-loginlinks"
                attributes={ attributes.attributes }
                label="hello label"
                checked="{displayLinkToAdmin}"
            />
            <InspectorControls key="setting">
                <div id="rd-oauth-loginlinks-controls" class="components-panel__body is-opened">
                    <fieldset>
                        <ToggleControl
                            label={__('Display link to admin dashboard', 'okv-oauth')}
                            checked={ displayLinkToAdmin }
                            onChange={ () => setAttributes({
                                displayLinkToAdmin: !displayLinkToAdmin
                            }) }
                        />
                    </fieldset>
                </div>
            </InspectorControls>
        </p>;
    },
} );
