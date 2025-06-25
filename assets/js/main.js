const rnz = {
	slideUp: (target, cb, duration=500) => {
		target.style.transitionProperty = 'height, margin, padding';
		target.style.transitionDuration = duration + 'ms';
		target.style.boxSizing = 'border-box';
		target.style.height = target.offsetHeight + 'px';
		target.offsetHeight;
		target.style.overflow = 'hidden';
		target.style.height = 0;
		target.style.paddingTop = 0;
		target.style.paddingBottom = 0;
		target.style.marginTop = 0;
		target.style.marginBottom = 0;
		window.setTimeout( () => {
			target.style.display = 'none';
			target.style.removeProperty('height');
			target.style.removeProperty('padding-top');
			target.style.removeProperty('padding-bottom');
			target.style.removeProperty('margin-top');
			target.style.removeProperty('margin-bottom');
			target.style.removeProperty('overflow');
			target.style.removeProperty('transition-duration');
			target.style.removeProperty('transition-property');
			//alert("!");

			if( typeof cb == 'function' ) cb(target, 'up');
		}, duration);
	},
	slideDown: (target, cb, duration=500, new_display='block') => {
		target.style.removeProperty('display');
		let display = window.getComputedStyle(target).display;
	
		if (display === 'none')
		  display = new_display;
	
		target.style.display = display;
		let height = target.offsetHeight;
		target.style.overflow = 'hidden';
		target.style.height = 0;
		target.style.paddingTop = 0;
		target.style.paddingBottom = 0;
		target.style.marginTop = 0;
		target.style.marginBottom = 0;
		target.offsetHeight;
		target.style.boxSizing = 'border-box';
		target.style.transitionProperty = "height, margin, padding";
		target.style.transitionDuration = duration + 'ms';
		target.style.height = height + 'px';
		target.style.removeProperty('padding-top');
		target.style.removeProperty('padding-bottom');
		target.style.removeProperty('margin-top');
		target.style.removeProperty('margin-bottom');
		window.setTimeout( () => {
			target.style.removeProperty('height');
			target.style.removeProperty('overflow');
			target.style.removeProperty('transition-duration');
			target.style.removeProperty('transition-property');

			if( typeof cb == 'function' ) cb(target, 'down');
		}, duration);
	},
	slideToggle: (target, cb, duration = 500, new_display='block') => {
		if (window.getComputedStyle(target).display === 'none') {
		  return rnz.slideDown(target, cb, duration, new_display);
		} else {
		  return rnz.slideUp(target, cb, duration);
		}
	},
	fadeIn: (target, cb, duration = 5000) => {
		let display = window.getComputedStyle(target).display;
		if (display === 'none') display = 'block';

		target.style.opacity = '0';			
		target.style.display = display;
		target.style.transitionProperty = 'opacity';
		target.style.transitionDuration = '500ms';
		
		window.setTimeout(() => {
			target.style.opacity = '1';
		}, duration);

		target.addEventListener('transitionend', event => {
			if (event.propertyName === 'opacity')
				if (typeof cb === 'function') cb(target, 'in');
		});
	},
	fadeOut: (target, cb, duration = 5000) => {
		target.style.opacity = '1';
		target.style.transitionProperty = "opacity";
		target.style.transitionDuration = '500ms';

		window.setTimeout(() => {
			target.style.opacity = '0';
		}, duration);

		target.addEventListener('transitionend', event => {
			target.style.display = 'none';
			if (event.propertyName === 'opacity')
				if (typeof cb === 'function') cb(target, 'out');
		});
	},
	fadeToggle: (target, cb, duration = 5000) => {
		let currentOpacity = parseFloat(target.style.opacity);  
		(currentOpacity === 1) ? rnz_transition.fadeOut(target, cb, duration) : rnz_transition.fadeIn(target, cb, duration);
	},
	header: () => {
		const header = document.getElementById('header');
		if (header) {
		let height = header.clientHeight;
		if( window.pageYOffset > height ){
			header.classList.add('scrolled');
			document.body.style.marginTop = height + 'px';
		} else {
			header.classList.remove('scrolled');
			document.body.style.marginTop = 0;
		}

		}
		
	},
	mobileMenu: () => {
		document.querySelectorAll('#menu-mobile .mobile-panel__header').forEach(elm => {
			elm.style.height = document.getElementById('header').clientHeight + 'px';
		})
		document.querySelectorAll('#menu-mobile li.menu-item-has-children > a').forEach(menu_link => {
			menu_link.addEventListener('click', evt => {
				evt.preventDefault();
				evt.target.closest('li').querySelector('.mobile-panel').classList.add('active');
			})
		})
		document.querySelectorAll('#menu-mobile .mobile-panel__header a[data-action="close-panel"]').forEach(link => {
			link.addEventListener('click', evt => {
				evt.preventDefault();
				evt.target.closest('.mobile-panel').classList.remove('active')
			})
		})
		document.querySelectorAll('#menu-mobile .mobile-panel__header a[data-action="close-panels"]').forEach(link => {
			link.addEventListener('click', evt => {
				evt.preventDefault();
				document.querySelectorAll('#menu-mobile .mobile-panel.active').forEach(panel => {
					panel.classList.remove('active')
				})
			})
		})
	}
}

