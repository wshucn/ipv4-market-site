/**
 * WordPress dependencies
 */
import * as React from '@safe-wordpress/element';
import { render } from '@safe-wordpress/element';

/**
 * External dependencies
 */
import { registerCoreExperiments } from '@nab/experiment-library';
import type { AlternativeId, ExperimentId } from '@nab/types';

/**
 * Internal dependencies
 */
import { JavaScriptEditorPage } from './components/page';

type Settings = {
	readonly experimentId: ExperimentId;
	readonly alternativeId: AlternativeId;
};

export function initJavaScriptEditorPage(
	id: string,
	settings: Settings
): void {
	registerCoreExperiments();

	const wrapper = document.getElementById( id );
	if ( ! wrapper ) {
		return;
	} //end if

	const { experimentId, alternativeId } = settings;
	render(
		<JavaScriptEditorPage
			experimentId={ experimentId }
			alternativeId={ alternativeId }
		/>,
		wrapper
	);
} //end initJavaScriptEditorPage()
