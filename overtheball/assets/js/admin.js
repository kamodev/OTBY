/**
 * Theme page: batch picture import with progress.
 */
( function () {
	'use strict';

	var button = document.querySelector( '.otb-import-start' );
	if ( ! button || ! window.otbAdmin ) {
		return;
	}

	var settings = window.otbAdmin;
	var progress = document.querySelector( '.otb-progress' );
	var bar = progress.querySelector( '.otb-progress__bar span' );
	var text = progress.querySelector( '.otb-progress__text' );

	function format( template ) {
		var args = Array.prototype.slice.call( arguments, 1 );
		return template.replace( /%(\d)\$d/g, function ( match, index ) {
			return args[ index - 1 ];
		} );
	}

	function post( action, data ) {
		var body = new URLSearchParams();
		body.append( 'action', action );
		body.append( 'nonce', settings.nonce );
		Object.keys( data ).forEach( function ( key ) {
			if ( Array.isArray( data[ key ] ) ) {
				data[ key ].forEach( function ( value ) {
					body.append( key + '[]', value );
				} );
			} else {
				body.append( key, data[ key ] );
			}
		} );
		return fetch( settings.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: body,
		} ).then( function ( response ) {
			return response.json();
		} );
	}

	button.addEventListener( 'click', function () {
		var source = button.getAttribute( 'data-source' );
		var keys = JSON.parse( button.getAttribute( 'data-keys' ) || '[]' );
		var total = keys.length;
		var done = 0;
		var failed = false;

		button.disabled = true;
		progress.hidden = false;

		function update() {
			var pct = total ? Math.round( ( done / total ) * 100 ) : 100;
			bar.style.width = pct + '%';
			text.textContent = format( settings.i18n.progress, done, total );
		}

		function next() {
			if ( ! keys.length ) {
				return post( 'otb_finish_pictures', { source: source } ).then( function ( response ) {
					bar.style.width = '100%';
					var data = response && response.data ? response.data : { library: 0, assigned: 0 };
					text.textContent = failed ? settings.i18n.error : format( settings.i18n.done, data.library, data.assigned );
				} );
			}
			var batch = keys.splice( 0, 4 );
			return post( 'otb_import_pictures', { source: source, keys: batch } )
				.then( function ( response ) {
					var results = response && response.success ? response.data.results : {};
					batch.forEach( function ( key ) {
						var item = document.querySelector( '.otb-thumbs li[data-key="' + CSS.escape( key ) + '"]' );
						if ( results[ key ] ) {
							if ( item ) {
								item.classList.add( 'is-imported' );
							}
						} else {
							failed = true;
						}
					} );
				} )
				.catch( function () {
					failed = true;
				} )
				.then( function () {
					done += batch.length;
					update();
					return next();
				} );
		}

		update();
		next();
	} );
} )();