document.addEventListener('touchmove', evt => {
	evt = evt.originalEvent || evt;
	if( evt.scale !== undefined && evt.scale !== 1) evt.preventDefault();
}, false);

document.addEventListener('DOMContentLoaded', () => {

	window.addEventListener('resize', () => {rnz.header()})
	window.addEventListener('scroll', () => {rnz.header()})

	rnz.header();
	rnz.mobileMenu();

	document.querySelectorAll('.mm-login-tabs li a').forEach(link => {
		let tab = document.getElementById(link.dataset.tab), li = link.closest('li');
		if( !li.classList.contains('active') )
			tab.classList.add('hidden');
		
		link.addEventListener('click', evt => {
			evt.preventDefault();
			document.querySelectorAll('.mm-login-tabs li.active').forEach(item => {
				item.classList.remove('active');document.getElementById(item.querySelector('a').dataset.tab).classList.add('hidden')
			});

			tab.classList.remove('hidden');li.classList.add('active');
		})
	})

	document.querySelectorAll('.toggle-search').forEach(link => {
		link.addEventListener('click', evt => {
			evt.preventDefault();
			
			document.querySelector('.op-form').classList.toggle('hidden');
			document.querySelector('.nav-main').classList.toggle('hidden');
		})
	})

	document.querySelectorAll('#header .has-megamenu, #header .has-megamenu > a').forEach(link => {
		link.addEventListener('mouseenter', evt => {
			if( !window.matchMedia('(min-width:1000px)').matches ) return;

			const container = link.closest('.row');
			const child = link.tagName == 'A' ? link.closest('li').querySelector('.megamenu-wrap') : link.querySelector('.megamenu-wrap');

			const containerRect = container.getBoundingClientRect();
			const containerWidth = containerRect.width;
			const containerLeft = containerRect.left;
			const containerRight = containerRect.right;

			// Get the child element dimensions
			const childRect = child.getBoundingClientRect();
			const childWidth = childRect.width;
			const childLeft = childRect.left;
			const childRight = childRect.right;

			// Check if the child element overflows outside the container
			if (childLeft < containerLeft) {
			// Adjust the child's left position to fit inside the container
				child.style.left = (containerLeft - childLeft) + 'px';
			} else if (childRight > containerRight) {
			// Adjust the child's right position to fit inside the container
				child.style.right = (containerRight - childRight) + 'px';
			}
		})
	})

	document.querySelectorAll('.op-form select, .rfilters-form select, .oportunity-search__form select').forEach(select => {
    // Evitar que se aplique a #sector
    if (select.id === 'region_born') return;
    if (select.id === 'province_born') return;
    if (select.id === 'city_born') return;

    let choices = new Choices(select, {itemSelectText: 'Selecciona', shouldSort: false});

       const removeSearchName = () => {
        const inputSearch = select.closest('.choices').querySelector('input[type="search"]');
        if (inputSearch && inputSearch.name === 'search_terms') {
            inputSearch.removeAttribute('name');
        }
    };

    removeSearchName(); // al iniciar

    const choices_alternate = () => {
        if (
            (window.matchMedia('(max-width:676px)').matches && select.classList.contains('no-mobile')) ||
            (window.matchMedia('(min-width:676px) and (max-width:1100px)').matches && select.classList.contains('no-tablet')) ||
            (window.matchMedia('(min-width:1100px)').matches && select.classList.contains('no-desktop'))
        ) {
            try {
                choices.destroy();
            } catch (ex) {}
        } else {
            try {
                choices.init();
            } catch (ex) {}
        }
    }

    choices_alternate();

    window.addEventListener('resize', evt => {
        choices_alternate();
    });
});


	document.addEventListener('click', evt => {
		if( evt.target.matches('.toggle-opsearch') ){
			evt.preventDefault();
			document.querySelector('.opintro').classList.toggle('hidden');
			document.querySelector('.opform').classList.toggle('hidden');
		}
	})

	document.querySelectorAll('.share-select select').forEach(select => {
		let choices = new Choices(select, {itemSelectText: '',searchEnabled:false,shouldSort:false});

		choices.passedElement.element.addEventListener('change', evt => {
			let selectedOption = choices.getValue().value;
			let current_url = select.closest('.share-select').dataset.url;

			console.log(selectedOption);

			switch(selectedOption){
				case 'clipboard':
					let tempInput = document.createElement('input');
					tempInput.setAttribute('value', current_url);
					document.body.appendChild(tempInput);
					tempInput.select();
					document.execCommand('copy');
					document.body.removeChild(tempInput);
				break;
				case 'linkedin':
					window.open('https://www.linkedin.com/shareArticle?mini=true&url=' + encodeURIComponent(current_url), '_blank');
				break;
				case 'whatsapp':
					window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(current_url), '_blank');
				break;
				case 'email':
					var emailSubject = 'Echa un vistazo a este enlace';
					var emailBody = 'Hola,\n\nMira este enlace: ' + current_url;
					window.open('mailto:?subject=' + emailSubject + '&body=' + emailBody, '_blank');
				break;
			}
			
			choices.setChoiceByValue(['']);
		})
	})

	document.querySelectorAll('[data-element="carousel"]').forEach(elm => {
		let slider = tns({
			container: elm.querySelector('.carousel'),
			items: elm.dataset.items !== undefined ? parseInt(elm.dataset.items) : 1,
			gutter: elm.dataset.gap !== undefined ? parseInt(elm.dataset.gap) : 0,
			slideBy: elm.dataset.slideby !== undefined ? elm.dataset.slideby : 1,
			nav: true,
			controls: false,
			mouseDrag: true,
			loop: false,
			edgePadding:30,
			responsive: {
				0: {
					gutter:2,
					items: 1
				},
				640: {
				  gutter: 20,
				  items: 2
				},
				700: {
				  gutter: 30
				},
				900: {
				  items: 3
				}
			}
		})

		
	})

	let tabClick = evt => {
		if( evt.target.tagName === 'A' && evt.target.closest('.rtabs__list') ){
			evt.preventDefault();

			let parent = evt.target.closest('.rtabs'),
				target = evt.target.href.split('#')[1];

			try {
				// TAB
				parent.querySelectorAll('li').forEach(li => {
					li.classList.remove('active')
				})
				evt.target.closest('li').classList.add('active');

				// TAB CONTENT
				parent.querySelectorAll('.rtabs__content-tab').forEach(div => {
					div.classList.remove('active')

					if( div.dataset.id == target ) div.classList.add('active')
				})
			} catch (error) {}
		}
		
		if( (evt.target.tagName === 'H3' && evt.target.classList.contains('rtlist__item-title') ) && evt.target.closest('.rtlist') ){
			evt.preventDefault();

			let parent = evt.target.closest('.rtlist__item');

			rnz.slideToggle( parent.querySelectorAll('.rtlist__item-content')[0], ( elm, type) => {
				if( 'up' == type ) elm.closest('.rtlist__item').classList.remove('active'); else elm.closest('.rtlist__item').classList.add('active');
			}, 300 );
		}
	}
	document.addEventListener('click', evt => {
		tabClick(evt)
	})
	document.querySelectorAll('.rtabs__list a').forEach(link => {
		link.addEventListener('click', evt => {tabClick(evt)})
	})

	document.querySelectorAll('.rtabs-select select').forEach(select => {
		let choices = new Choices(select, {itemSelectText: '',searchEnabled:false,shouldSort:false});

		choices.passedElement.element.addEventListener('change', evt => {
			let selectedOption = choices.getValue().value;
			select.closest('.rtabs').querySelector('.rtabs__list a[href="#' + selectedOption + '"]').dispatchEvent(new Event('click'));
		})

		const choices_alternate = () => {
			if( 
				( window.matchMedia('(max-width:676px)').matches && select.classList.contains('no-mobile') )
				|| ( window.matchMedia('(min-width:676px) and (max-width:1100px)').matches && select.classList.contains('no-tablet') )
				|| ( window.matchMedia('(min-width:1100px)').matches && select.classList.contains('no-desktop') )
			){
				try{choices.destroy()} catch(ex){}
			} else {
				try{choices.init()} catch(ex){}
			}
		}

		choices_alternate();
	})

	document.querySelectorAll('.fwidget.widget_nav_menu .fwidget__title').forEach(link => {
		link.addEventListener('click', evt => {
			evt.preventDefault();

			if( window.matchMedia('(max-width:676px)').matches ){
				rnz.slideToggle( link.closest('.fwidget').querySelector('.fwidget__content'), (elm, type) => {
					('up' == type) ? elm.closest('.fwidget').classList.remove('active') : elm.closest('.fwidget').classList.add('active');
				})
			}
		})
	})

	try {
		document.querySelectorAll('input.intl').forEach(input => {
			window.intlTelInput(input, {
				initialCountry: 'pe',
				separateDialCode: false
			})

			function tel_getNumber(){
				let iti = window.intlTelInputGlobals.getInstance(input),
					country = iti.getSelectedCountryData();

				input.closest('form').querySelectorAll('[name*="celular_full"]').forEach(output => {
					output.value = iti.getNumber();
				})

				input.closest('form').querySelectorAll('[name*="pais"]').forEach(output => {
					output.value = country.name;
				})
			}

			input.addEventListener('keyup', evt => {tel_getNumber();})
			input.addEventListener('change', evt => {tel_getNumber();})
			input.addEventListener('countrychange', () => {tel_getNumber();})
		})

		document.querySelectorAll('input.intl2').forEach(input => {
			window.intlTelInput(input, {
				initialCountry: 'pe',
				autoInsertDialCode: true,
				formatOnDisplay: true,
				nationalMode: true
			})

			const telCountry = () => {
				let iti = window.intlTelInputGlobals.getInstance(input), country_code = iti.getSelectedCountryData().dialCode, number_with_code = iti.getNumber();
				let mod_number = "" + number_with_code.substr(0, country_code.length + 1) + "" + number_with_code.substr(country_code.length + 1);

				input.value = mod_number;
			}

			input.addEventListener('countrychange', () => {telCountry()})
			input.addEventListener('blur', () => {telCountry()})
			input.addEventListener('change', () => {telCountry()})	
		})
	} catch (error) {
		console.log(error);
	}

	document.addEventListener('click', evt => {
		if( evt.target.matches('.plans-block .expand') ) evt.target.closest('.plans-block').classList.add('active')
	})

	document.addEventListener('keypress', evt => {
		if( evt.target.matches('.modal__download input[name=phone]') ){
			let keyCode = evt.keyCode || evt.which;
  			let char = String.fromCharCode(keyCode);
			let allowedChars = "0123456789-+()";
		
			if (allowedChars.indexOf(char) === -1) {
				evt.preventDefault();
				return false;
			}
			
			return true;
		}
	})

	if( typeof StickySidebar !== 'undefined' ){
		let fside = new StickySidebar('#tside', {
			innerWrapperSelector: '#tside .simpletoc',
			containerSelector: '#tmain',
			topSpacing: 120,
			bottomSpacing: 40
		});

		let fside_update = () => {
			if( window.matchMedia('(max-width:676px)').matches ){
				fside.destroy();
			} else {
				fside.updateSticky();
			}
		}

		document.addEventListener('resize', evt => {fside_update();})
		fside_update();
	}
})