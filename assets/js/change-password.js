'use strict';
document.addEventListener('DOMContentLoaded', () => {
    const tCPwd = {
        pushAlert: (text, type = 'error') => {
			let alert = document.createElement('div');
			alert.classList.add('account-alert', type);
			alert.innerHTML = text;

			document.querySelector('.turimet-account__alerts').append(alert);
			tCPwd.scrollTop();

			tCPwd.fadeOut(alert, (target, type) => {
				document.querySelector('.turimet-account__alerts').removeChild(alert);
			}, 5000);
		},
        scrollTop: () => {
			window.scrollTo({top: 0,behavior:'smooth'})
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
        validateForm: () => {
            document.addEventListener('change', evt => {
                if(evt.target.matches('input[type="password"]')){
                    evt.target.value = evt.target.value.trim();
                }
            })

            document.addEventListener('keyup', evt => {
                if(evt.target.matches('[name="password"]') || evt.target.matches('[name="password2"]')){
                    let input = evt.target;
                    if( 8 > input.value.length){
                        input.classList.add('has-error');
                        input.dataset.tippyContent = 'Las contraseñas deben tener 8 caracteres como mínimo.';
                        tippy(input, {arrow: true, trigger: 'focus', hideOnClick: false, theme: 'light-border'});
                        input._tippy.show();
                    } else {
                        input.classList.remove('has-error');
                        input.removeAttribute('data-tippy-content');
    
                        if(typeof input._tippy != 'undefined') input._tippy.destroy();
                    }
                }
                if(evt.target.matches('[name="password2"]')){
                    let input = evt.target;
                    if(input.value != document.querySelector('[name="password"]').value){
                        input.classList.add('has-error');
                        input.dataset.tippyContent = 'Las contraseñas no coinciden';
                        tippy(input, {arrow: true, trigger: 'focus', hideOnClick: false, theme: 'light-border'});
                        input._tippy.show();
                    } else {
                        input.classList.remove('has-error');
                        input.removeAttribute('data-tippy-content');
    
                        if(typeof input._tippy != 'undefined') input._tippy.destroy();
                    }
                }
            })

            document.addEventListener('submit', evt => {
                if(evt.target.matches('.form2')){
                    evt.preventDefault();

                    let data = new FormData(evt.target);
                    data.append('action', 'cambiar_clave');
                    data.append('nonce', tuProfile.nonce);

                    fetch(tuProfile.ajax_url, {
                        method: 'POST',
                        body: data
                    }).then(response => {return response.json()}).then(response => {
                        if(response.success){
                            tCPwd.pushAlert(response.data.msg, 'success');
                        } else {
                            tCPwd.pushAlert(response.data.error);
                        }
                    })
                }
            })
        },
        init: () => {
            tCPwd.validateForm();
        }
    }

    tCPwd.init();
})