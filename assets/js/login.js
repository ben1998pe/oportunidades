'use strict';
document.addEventListener('DOMContentLoaded', () => {
	const rnz_sum = obj => Object.values(obj).reduce((a, b) => a + b, 0);
	const rnz_transition = {
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
		}
	}
	const rnz_alerts = {
		push: (msg, type) => {
			let alert = document.createElement('div');
			alert.classList.add('access-alert', type);
			alert.textContent = msg;
			
			document.querySelector('.access-alerts').append(alert);

			rnz_transition.fadeOut(alert, (target, type) => {
				document.querySelector('.access-alerts').removeChild(alert);
			});
		},
		input_error: (form, target, msg) => {
			if( typeof target == 'string' ) target = form.querySelector('[name="' + target + '"]');

			let form_row = target.closest('.form-row');

			if( 0 == form_row.querySelectorAll('.ff-alert').length ){
				form_row.classList.add('has-error');

				let alert = document.createElement('div');
				alert.classList.add('ff-alert');
				form_row.append(alert);
				alert.textContent = msg;
			} else {
				form_row.querySelector('.ff-alert').textContent = msg;
			}
		}
	};

	// Login
	try {
		let frm_login = document.getElementById('form-login');
		if( typeof frm_login !== undefined || frm_login !== null ){
			let frm_login_submit = frm_login.querySelector('input[type=submit]'),
				frm_has_error = new Array();

			frm_login.querySelectorAll('#login_username, #login_password').forEach(input => {
				frm_has_error[input.id] = 1;
				
				function unlock_submit(){
					if( 'login_username'==input.id ){
						let regex = /^((?!\.)[\w-_.]*[^.])(@\w+)(\.\w+(\.\w+)?[^.\W])$/gm;
						frm_has_error[input.id] = ( regex.exec(input.value) == null ) ? 1 : 0;
					} else if( 'login_password'==input.id ){
						let val = input.value.trim();
						frm_has_error[input.id] = ( 3 >= val.length ) ? 1 : 0;
					}

					if( 0==rnz_sum(frm_has_error) ){
						frm_login_submit.removeAttribute('disabled');
					} else {
						frm_login_submit.setAttribute('disabled', 'disabled');
					}
				}
				input.addEventListener('keyup', unlock_submit);
				input.addEventListener('change', unlock_submit);
				input.addEventListener('blur', unlock_submit);
				input.addEventListener('input', unlock_submit);
			})

			// Login Submit
			frm_login.addEventListener('submit', evt => {
				evt.preventDefault();

				const frm_data = new FormData(frm_login);
				frm_data.append('action', 'login_opt');

				frm_data.append('nonce', login_opt_all.nonce_login_opt);

				fetch(login_opt_all.ajax_url,{
					method: 'POST',
					credentials: 'same-origin',
					body: frm_data
				})
				.then(response => response.json())
				.then(result => {
					if( !result.success ){
						rnz_alerts.push(result.data.msg, 'error');
					} else {
						rnz_alerts.push(result.data.msg, 'success');
						if( typeof result.data.redirect_to != 'undefined' ) location.replace( result.data.redirect_to );
					}
				})
			})
		}
	} catch (error) {
		
	}

	// Lost Password
	document.addEventListener('click', evt => {
		if(evt.target.matches('[data-action="lostpassword"]')){
			evt.preventDefault();

			let input = document.querySelector('#login_username');
			let regex = /^((?!\.)[\w-_.]*[^.])(@\w+)(\.\w+(\.\w+)?[^.\W])$/gm;
			
			if( regex.exec(input.value) == null ){
				rnz_alerts.push('Ingresa tu email para recuperar tu contraseña', 'error');
				input.classList.add('has-error');
				return;
			}

			input.classList.remove('has-error');

			const frm_data = new FormData();
			frm_data.append('action', 'lostpassword_opt');
			frm_data.append('email', input.value);
			frm_data.append('nonce', login_opt_all.nonce);

			fetch(login_opt_all.ajax_url,{
				method: 'POST',
				body: frm_data
			})
			.then(response => response.json())
			.then(result => {
				console.log(result);
				if( !result.success ){
					rnz_alerts.push(result.data.msg, 'error');
				} else {
					rnz_alerts.push(result.data.msg, 'success');
				}
			})
			
		}
	})

	// Recovery
	try {
		let frm_recovery = document.getElementById('form-recovery');
		// Register Validation
		if( typeof frm_recovery !== undefined || frm_recovery !== null ){
			let frm_recovery_submit = frm_recovery.querySelector('input[type=submit]'),
				frm_has_error3 = new Array();

			frm_recovery.querySelectorAll('#recovery_email, #recovery_code, #recovery_password, #recovery_password2').forEach(input => {
				frm_has_error3[input.id] = 1;
				
				function unlock_submit(){
					
					if( 'recovery_email'==input.id ){
						let regex = /^((?!\.)[\w-_.]*[^.])(@\w+)(\.\w+(\.\w+)?[^.\W])$/gm;
						frm_has_error3[input.id] = ( regex.exec(input.value) == null ) ? 1 : 0;
					} else if( ['recovery_code'].includes(input.id) ){
						let val = input.value.trim();
						frm_has_error3[input.id] = ( 3 > val.length ) ? 1 : 0;
					} else if( ['recovery_password', 'recovery_password2'].includes(input.id) ){
						let val = input.value.trim();
						frm_has_error3[input.id] = ( 8 > val.length ) ? 1 : 0;
					}

					if( 0==rnz_sum(frm_has_error3) ){
						frm_recovery_submit.removeAttribute('disabled');
					} else {
						frm_recovery_submit.setAttribute('disabled', 'disabled');
					}

					Object.keys(frm_has_error3).forEach(key => {
						if(frm_has_error3[key] == 1){
							document.getElementById(key).classList.add('has-error');
						} else {
							document.getElementById(key).classList.remove('has-error');
						}
					})
				}
				input.addEventListener('keyup', unlock_submit);
				input.addEventListener('blur', unlock_submit);
				input.addEventListener('input', unlock_submit);
				input.addEventListener('change', unlock_submit);
			})

			frm_recovery.addEventListener('submit', evt => {
				evt.preventDefault();

				frm_recovery.querySelectorAll('.has-error').forEach(input => {
					input.classList.remove('has-error');
					input.removeAttribute('data-tippy-content');

					if(typeof input._tippy != 'undefined'){
						input._tippy.destroy();
					}
				})
		
				const frm_data = new FormData(frm_recovery);
				frm_data.append('action', 'turimet-recovery');
				frm_data.append('nonce', turivar.nonce);
		
				fetch(turivar.ajax_url,{
					method: 'POST',
					body: frm_data
				})
				.then(response => response.json())
				.then(result => {
					console.log(result);
					if( result.success ){
						rnz_alerts.push(result.data.msg, 'success');
						if( typeof result.data.redirect_to != 'undefined' ) location.replace( result.data.redirect_to );
					} else {
						if( typeof result.data.errors !== 'undefined' ){
							result.data.errors.forEach(item => {
								let input = frm_recovery.querySelector('[name="' + item.input + '"]');
								input.classList.add('has-error');

								if( item.msg != false ){
									input.dataset.tippyContent = item.msg;
									tippy(input, {arrow: true, trigger: 'focus', hideOnClick: false, theme: 'light-border'});
									input._tippy.show();
								}
							})
						}

						if( typeof result.data.error !== 'undefined' ){
							rnz_alerts.push(result.data.msg, 'error');
						}
					}
				})
			})
		}
	} catch (error) {
		
	}


	// Register
	try {
		let frm_register = document.getElementById('form-register');
		// Register Validation
		if( typeof frm_register !== undefined || frm_register !== null ){
			let frm_register_submit = frm_register.querySelector('input[type=submit]'),
				frm_has_error2 = new Array();

			frm_register.querySelectorAll('#register_firstname, #register_lastname, #register_email').forEach(input => {
				frm_has_error2[input.id] = 1;
				
				function unlock_submit(){
					
					if( 'register_email'==input.id ){
						let regex = /^((?!\.)[\w-_.]*[^.])(@\w+)(\.\w+(\.\w+)?[^.\W])$/gm;
						frm_has_error2[input.id] = ( regex.exec(input.value) == null ) ? 1 : 0;
					} else if( ['register_firstname', 'register_lastname'].includes(input.id) ){
						let val = input.value.trim();
						frm_has_error2[input.id] = ( 3 >= val.length ) ? 1 : 0;
					} /*else if( ['register_password', 'register_password2'].includes(input.id) ){
						let val = input.value.trim();
						frm_has_error2[input.id] = ( 8 > val.length ) ? 1 : 0;
					}*/

					console.log(frm_has_error2);

					//frm_has_error2['match'] = document.getElementById('register_password').value!=document.getElementById('register_password2').value ? 1 : 0;
					if( 0==rnz_sum(frm_has_error2) ){
						frm_register_submit.removeAttribute('disabled');
					} else {
						frm_register_submit.setAttribute('disabled', 'disabled');
					}

					Object.keys(frm_has_error2).forEach(key => {
						if(frm_has_error2[key] == 1){
							document.getElementById(key).classList.add('has-error');
						} else {
							document.getElementById(key).classList.remove('has-error');
						}
					})
				}
				input.addEventListener('keyup', unlock_submit);
				input.addEventListener('blur', unlock_submit);
				input.addEventListener('input', unlock_submit);
				input.addEventListener('change', unlock_submit);
			})

			frm_register.addEventListener('submit', evt => {
				evt.preventDefault();
		
				const frm_data = new FormData(frm_register);
				frm_data.append('action', 'register_opt');
				frm_data.append('nonce', login_opt_all.nonce_register_opt);

				var serializeForm = function (formData) {
					var obj = {};
					for (var key of formData.keys()) {
						obj[key] = formData.get(key);
					}
					return obj;
				}
		
				fetch(login_opt_all.ajax_url,{
					method: 'POST',
					body: frm_data
				})
				.then(response => response.json())
				.then(result => {
					console.log(result)
					if( result.success ){
						rnz_alerts.push(result.data.msg, 'success');
						if( typeof result.data.redirect_to != 'undefined' ) location.replace( result.data.redirect_to );
					} else {
						if( typeof result.data.errors != 'undefined' ){
							frm_register.querySelectorAll('.form-row.has-error').forEach(row => {row.classList.remove('has-error')});
							frm_register.querySelectorAll('.form-row .ff-alert').forEach(alert => {alert.remove()});
							
							rnz_alerts.push('Revisa los campos marcados para continuar.', 'error');
							result.data['errors'].forEach(item => {
								rnz_alerts.input_error(frm_register, item.input, item.msg);
							})
							
						} else {
							rnz_alerts.push(result.data.msg, 'error');
						}
					}
				})
			})
		}
	} catch (error) {
		
	}

	document.querySelectorAll('.global-tab__list a').forEach(link => {
		link.addEventListener('click', evt => {
			evt.preventDefault();
			let target = link.href.split('#')[1];

			link.closest('.global-tab__list').querySelectorAll('li').forEach(li => {
				li.classList.remove('active')
			})
			link.closest('li').classList.add('active');

			document.querySelectorAll('.global-tab__content').forEach(div => {
				div.classList.remove('active')

				if( div.dataset.id == target ) div.classList.add('active')
			})
		})
	})
})