/**
 * External dependencies
 */
import '@nab/data';
import type { Dict } from '@nab/types';

/**
 * Internal dependencies
 */
import { initEditPostAlternativeMetabox } from './post-alternative-metabox';
import { initEditPostAlternativeBlockEditorSidebar } from './post-alternative-sidebar';

const hasNab = ( x: unknown ): x is { nab: Dict } =>
	!! x && 'object' === typeof x && 'nab' in x;

// eslint-disable-next-line @typescript-eslint/no-explicit-any
( window as any as Dict ).nab = {
	...( hasNab( window ) ? window.nab : {} ),
	initEditPostAlternativeMetabox,
	initEditPostAlternativeBlockEditorSidebar,
};
