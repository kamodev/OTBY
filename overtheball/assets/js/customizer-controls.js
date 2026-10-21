/**
 * Customizer controls: range sliders, sortable section order, gallery picker.
 */
( function ( $, api ) {
	'use strict';

	function setSetting( id, value ) {
		var setting = api( id );
		if ( setting ) {
			setting.set( value );
		}
	}

	// Range + number pair.
	$( document ).on( 'input change', '.otb-range__slider, .otb-range__number', function () {
		var $wrap = $( this ).closest( '.otb-range' );
		var value = this.value;
		$wrap.find( '.otb-range__slider, .otb-range__number' ).not( this ).val( value );
		setSetting( $wrap.data( 'setting' ), value );
	} );

	$( document ).on( 'click', '.otb-range__reset', function () {
		var $wrap = $( this ).closest( '.otb-range' );
		var value = $( this ).data( 'default' );
		$wrap.find( '.otb-range__slider, .otb-range__number' ).val( value );
		setSetting( $wrap.data( 'setting' ), String( value ) );
	} );

	// Sortable lists.
	function saveOrder( $list ) {
		var keys = $list.children( '.otb-sortable__item' ).map( function () {
			return $( this ).data( 'key' );
		} ).get();
		setSetting( $list.data( 'setting' ), keys.join( ',' ) );
	}

	api.bind( 'ready', function () {
		$( '.otb-sortable' ).each( function () {
			var $list = $( this );
			$list.sortable( {
				axis: 'y',
				handle: '.dashicons-menu',
				update: function () {
					saveOrder( $list );
				},
			} );
		} );
	} );

	$( document ).on( 'click', '.otb-sortable__up, .otb-sortable__down', function () {
		var $item = $( this ).closest( '.otb-sortable__item' );
		if ( $( this ).hasClass( 'otb-sortable__up' ) ) {
			$item.prev().before( $item );
		} else {
			$item.next().after( $item );
		}
		saveOrder( $item.parent() );
		$item.trigger( 'focus' );
	} );

	// Gallery picker.
	$( document ).on( 'click', '.otb-gallery-control__select', function ( event ) {
		event.preventDefault();
		var $wrap = $( this ).closest( '.otb-gallery-control' );
		var settingId = $wrap.data( 'setting' );
		var current = String( api( settingId ).get() || '' ).split( ',' ).filter( Boolean );

		var frame = wp.media( {
			title: window.otbControls.galleryTitle,
			button: { text: window.otbControls.galleryButton },
			library: { type: 'image' },
			multiple: 'add',
		} );

		frame.on( 'open', function () {
			var selection = frame.state().get( 'selection' );
			current.forEach( function ( id ) {
				var attachment = wp.media.attachment( id );
				attachment.fetch();
				selection.add( attachment );
			} );
		} );

		frame.on( 'select', function () {
			var ids = [];
			var $thumbs = $wrap.find( '.otb-gallery-control__thumbs' ).empty();
			frame.state().get( 'selection' ).each( function ( attachment ) {
				var data = attachment.toJSON();
				var thumb = data.sizes && data.sizes.thumbnail ? data.sizes.thumbnail.url : data.url;
				ids.push( data.id );
				$( '<li>' ).attr( 'data-id', data.id ).append( $( '<img alt="">' ).attr( 'src', thumb ) ).appendTo( $thumbs );
			} );
			setSetting( settingId, ids.join( ',' ) );
			$wrap.find( '.otb-gallery-control__empty' ).prop( 'hidden', ids.length > 0 );
			$wrap.find( '.otb-gallery-control__clear' ).prop( 'hidden', ids.length === 0 );
		} );

		frame.open();
	} );

	$( document ).on( 'click', '.otb-gallery-control__clear', function () {
		var $wrap = $( this ).closest( '.otb-gallery-control' );
		$wrap.find( '.otb-gallery-control__thumbs' ).empty();
		$wrap.find( '.otb-gallery-control__empty' ).prop( 'hidden', false );
		$( this ).prop( 'hidden', true );
		setSetting( $wrap.data( 'setting' ), '' );
	} );
} )( jQuery, wp.customize );
