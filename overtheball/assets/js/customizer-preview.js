/**
 * Customizer preview: update CSS custom properties without a page reload.
 */
( function ( api, map ) {
	'use strict';

	var root = document.documentElement;

	Object.keys( map || {} ).forEach( function ( settingId ) {
		var conf = map[ settingId ];
		api( settingId, function ( setting ) {
			setting.bind( function ( value ) {
				var out = value;
				if ( conf.scale && Number( conf.scale ) !== 1 ) {
					out = Math.round( Number( value ) * Number( conf.scale ) * 100 ) / 100;
				}
				root.style.setProperty( conf.var, out + ( conf.unit || '' ) );
			} );
		} );
	} );
} )( wp.customize, window.otbPreviewMap );
