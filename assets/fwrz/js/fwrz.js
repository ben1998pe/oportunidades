'use strict';

const fwrz_conditionals = {
	array_sum: function( accumulator, a ){
		return accumulator+a;
	},
	reload: function(){
		/*
		 * Set data = {relation: OR, condition: {parent, condition, value} }
		 */
		document.querySelectorAll('.fwrz__row[data-conditions]').forEach(form_row => {
			let conditions = JSON.parse(form_row.dataset.conditions),
				completed = new Array(),
				relation = form_row.dataset.relation === undefined ? 'AND' : form_row.dataset.relation;

			let group = form_row.closest('.fwrz__row-repeater');

			conditions.forEach( (condition, index) => {
				function set_condition( condition ){
					let current = 'document.querySelectorAll(\'.fwrz__row [data-parent="' + condition[0] + '"]\')[0].value';

					if( group !== null ){
						current = "group.querySelectorAll('.fwrz__row [data-parent=\"" + condition[0] + "\"]')[0].value";
					}

					try {
						console.log( document.querySelectorAll('.fwrz__row [data-parent="' + condition[0] + '"]')[0].tagName );
						if( 'LABEL' == document.querySelectorAll('.fwrz__row [data-parent="' + condition[0] + '"]')[0].tagName ){
							if( document.querySelectorAll('.fwrz__row [data-parent="' + condition[0] + '"]').first().classList.contains('checkbox') ){
								let checked = document.querySelectorAll('.fwrz__row [data-parent="' + condition[0] + '"]').first().querySelector('input[type=checkbox]').checked ? 1 : 0;
								completed[ index ] = eval( checked + ' ' + condition[1] + ' "' + condition[2] + '"' ) ? 1 : 0;
							}
						} else {
							completed[ index ] = eval( current + ' ' + condition[1] + ' "' + condition[2] + '"' );
						}
					} catch (error) {}

					if( 
						( 'OR' == relation && completed.reduce( fwrz_conditionals.array_sum, 0 ) > 0 ) ||
						( 'AND' == relation && completed.reduce( fwrz_conditionals.array_sum, 0 ) == conditions.length )
					){
						form_row.classList.remove('hidden');
						if( group !== null ){
							form_row.querySelectorAll('input, select, textarea').forEach(input => {
								input.removeAttribute('disabled');
							})
						}
					} else {
						form_row.classList.add('hidden');
						if( group !== null ){
							form_row.querySelectorAll('input, select, textarea').forEach(input => {
								input.disabled = 'disabled';
							})
						}
					}
				}

				document.addEventListener('change', evt => {
					if( evt.target.closest('.fwrz__row [data-parent="'+ condition[0] +'"]') ){
						set_condition(condition);
					}
				})

				set_condition(condition);
			})
		})

	},
	init: function(){
		fwrz_conditionals.reload();
	}
};

const fwrz_template = {
	get: (obj, path, def) => {

		/**
		 * If the path is a string, convert it to an array
		 * @param  {String|Array} path The path
		 * @return {Array}             The path array
		 */
		var stringToPath = function (path) {
	
			// If the path isn't a string, return it
			if (typeof path !== 'string') return path;
	
			// Create new array
			var output = [];
	
			// Split to an array with dot notation
			path.split('.').forEach(function (item) {
	
				// Split to an array with bracket notation
				item.split(/\[([^}]+)\]/g).forEach(function (key) {
	
					// Push to the new array
					if (key.length > 0) {
						output.push(key);
					}
	
				});
	
			});
	
			return output;
	
		};
	
		// Get the path as an array
		path = stringToPath(path);
	
		// Cache the current object
		var current = obj;
	
		// For each item in the path, dig into the object
		for (var i = 0; i < path.length; i++) {
	
			// If the item isn't found, return the default (or null)
			if (!current[path[i]]) return def;
	
			// Otherwise, update the current  value
			current = current[path[i]];
	
		}
	
		return current;
	
	},
	render: (template, data) => {
		'use strict';
	
		// Check if the template is a string or a function
		template = typeof (template) === 'function' ? template() : template;
		if (['string', 'number'].indexOf(typeof template) === -1) throw 'PlaceholdersJS: please provide a valid template';
	
		// If no data, return template as-is
		if (!data) return template;
	
		// Replace our curly braces with data
		template = template.replace(/\{\{([^}]+)\}\}/g, function (match) {
	
			// Remove the wrapping curly braces
			match = match.slice(2, -2);
	
			// Get the value
			var val = fwrz_template.get(data, match.trim());
	
			// Replace
			if (!val) return '{{' + match + '}}';
			return val;
	
		});
	
		return template;
	}
}

