<?php

namespace Nelio_AB_Testing\Experiment_Library\Widget_Experiment;

defined( 'ABSPATH' ) || exit;

use function add_action;
use function add_filter;

function get_preview_link( $preview_link, $alternative, $control, $experiment_id, $alternative_id ) {

	$experiment = nab_get_experiment( $experiment_id );
	$scope      = $experiment->get_scope();
	return nab_get_preview_url_from_scope( $scope, $alternative_id );
}//end get_preview_link()
add_filter( 'nab_nab/widget_preview_link_alternative', __NAMESPACE__ . '\get_preview_link', 10, 5 );

add_action( 'nab_nab/widget_preview_alternative', __NAMESPACE__ . '\load_alternative', 10, 4 );

function can_browse_preview( $enabled, $type ) {
	return 'nab/widget' === $type ? true : $enabled;
}//end can_browse_preview()
add_filter( 'nab_is_preview_browsing_enabled', __NAMESPACE__ . '\can_browse_preview', 10, 2 );
