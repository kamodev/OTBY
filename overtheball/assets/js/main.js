/**
 * Over The Ball — front-end behavior (no dependencies).
 *
 * Header states, drawers & search overlay, submenus, announcement bar,
 * hero slider, product carousels & tabs, reveal animations, lightbox,
 * WooCommerce niceties (quantity buttons, cart drawer, skein calculator,
 * sticky add to cart, filter drawer).
 */
( function () {
	'use strict';

	var settings = window.otbSettings || {};
	var doc = document;
	var body = doc.body;
	var reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function $( selector, context ) {
		return ( context || doc ).querySelector( selector );
	}

	function $$( selector, context ) {
		return Array.prototype.slice.call( ( context || doc ).querySelectorAll( selector ) );
	}

	function focusable( container ) {
		return $$( 'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select, textarea, [tabindex]:not([tabindex="-1"])', container ).filter( function ( el ) {
			return el.offsetParent !== null;
		} );
	}

	function storageGet( key ) {
		try {
			return window.localStorage.getItem( key );
		} catch ( e ) {
			return null;
		}
	}

	function storageSet( key, value ) {
		try {
			window.localStorage.setItem( key, value );
		} catch ( e ) {}
	}

	/* ---------------------------------------------------------------------
	 * Dialog-like panels: mobile menu, cart drawer, search overlay, filters.
	 * ------------------------------------------------------------------ */

	var openPanel = null;

	function openDialog( panel, trigger ) {
		if ( ! panel ) {
			return;
		}
		if ( openPanel && openPanel.el !== panel ) {
			closeDialog();
		}
		panel.hidden = false;
		// Next frame so the transition runs.
		window.requestAnimationFrame( function () {
			panel.classList.add( 'is-open' );
		} );
		body.classList.add( 'otb-locked' );
		if ( trigger ) {
			trigger.setAttribute( 'aria-expanded', 'true' );
		}
		openPanel = { el: panel, trigger: trigger };

		var first = $( 'input[type="search"]', panel ) || focusable( panel )[ 0 ] || $( '[tabindex="-1"]', panel );
		window.setTimeout( function () {
			if ( first ) {
				first.focus();
			}
		}, 60 );
	}

	function closeDialog() {
		if ( ! openPanel ) {
			return;
		}
		var current = openPanel;
		openPanel = null;
		current.el.classList.remove( 'is-open' );
		body.classList.remove( 'otb-locked' );
		if ( current.trigger ) {
			current.trigger.setAttribute( 'aria-expanded', 'false' );
			current.trigger.focus();
		}
		window.setTimeout( function () {
			if ( ! current.el.classList.contains( 'is-open' ) && ! current.el.classList.contains( 'sidebar' ) ) {
				current.el.hidden = true;
			}
		}, reduceMotion ? 0 : 380 );
	}

	doc.addEventListener( 'keydown', function ( event ) {
		if ( ! openPanel ) {
			return;
		}
		if ( 'Escape' === event.key ) {
			closeDialog();
			return;
		}
		if ( 'Tab' === event.key ) {
			var items = focusable( openPanel.el );
			if ( ! items.length ) {
				return;
			}
			var first = items[ 0 ];
			var last = items[ items.length - 1 ];
			if ( event.shiftKey && doc.activeElement === first ) {
				event.preventDefault();
				last.focus();
			} else if ( ! event.shiftKey && doc.activeElement === last ) {
				event.preventDefault();
				first.focus();
			}
		}
	} );

	doc.addEventListener( 'click', function ( event ) {
		var closer = event.target.closest( '[data-otb-close]' );
		if ( closer && openPanel && openPanel.el.contains( closer ) ) {
			event.preventDefault();
			closeDialog();
		}
	} );

	$$( '.menu-toggle' ).forEach( function ( button ) {
		button.addEventListener( 'click', function () {
			openDialog( $( '#otb-mobile-drawer' ), button );
		} );
	} );

	$$( '.search-toggle' ).forEach( function ( button ) {
		button.addEventListener( 'click', function () {
			openDialog( $( '#otb-search-overlay' ), button );
		} );
	} );

	function openCart( trigger ) {
		var drawer = $( '#otb-cart-drawer' );
		if ( drawer ) {
			openDialog( drawer, trigger || $( '.js-otb-cart-toggle' ) );
			return true;
		}
		return false;
	}

	$$( '.js-otb-cart-toggle' ).forEach( function ( link ) {
		link.addEventListener( 'click', function ( event ) {
			if ( openCart( link ) ) {
				event.preventDefault();
			}
		} );
	} );

	// Open the mini cart after an AJAX add to cart (WooCommerce fires this via jQuery).
	if ( window.jQuery ) {
		window.jQuery( body ).on( 'added_to_cart', function () {
			if ( 'drawer' === settings.cartBehavior ) {
				openCart();
			}
		} );
	}

	/* ---------------------------------------------------------------------
	 * Shop filter drawer (mobile).
	 * ------------------------------------------------------------------ */

	var filterToggle = $( '.otb-filter-toggle' );
	var filterSidebar = $( '.sidebar--drawer' );
	if ( filterToggle && filterSidebar ) {
		var backdrop = doc.createElement( 'div' );
		backdrop.className = 'otb-filters-backdrop';
		backdrop.hidden = true;
		body.appendChild( backdrop );

		var closeFilters = function () {
			filterSidebar.classList.remove( 'is-open' );
			backdrop.hidden = true;
			body.classList.remove( 'otb-locked' );
			filterToggle.setAttribute( 'aria-expanded', 'false' );
		};

		filterToggle.addEventListener( 'click', function () {
			filterSidebar.classList.add( 'is-open' );
			backdrop.hidden = false;
			body.classList.add( 'otb-locked' );
			filterToggle.setAttribute( 'aria-expanded', 'true' );
			var first = focusable( filterSidebar )[ 0 ];
			if ( first ) {
				first.focus();
			}
		} );
		backdrop.addEventListener( 'click', closeFilters );
		$$( '[data-otb-close-filters]', filterSidebar ).forEach( function ( button ) {
			button.addEventListener( 'click', closeFilters );
		} );
		doc.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key && filterSidebar.classList.contains( 'is-open' ) ) {
				closeFilters();
				filterToggle.focus();
			}
		} );
	}

	/* ---------------------------------------------------------------------
	 * Submenus (keyboard + touch + mobile accordion).
	 * ------------------------------------------------------------------ */

	doc.addEventListener( 'click', function ( event ) {
		var toggle = event.target.closest( '.submenu-toggle' );
		if ( toggle ) {
			event.preventDefault();
			var item = toggle.closest( 'li' );
			var open = ! item.classList.contains( 'is-open' );
			// Close siblings in desktop menus.
			if ( ! toggle.closest( '.menu--mobile' ) ) {
				$$( '.is-open', item.parentNode ).forEach( function ( sibling ) {
					if ( sibling !== item && sibling.parentNode === item.parentNode ) {
						sibling.classList.remove( 'is-open' );
						var btn = $( ':scope > .submenu-toggle', sibling );
						if ( btn ) {
							btn.setAttribute( 'aria-expanded', 'false' );
						}
					}
				} );
			}
			item.classList.toggle( 'is-open', open );
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			return;
		}
		if ( ! event.target.closest( '.main-navigation' ) ) {
			$$( '.main-navigation li.is-open' ).forEach( function ( item ) {
				item.classList.remove( 'is-open' );
				var btn = $( ':scope > .submenu-toggle', item );
				if ( btn ) {
					btn.setAttribute( 'aria-expanded', 'false' );
				}
			} );
		}
	} );

	doc.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' !== event.key ) {
			return;
		}
		var openItem = event.target.closest && event.target.closest( '.main-navigation li.is-open, .main-navigation li:focus-within' );
		if ( openItem ) {
			var parentItem = openItem.closest( '.menu > li' ) || openItem;
			parentItem.classList.remove( 'is-open' );
			var btn = $( ':scope > .submenu-toggle', parentItem );
			if ( btn ) {
				btn.setAttribute( 'aria-expanded', 'false' );
				btn.focus();
			}
		}
	} );

	/* ---------------------------------------------------------------------
	 * Header: scrolled / shrink / stuck states.
	 * ------------------------------------------------------------------ */

	var header = $( '#masthead' );
	var backToTop = $( '.back-to-top' );
	if ( header ) {
		var isTransparent = header.classList.contains( 'is-transparent' );
		if ( settings.shrinkHeader ) {
			header.classList.add( 'can-shrink' );
		}
		var headerTop = 0;
		var measure = function () {
			headerTop = header.getBoundingClientRect().top + window.scrollY;
		};
		measure();
		window.addEventListener( 'resize', measure );

		var ticking = false;
		var onScroll = function () {
			var y = window.scrollY;
			var scrolled = y > headerTop + header.offsetHeight;
			header.classList.toggle( 'is-scrolled', settings.stickyHeader && y > headerTop + 4 );
			if ( isTransparent && settings.stickyHeader ) {
				header.classList.toggle( 'is-stuck', scrolled );
			}
			if ( backToTop ) {
				backToTop.classList.toggle( 'is-visible', y > 700 );
			}
			ticking = false;
		};
		window.addEventListener(
			'scroll',
			function () {
				if ( ! ticking ) {
					ticking = true;
					window.requestAnimationFrame( onScroll );
				}
			},
			{ passive: true }
		);
		onScroll();
	}

	if ( backToTop ) {
		backToTop.addEventListener( 'click', function ( event ) {
			event.preventDefault();
			window.scrollTo( { top: 0, behavior: reduceMotion ? 'auto' : 'smooth' } );
			var skip = $( '.skip-link' );
			if ( skip ) {
				skip.focus( { preventScroll: true } );
			}
		} );
	}

	/* ---------------------------------------------------------------------
	 * Announcement bar.
	 * ------------------------------------------------------------------ */

	var announcement = $( '.otb-announcement' );
	if ( announcement ) {
		var key = 'otb-announce-' + announcement.getAttribute( 'data-key' );
		if ( storageGet( key ) ) {
			announcement.hidden = true;
		}
		var close = $( '.otb-announcement__close', announcement );
		if ( close ) {
			close.addEventListener( 'click', function () {
				announcement.hidden = true;
				storageSet( key, '1' );
				if ( header ) {
					window.dispatchEvent( new Event( 'resize' ) );
				}
			} );
		}
		var messages = $$( '.otb-announcement__msg', announcement );
		if ( messages.length > 1 && ! reduceMotion ) {
			var index = 0;
			var paused = false;
			announcement.addEventListener( 'mouseenter', function () {
				paused = true;
			} );
			announcement.addEventListener( 'mouseleave', function () {
				paused = false;
			} );
			window.setInterval( function () {
				if ( paused || announcement.hidden ) {
					return;
				}
				messages[ index ].hidden = true;
				messages[ index ].classList.remove( 'is-active' );
				index = ( index + 1 ) % messages.length;
				messages[ index ].hidden = false;
				messages[ index ].classList.add( 'is-active' );
			}, Math.max( 2, settings.announceSpeed || 5 ) * 1000 );
		}
	}

	/* ---------------------------------------------------------------------
	 * Hero slider.
	 * ------------------------------------------------------------------ */

	$$( '.otb-hero' ).forEach( function ( hero ) {
		var slides = $$( '.otb-hero__slide', hero );
		if ( slides.length < 2 ) {
			return;
		}
		var dots = $$( '.otb-hero__dot', hero );
		var current = 0;
		var timer = null;
		var delay = parseInt( hero.getAttribute( 'data-autoplay' ), 10 ) || 0;

		var show = function ( next ) {
			current = ( next + slides.length ) % slides.length;
			slides.forEach( function ( slide, i ) {
				var active = i === current;
				slide.classList.toggle( 'is-active', active );
				slide.setAttribute( 'aria-hidden', active ? 'false' : 'true' );
				if ( 'inert' in slide ) {
					slide.inert = ! active;
				}
			} );
			dots.forEach( function ( dot, i ) {
				dot.classList.toggle( 'is-active', i === current );
				if ( i === current ) {
					dot.setAttribute( 'aria-current', 'true' );
				} else {
					dot.removeAttribute( 'aria-current' );
				}
			} );
		};

		var stop = function () {
			window.clearInterval( timer );
			timer = null;
		};

		var start = function () {
			if ( delay && ! reduceMotion && ! timer ) {
				timer = window.setInterval( function () {
					show( current + 1 );
				}, delay * 1000 );
			}
		};

		show( 0 );
		start();

		var prev = $( '.otb-hero__prev', hero );
		var next = $( '.otb-hero__next', hero );
		if ( prev ) {
			prev.addEventListener( 'click', function () {
				stop();
				show( current - 1 );
			} );
		}
		if ( next ) {
			next.addEventListener( 'click', function () {
				stop();
				show( current + 1 );
			} );
		}
		dots.forEach( function ( dot, i ) {
			dot.addEventListener( 'click', function () {
				stop();
				show( i );
			} );
		} );
		hero.addEventListener( 'mouseenter', stop );
		hero.addEventListener( 'focusin', stop );
		hero.addEventListener( 'mouseleave', start );

		var touchX = null;
		hero.addEventListener( 'touchstart', function ( event ) {
			touchX = event.touches[ 0 ].clientX;
		}, { passive: true } );
		hero.addEventListener( 'touchend', function ( event ) {
			if ( null === touchX ) {
				return;
			}
			var delta = event.changedTouches[ 0 ].clientX - touchX;
			if ( Math.abs( delta ) > 50 ) {
				stop();
				show( current + ( delta < 0 ? 1 : -1 ) );
			}
			touchX = null;
		} );
	} );

	/* ---------------------------------------------------------------------
	 * Product carousels and tabs.
	 * ------------------------------------------------------------------ */

	function initCarousel( wrap ) {
		var track = $( 'ul.products', wrap );
		var prev = $( '.otb-carousel__prev', wrap );
		var next = $( '.otb-carousel__next', wrap );
		if ( ! track || ! prev || ! next ) {
			return;
		}
		var update = function () {
			var max = track.scrollWidth - track.clientWidth - 2;
			prev.disabled = track.scrollLeft <= 2;
			next.disabled = track.scrollLeft >= max;
			$( '.otb-carousel__nav', wrap ).hidden = max <= 0;
		};
		var step = function ( direction ) {
			var item = $( 'li.product', track );
			var distance = item ? item.getBoundingClientRect().width + 24 : track.clientWidth;
			track.scrollBy( { left: direction * distance, behavior: reduceMotion ? 'auto' : 'smooth' } );
		};
		prev.addEventListener( 'click', function () {
			step( -1 );
		} );
		next.addEventListener( 'click', function () {
			step( 1 );
		} );
		track.addEventListener( 'scroll', update, { passive: true } );
		window.addEventListener( 'resize', update );
		update();
	}

	$$( '.otb-carousel' ).forEach( function ( wrap ) {
		if ( ! wrap.hidden ) {
			initCarousel( wrap );
		}
	} );

	$$( '.otb-tabs' ).forEach( function ( list ) {
		var tabs = $$( '[role="tab"]', list );
		var activate = function ( tab ) {
			tabs.forEach( function ( other ) {
				var active = other === tab;
				other.classList.toggle( 'is-active', active );
				other.setAttribute( 'aria-selected', active ? 'true' : 'false' );
				other.tabIndex = active ? 0 : -1;
				var panel = doc.getElementById( other.getAttribute( 'aria-controls' ) );
				if ( panel ) {
					panel.hidden = ! active;
					if ( active && panel.classList.contains( 'otb-carousel' ) && ! panel.dataset.ready ) {
						panel.dataset.ready = '1';
						initCarousel( panel );
					}
					if ( active ) {
						$$( '.reveal', panel ).forEach( function ( el ) {
							el.classList.add( 'is-visible' );
						} );
					}
				}
			} );
		};
		tabs.forEach( function ( tab, i ) {
			tab.addEventListener( 'click', function () {
				activate( tab );
			} );
			tab.addEventListener( 'keydown', function ( event ) {
				var target = null;
				if ( 'ArrowRight' === event.key ) {
					target = tabs[ ( i + 1 ) % tabs.length ];
				} else if ( 'ArrowLeft' === event.key ) {
					target = tabs[ ( i - 1 + tabs.length ) % tabs.length ];
				}
				if ( target ) {
					event.preventDefault();
					target.focus();
					activate( target );
				}
			} );
		} );
	} );

	/* ---------------------------------------------------------------------
	 * Reveal on scroll.
	 * ------------------------------------------------------------------ */

	var reveals = $$( '.reveal, .otb-section ul.products li.product' );
	if ( settings.animations && 'IntersectionObserver' in window && ! reduceMotion ) {
		reveals.forEach( function ( el ) {
			el.classList.add( 'reveal' );
		} );
		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'is-visible' );
						observer.unobserve( entry.target );
					}
				} );
			},
			{ rootMargin: '0px 0px -8% 0px', threshold: 0.08 }
		);
		reveals.forEach( function ( el, i ) {
			el.style.transitionDelay = ( i % 4 ) * 70 + 'ms';
			observer.observe( el );
		} );
	} else {
		reveals.forEach( function ( el ) {
			el.classList.add( 'is-visible' );
		} );
	}

	/* ---------------------------------------------------------------------
	 * Gallery lightbox.
	 * ------------------------------------------------------------------ */

	var lightboxLinks = $$( '[data-otb-lightbox]' );
	if ( lightboxLinks.length && 'HTMLDialogElement' in window ) {
		var dialog = doc.createElement( 'dialog' );
		dialog.className = 'otb-lightbox';
		dialog.innerHTML = '<button type="button" class="icon-btn otb-lightbox__close">&times;</button><img alt="">';
		$( '.otb-lightbox__close', dialog ).setAttribute( 'aria-label', ( settings.i18n && settings.i18n.close ) || 'Close' );
		body.appendChild( dialog );
		var image = $( 'img', dialog );
		$( '.otb-lightbox__close', dialog ).addEventListener( 'click', function () {
			dialog.close();
		} );
		dialog.addEventListener( 'click', function ( event ) {
			if ( event.target === dialog ) {
				dialog.close();
			}
		} );
		lightboxLinks.forEach( function ( link ) {
			link.addEventListener( 'click', function ( event ) {
				event.preventDefault();
				var thumb = $( 'img', link );
				image.src = link.getAttribute( 'href' );
				image.alt = thumb ? thumb.alt : '';
				dialog.showModal();
			} );
		} );
	}

	/* ---------------------------------------------------------------------
	 * WooCommerce: quantity buttons.
	 * ------------------------------------------------------------------ */

	function addQuantityButtons( context ) {
		$$( '.quantity', context ).forEach( function ( wrap ) {
			var input = $( 'input.qty', wrap );
			if ( ! input || wrap.classList.contains( 'otb-qty' ) || 'number' !== input.type || input.readOnly ) {
				return;
			}
			wrap.classList.add( 'otb-qty' );
			var minus = doc.createElement( 'button' );
			var plus = doc.createElement( 'button' );
			minus.type = plus.type = 'button';
			minus.className = 'otb-qty__btn otb-qty__minus';
			plus.className = 'otb-qty__btn otb-qty__plus';
			minus.setAttribute( 'aria-label', ( settings.i18n && settings.i18n.qtyMinus ) || '−' );
			plus.setAttribute( 'aria-label', ( settings.i18n && settings.i18n.qtyPlus ) || '+' );
			minus.textContent = '−';
			plus.textContent = '+';
			input.parentNode.insertBefore( minus, input );
			input.parentNode.insertBefore( plus, input.nextSibling );

			var change = function ( direction ) {
				var step = parseFloat( input.step ) || 1;
				var min = '' === input.min ? 0 : parseFloat( input.min );
				var max = '' === input.max ? Infinity : parseFloat( input.max );
				var value = ( parseFloat( input.value ) || 0 ) + direction * step;
				value = Math.min( max, Math.max( min, value ) );
				input.value = value;
				input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
				if ( window.jQuery ) {
					window.jQuery( input ).trigger( 'change' );
				}
			};
			minus.addEventListener( 'click', function () {
				change( -1 );
			} );
			plus.addEventListener( 'click', function () {
				change( 1 );
			} );
		} );
	}

	addQuantityButtons( doc );
	if ( window.jQuery ) {
		window.jQuery( body ).on( 'updated_wc_div updated_cart_totals wc_fragments_refreshed', function () {
			addQuantityButtons( doc );
		} );
	}

	/* ---------------------------------------------------------------------
	 * WooCommerce: skein calculator.
	 * ------------------------------------------------------------------ */

	$$( '.otb-skein-calc' ).forEach( function ( calc ) {
		var input = $( 'input', calc );
		var unit = $( 'select', calc );
		var result = $( '.otb-skein-calc__result', calc );
		var yards = parseFloat( calc.getAttribute( 'data-yards' ) ) || 0;
		var meters = parseFloat( calc.getAttribute( 'data-meters' ) ) || 0;
		var i18n = settings.i18n || {};
		var update = function () {
			var need = parseFloat( input.value ) || 0;
			var per = 'm' === unit.value ? meters : yards;
			if ( need <= 0 || per <= 0 ) {
				result.textContent = '';
				return;
			}
			var skeins = Math.ceil( need / per );
			result.textContent = 1 === skeins ? i18n.skein : ( i18n.skeins || '%s' ).replace( '%s', skeins );
			var qty = $( 'form.cart input.qty' );
			if ( qty && ! qty.readOnly ) {
				qty.value = skeins;
				qty.dispatchEvent( new Event( 'change', { bubbles: true } ) );
			}
		};
		input.addEventListener( 'input', update );
		unit.addEventListener( 'change', update );
	} );

	/* ---------------------------------------------------------------------
	 * WooCommerce: sticky add to cart.
	 * ------------------------------------------------------------------ */

	var stickyBar = $( '.otb-sticky-atc' );
	var cartForm = $( 'form.cart' );
	if ( stickyBar && cartForm && 'IntersectionObserver' in window ) {
		body.classList.add( 'otb-has-sticky-atc' );
		var formObserver = new IntersectionObserver( function ( entries ) {
			var entry = entries[ 0 ];
			var past = ! entry.isIntersecting && entry.boundingClientRect.top < 0;
			stickyBar.hidden = ! past;
		} );
		formObserver.observe( cartForm );

		$( '.otb-sticky-atc__btn', stickyBar ).addEventListener( 'click', function () {
			var realButton = $( '.single_add_to_cart_button', cartForm );
			var isSimple = ! cartForm.classList.contains( 'variations_form' ) && ! cartForm.classList.contains( 'grouped_form' );
			if ( isSimple && realButton && ! realButton.disabled ) {
				realButton.click();
				return;
			}
			cartForm.scrollIntoView( { behavior: reduceMotion ? 'auto' : 'smooth', block: 'center' } );
			var firstField = $( 'select, input:not([type="hidden"])', cartForm );
			if ( firstField ) {
				window.setTimeout( function () {
					firstField.focus( { preventScroll: true } );
				}, 400 );
			}
		} );
	}
} )();