const fwrz = {
	setup_tabs: function()
	{
		document.querySelectorAll('.fwrz__tabs a').forEach(tab => {
			tab.addEventListener('click', evt => {
				evt.preventDefault();

				let parent = tab.closest('.fwrz'),
					target = tab.dataset.tab;

				// TABS
				parent.querySelectorAll('.fwrz__tabs li.active, .fwrz__content > :not([data-tab="' + target + '"])').forEach(item => {
					item.classList.remove('active');
				})
				tab.closest('li').classList.add('active');
				parent.querySelectorAll('.fwrz__content > [data-tab="' + target + '"]').forEach(item => {
					item.classList.add('active');
				})
			})
		})
	},
	setup_toggle: () => {
		document.addEventListener('click', evt => {
			if( evt.target.classList.contains('fwrz__row-field-toggle-head') || (evt.target.tagName=='SPAN' && evt.target.closest('.fwrz__row-field-toggle-head')) ){
				evt.preventDefault();

				evt.target.closest('.fwrz__row-field-toggle').classList.toggle('active');
			}
		})

		document.addEventListener('change', evt => {
			if( evt.target.closest('.fwrz__row-field-toggle') ){
				let title_span = evt.target.closest('.fwrz__row-field-toggle').querySelector('.fwrz__row-field-toggle-head span');
				
				if( evt.target.dataset.parent == title_span.dataset.rel )
					title_span.innerHTML = evt.target.value.trim();
			}
		})

		document.querySelectorAll('.fwrz__row-field-toggle .fwrz__row-field-toggle-head span[data-rel]').forEach(title_span => {
			try {
				let current = title_span.closest('.fwrz__row-field-toggle').querySelector('[data-parent="' + title_span.dataset.rel + '"]');
				title_span.innerHTML = current.value.trim();
			} catch (error) {}
		})
	},
	setup_wpeditor: function(){
		document.querySelectorAll('[data-element="wpeditor"]:not([data-load="lazy"])').forEach( elm => {
			wp.editor.initialize( elm.id, {
				tinymce: {
					wpautop: true,
					textarea_rows: elm.rows ? parseInt(elm.rows) : 5,
				}, quicktags: true, mediaButtons: true
			} );
		})

		// LAZY WPEDITOR
		document.addEventListener('click', evt => {
			let element = evt.target;

			if( element.dataset.element == 'wpeditor' && element.closest('.wp-editor-container') == null ){
				element.removeAttribute('reandonly');
				wp.editor.initialize( element.id, {
					tinymce: {
						wpautop: true,
						textarea_rows: element.rows ? parseInt(element.rows) : 5,
					}, quicktags: true, mediaButtons: true
				} );
			}
		})
	},
	setup_image: function(){
		document.querySelectorAll('.fwrz-input-image').forEach(elm => {
			elm.querySelectorAll('[data-action="choose"]').forEach(btn => {

				btn.addEventListener('click', evt => {
					evt.preventDefault();

					let wp_media_frame;

					if ( typeof wp === 'undefined' || ! wp.media || ! wp.media.gallery ) return;

					let input = elm.querySelector('input[type="hidden"]'),
						type = btn.dataset.type ? btn.dataset.type : 'image';

					wp_media_frame = wp.media({multiple: false, type: type});

					if ( wp_media_frame ) wp_media_frame.open();

					if( input.value != '' ){
						var selection = wp_media_frame.state().get('selection');
						selection.add( wp.media.attachment(input.value) );
					}

					wp_media_frame.on( 'select', function() {
						let attachment = wp_media_frame.state().get('selection').first(),
							figure = elm.querySelector('figure'),
							img = elm.querySelector('figure img');

						elm.classList.add('selected');
						elm.dataset.id = input.value = attachment.attributes.id;

						img.src = (/^image\//gm.test(attachment.attributes.mime)) ? attachment.attributes.url : attachment.attributes.icon;

						if(/^image\//gm.test(attachment.attributes.mime)){
							figure.classList.remove('non-image');
						} else {
							figure.classList.add('non-image');
							figure.querySelector('figcaption').innerText = attachment.attributes.filename;
						}
					});
				})
			})

			elm.querySelectorAll('[data-action="remove"]').forEach(a => {
				a.addEventListener('click', event => {
					event.preventDefault();
					elm.classList.remove('selected');
				})
			})
		})
	},
	setup_gallery: function(){
		document.querySelectorAll('.fwrz-input-gallery').forEach(elm => {
			elm.querySelectorAll('.fwrz-input-gallery-item [data-action="remove"]').forEach(a => {
				a.addEventListener('click', event => {
					event.preventDefault();
					a.closest('.fwrz-input-gallery-item').remove();
				})
			})

			elm.querySelectorAll('[data-action="choose"]').forEach(btn => {

				btn.addEventListener('click', evt => {
					evt.preventDefault();

					let wp_media_frame;

					if ( typeof wp === 'undefined' || ! wp.media || ! wp.media.gallery ) return;

					wp_media_frame = wp.media({multiple: 'add', type: 'image'});

					if ( wp_media_frame ) {
						wp_media_frame.open();
					}

					wp_media_frame.on( 'select', function() {
						let body = elm.querySelector('.fwrz-input-gallery__main--body');

						// append
						wp_media_frame.state().get('selection').forEach(item => {
							if( elm.querySelector('.fwrz-input-gallery-item input[type=hidden][value="' + item.attributes.id + '"]') == null ){

								body.innerHTML += '<li class="fwrz-input-gallery-item" data-id="' + item.attributes.id + '">'
									+ '<a href="javascript:void(0);" class="fwrz-input-gallery-item__close" data-action="remove">Remove</a>'
									+ '<input type="hidden" value="' + item.attributes.id + '" name="' + elm.dataset.name + '[]" />'
									+ '<figure><img src="' + item.attributes.url + '" loading="lazy" /></figure>'
									+ '</li>';
							}						
						})

						elm.querySelectorAll('.fwrz-input-gallery-item [data-action="remove"]').forEach(a => {
							a.addEventListener('click', event => {
								event.preventDefault();
								a.closest('.fwrz-input-gallery-item').remove();
							})
						})
					});
				})
			})
		})

		document.querySelectorAll('.fwrz-input-gallery__main--body').forEach(elm => {
			Sortable.create(elm);
		})
		
	},
	setup_calls: function()
	{
		jQuery( '.form-row select.select2' ).each(function(){
			let select = jQuery( this );
			let args = {};

			if( undefined !== select.data('rnz-call') ){
				jQuery.extend( args, {
					ajax: {
						url: rnz_wcups_conds.ajax_url,
						dataType: 'json',
						delay: 250,
						data: function( params ){
							let query = {
								action: 'rnz-wcups',
								do: select.data('rnz-call'),
								q: params.term
							};

							return query;
						},
						processResults: function( data, params ){
							if( data.success )
								return { results: data.data.products };
							
							return { results: [] };
						}
					},
					minimumInputLength: 3
				} );
			}
			
			jQuery( this ).select2( args );
		});
	},
	setup_repeater: function(){
		document.addEventListener('click', evt => {
			if( evt.target.closest('.fwrz__row-repeater') && (evt.target).dataset.fwrzAction ){
				evt.preventDefault();

				let parent = evt.target.closest('.fwrz__row-repeater');

				switch( (evt.target).dataset.fwrzAction ){
					case 'clone':
						let template = fwrz_template.render( parent.querySelector('script.tmpl-repeater').innerHTML, {
							key: fwrz.uniqid()
						});

						if( parent.querySelectorAll('.fwrz__row-repeater--group').length == 0 ){
							parent.querySelector('script.tmpl-repeater').outerHTML += template;
						} else {
							parent.querySelector('.fwrz__row-repeater--footer').outerHTML = template + parent.querySelector('.fwrz__row-repeater--footer').outerHTML;
						}
						
						fwrz_conditionals.init();
					break;
					case 'remove':
						(evt.target).closest('.fwrz__row-repeater--group').remove();
					break;
				}
			}
		})

		document.querySelectorAll('.fwrz__row-repeater').forEach(elm => {
			Sortable.create(elm, {
				draggable: '.fwrz__row-repeater--group',
				handle: '.fwrz__row-field-toggle'
			});
		})
		
	},
	reload: function()
	{
		fwrz.setup_tabs();
		fwrz.setup_repeater();
		fwrz.setup_toggle();
		fwrz.setup_wpeditor();
		fwrz.setup_image();
		fwrz.setup_gallery();
		
	},
	uniqid: () => {
		let output = String( Date.now().toString(32) + Math.random().toString(16) ).replace(/\./g, '');
		let start = Math.floor(Math.random() * 6);

		return output.substring(start, start + 12);
	},
}


document.addEventListener('DOMContentLoaded', function(){
	fwrz_conditionals.init();
	fwrz.reload();
});