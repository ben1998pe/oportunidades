document.addEventListener('DOMContentLoaded', () => {
	const _tmpl={get:(t,e,r)=>{e=function(t){if("string"!=typeof t)return t;var e=[];return t.split(".").forEach((function(t){t.split(/\[([^}]+)\]/g).forEach((function(t){t.length>0&&e.push(t)}))})),e}(e);for(var n=t,i=0;i<e.length;i++){if(!n[e[i]])return r;n=n[e[i]]}return n},render:(t,e)=>{"use strict";if(t="function"==typeof t?t():t,-1===["string","number"].indexOf(typeof t))throw"PlaceholdersJS: please provide a valid template";return e?t=t.replace(/\{\{([^}]+)\}\}/g,(function(t){t=t.slice(2,-2);var r=_tmpl.get(e,t.trim());return r||"{{"+t+"}}"})):t}};
	let oportunity_last = false;

	document.addEventListener('click', evt => {
		if( evt.target.matches('[data-action="postulate"]') ){
			evt.preventDefault();
			let link = evt.target;

			const tOportunity = {
				pushAlert: (text, type = 'error') => {
					let alert = document.createElement('div');
					alert.classList.add('account-alert', type);
					alert.innerHTML = text;
		
					document.querySelector('.turimet-account__alerts').append(alert);
					tOportunity.scrollTop();
		
					tOportunity.fadeOut(alert, (target, type) => {
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
				}
			};

			frm_data = new FormData();
			frm_data.append('action', 'apply_opt');
			frm_data.append('nonce', oportunity.nonce);
			frm_data.append('job', link.dataset.id );

			link.classList.add('loading');
			link.classList.remove('checked');
			link.classList.remove('error');

			fetch(oportunity.ajax_url,{
				method: 'POST',
				body: frm_data
			})
			.then(response => response.json())
			.then(result => {
				console.log(result)
				link.classList.remove('loading');
				if( result.success ){
					tOportunity.pushAlert('Postulación exitosa, se ha enviado una confirmación de la postulación a tu correo.', 'success');
					link.classList.add('checked');
				} else {
					if( typeof result.data.redirect_to != 'undefined' ) location.replace( result.data.redirect_to );
					link.classList.add('error');
					// tOportunity.pushAlert('Ya postulaste a esta oportunidad de trabajo.');
					tOportunity.pushAlert(result.data.msg);
				}
			})
		}
	})

	document.querySelectorAll('.oplist__item[data-id]').forEach(item => {
		item.addEventListener('click', evt => {
			console.log("test");
			let element = evt.target;

			if( element.tagName = 'A' && element.closest('.oplist__main--company') ){
			   // Do nothing 
			} else {
				if( window.matchMedia('(max-width:676px)').matches ) return;
				evt.preventDefault();

				if( item.dataset.id != oportunity_last ){
					let loader = document.createElement('div');
					loader.classList.add('oportunity-loader');

					document.querySelector('#oportunity-section').innerHTML = '';
					document.querySelector('#oportunity-section').append(loader);

					oportunity_last = item.dataset.id;
	
					fetch(oportunity.ajax_url, {
					    method: 'POST',
					    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
					    body: new URLSearchParams({
					        action: 'get_oportunity_opt',
					        id: oportunity_last,  // Asegúrate de que 'oportunity_last' esté bien definido
					        nonce: oportunity.nonce
					    })
					})
					.then(response => response.json())
					.then(data => {
		    					console.log(data);
								try {
									let output = _tmpl.render( document.getElementById('tmpl-oportunity').innerHTML, data.data);

									document.querySelector('#oportunity-section').innerHTML = output;

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
												 case 'facebook':
		                window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(current_url), '_blank');
		                break;
											}
											
											choices.setChoiceByValue(['']);
										})
									})
									//document.querySelector('#oportunity-section .oportunity-loader').remove();
								} catch (error) {}
								
							})
					  .catch(error => {
					    console.log('Fetch error:', error);
					  });
					
				}
			}
			
		})
	})
})