'use strict';
document.addEventListener('DOMContentLoaded', () => {
	var click = false;
	let step = 1;
	const tProfile = {
		countrySelect: () => {
			let enableUbigeo = false;
			const fetchOptions = (evt) => {
				let type = evt.target.dataset.input;
				let input = '';
				try{
					input = evt.target.querySelector('option:checked').dataset.id
				} catch(ex){}

				console.log('Input: ', input);
				let data = new FormData();
				data.append('nonce', tuProfile.nonce);
				data.append('action', 'turimet-ubigeo');
				data.append('type', type);
				data.append('input', input);
				data.append('country', evt.target.closest('.ff__field-country').querySelector('[data-input=country]').value);

				fetch(tuProfile.ajax_url, {
					method: 'POST',
					body: data
				}).then(response => response.json())
				.then(result => {
					let output_id = ('country' == type) ? 'region' : ('region' == type) ? 'province' : ('province' == type) ? 'city' : false;
					let elm = evt.target.closest('.ff__field-country').querySelector('[data-input=' + output_id + ']');
					let label = ('region' == output_id) ? 'Departamento' : ('province' == output_id) ? 'Provincia' : ('city' == output_id) ? 'Distrito' : false;

					if(result.success && output_id ){	
						elm.innerHTML = '<option value="" selected disabled>' + label + '</option>';
						Object.keys(result.data.options).forEach(index => {
							elm.innerHTML += '<option value="' + result.data.options[index] + '" data-id="' + index + '">' + result.data.options[index] + '</option>'
						})

						tProfile.dispatch('change', elm);
					} else {
						elm.innerHTML = '';
					}
					console.log(result)
				})
			}
			document.addEventListener('change', evt => {
				if( evt.target.matches('[data-input=country]') ){
					console.log("[data-input=country]")
					console.log(evt.target.id)
					enableUbigeo = evt.target.value=='Peru';
					evt.target.closest('.ff__field-country').querySelectorAll('[data-input=region], [data-input=province], [data-input=city]').forEach(select => {
						enableUbigeo ? select.classList.remove('hidden') : select.classList.add('hidden');
						let container = select.closest('.ff__field-country');
						container.querySelector('[data-input=region]').innerHTML = '<option value="" selected disabled>Departamento</option>';
						container.querySelector('[data-input=province]').innerHTML = '<option value="" selected disabled>Provincia</option>';
						container.querySelector('[data-input=city]').innerHTML = '<option value="" selected disabled>Distrito</option>';
				
					})
					if (evt.target.id == 'country_born') {
						if (evt.target.id == 'country_born' &&  evt.target.value=='Peru') {
						$('#region_born').select2({
							placeholder: "Departamento",
							allowClear: true
						}).on('select2:select select2:clear', function () {
							this.dispatchEvent(new Event('change', { bubbles: true }));
						});
						$('#province_born').select2({
							placeholder: "Provincia",
							allowClear: true
						}).on('select2:select select2:clear', function () {
							this.dispatchEvent(new Event('change', { bubbles: true }));
						});
						$('#city_born').select2({
							placeholder: "Distrito",
							allowClear: true
						}).on('select2:select select2:clear', function () {
							this.dispatchEvent(new Event('change', { bubbles: true }));
						});
					}else{
						 if ($('#region_born').hasClass('select2-hidden-accessible')) {
                $('#region_born').select2('destroy');
            }
            if ($('#province_born').hasClass('select2-hidden-accessible')) {
                $('#province_born').select2('destroy');
            }
            if ($('#city_born').hasClass('select2-hidden-accessible')) {
                $('#city_born').select2('destroy');
            }
					}
					}
					
					if (evt.target.id == 'country_res') {
					if (evt.target.id == 'country_res' &&  evt.target.value=='Peru') {
							    $('#region_res').select2({
							      placeholder: "Departamento",
							      allowClear: true
							    }).on('select2:select select2:clear', function () {
							  this.dispatchEvent(new Event('change', { bubbles: true }));
							});
							    $('#province_res').select2({
							      placeholder: "Provincia",
							      allowClear: true
							    }).on('select2:select select2:clear', function () {
							  this.dispatchEvent(new Event('change', { bubbles: true }));
							});
							    $('#city_res').select2({
							      placeholder: "Distrito",
							      allowClear: true
							}).on('select2:select select2:clear', function () {
							  this.dispatchEvent(new Event('change', { bubbles: true }));
							});
					}else{
						if ($('#region_res').hasClass('select2-hidden-accessible')) {
                $('#region_res').select2('destroy');
            }
            if ($('#province_res').hasClass('select2-hidden-accessible')) {
                $('#province_res').select2('destroy');
            }
            if ($('#city_res').hasClass('select2-hidden-accessible')) {
                $('#city_res').select2('destroy');
            }
					}
				}
					
				}

				if( evt.target.matches('[data-input=country]') || evt.target.matches('[data-input=region]') || evt.target.matches('[data-input=province]') ){
					if(enableUbigeo){
						fetchOptions(evt);
						let container = evt.target.closest('.ff__field-country');
						try {
							container.querySelector('[data-input=ubigeo]').value = evt.target.querySelector('option:checked').dataset.id == undefined ? '' : evt.target.querySelector('option:checked').dataset.id;
						} catch (error) {}
					}
				}

				if( evt.target.matches('[data-input=city]') ){
					let container = evt.target.closest('.ff__field-country');
					try {
						container.querySelector('[data-input=ubigeo]').value = evt.target.querySelector('option:checked').dataset.id == undefined ? '' : evt.target.querySelector('option:checked').dataset.id;
					} catch (error) {}
				}
			})
		},
		avatarChange: () => {
			document.addEventListener('change', evt => {
				if(evt.target.matches('[data-input=avatar-upload]')){
					let parent = evt.target.closest('.ff__field-image-upload');
					if(evt.target.files[0] !== undefined){
						parent.querySelector('[data-input=avatar]').src = URL.createObjectURL(evt.target.files[0]);

						document.querySelectorAll('.user-avatar img').forEach(img => {
							img.src = URL.createObjectURL(evt.target.files[0]);
						})
						parent.classList.add('active');
					} else {
						parent.classList.remove('active');
					}
				}
				if(evt.target.matches('#cv')){
					if(evt.target.files[0] !== undefined){
						let file = evt.target.files[0], parent = evt.target.closest('.ff__field-upload');

						if (file.type === 'application/pdf' || file.type === 'application/msword' || file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
							parent.classList.remove('checked', 'loading');
							parent.classList.add('loading');

							if (file.size <= 2 * 1024 * 1024) {
								let reader = new FileReader();
								reader.onloadend = () => {
									var base64Data = reader.result.split(',')[1];
									//uploadFile(base64Data);
									let data = new FormData();
									data.append('cv', base64Data);

									fetch(tuProfile.ajax_url + '?action=upload_cv&nonce=' + tuProfile.nonce, {
										method: 'POST',
										body: data
									}).then(response => {return response.json()}).then(response => {
										console.log(response);
										if( response.success ){
											parent.classList.add('checked');
										} else {
											tProfile.pushAlert('Ha ocurrido un error. Intenta de nuevo');
										}
										
									});
								}
								reader.readAsDataURL(file);
							} else {
								console.log('No sale 2');
								parent.classList.remove('checked', 'loading');
								tProfile.pushAlert('No se permiten archivos mayores de 2MB.');
							}
						} else {
							console.log('No sale');
							parent.classList.remove('checked', 'loading');
							tProfile.pushAlert('Ha ingresado un tipo de archivo no válido.');
						}
					}
				}
			})
		},
		setupChoices: () => {
			new Choices(document.querySelector('[name="keywords[]"]'), {maxItemCount: 3,
				maxItemText: (maxItemCount) => {
					return `Solo puedes agregar ${maxItemCount} opciones`;
				}, noChoicesText: 'No hay más opciones para elegir', removeItemButton: true, delimiter: '|'});

			new Choices(document.querySelector('[name="skills[]"]'), {removeItemButton: true, noChoicesText: 'No hay más opciones para elegir', delimiter: '|'})
		},
		setupMobile: () => {
			try {
				let input = document.querySelector('#mobile_visible');

				window.intlTelInput(input, {
					initialCountry: 'pe',
					separateDialCode: false
				});

				function tel_getNumber(){
					let iti = window.intlTelInputGlobals.getInstance(input),
					country = iti.getSelectedCountryData();

					input.closest('.ff__field-wrap').querySelector('#mobile').value = iti.getNumber();
				}

				input.addEventListener('keyup', evt => {tel_getNumber();})
				input.addEventListener('change', evt => {tel_getNumber();})
				input.addEventListener('countrychange', () => {tel_getNumber();})
			} catch (error) {}
		},
		gotoStep: (_step) => {
			if( 0 > _step || _step > 2 ) return;

			document.querySelectorAll('.form2[data-step]').forEach(div => {
				div.classList.add('hidden');
			})
			document.querySelectorAll('.tab-titles span[data-step]').forEach(span => {
				span.classList.remove('active');
			})

			document.querySelectorAll('.form2[data-step="' + _step + '"]').forEach(div => {
				div.classList.remove('hidden');
			})
			document.querySelectorAll('.tab-titles span[data-step="' + _step + '"]').forEach(span => {
				span.classList.add('active');
			})

			_step == 1 ? document.querySelector('.form-footer').classList.remove('last') : document.querySelector('.form-footer').classList.add('last');
			document.querySelector('[data-action="toggle-step"] span').textContent = (_step == 1) ? 'Siguiente' : 'Anterior';

			tProfile.scrollTop();
		},
		setupSteps: () => {
			document.addEventListener('click', evt => {
				if(evt.target.matches('[data-action="toggle-step"]') || evt.target.matches('[data-action="toggle-step"] span')){
					evt.preventDefault();

					step = 1==step ? 2 : 1
					tProfile.gotoStep(step);
				}
			})
		},
		setupRepeaters: () => {
			const rLangs = () => {
				if(document.querySelectorAll('[data-input="langs"] .ff__field-wrap--lang').length >= 5) return;
				
				let output = tProfile.template(document.getElementById('tpl-languages').innerHTML, {
					key: tProfile.uniqid()
				})

				let container = document.createElement('div');
				container.innerHTML = output;

				document.querySelector('[data-input="langs"]').append(container.childNodes[1]);
				$('.language_language_').select2({
					allowClear: true
				}).on('select2:select select2:clear', function () {
					this.dispatchEvent(new Event('change', { bubbles: true }));
				});
			}, rStudies = () => {
				if(document.querySelectorAll('[data-input="studies"] .ff__field-group').length >= 10) return;

				let key = tProfile.uniqid();
				let output = tProfile.template(document.getElementById('tpl-studies').innerHTML, {
					key: key
				})

				let container = document.createElement('div');
				container.innerHTML = output;
				container.childNodes[1].classList.add('active');

				document.querySelector('[data-input="studies"]').append(container.childNodes[1]);
				$('.studies_grade_').select2({
					allowClear: true
				}).on('select2:select select2:clear', function () {
					this.dispatchEvent(new Event('change', { bubbles: true }));
				});
				$('.studies_specialty_').select2({
					allowClear: true
				}).on('select2:select select2:clear', function () {
					this.dispatchEvent(new Event('change', { bubbles: true }));
				});
				$('.studies__institution_').select2({
					allowClear: true
				}).on('select2:select select2:clear', function () {
					this.dispatchEvent(new Event('change', { bubbles: true }));
				});
				$('.studies_year_start_').select2({
					allowClear: true
				}).on('select2:select select2:clear', function () {
					this.dispatchEvent(new Event('change', { bubbles: true }));
				});
				$('.studies_year_end_').select2({
					allowClear: true
				}).on('select2:select select2:clear', function () {
					this.dispatchEvent(new Event('change', { bubbles: true }));
				});

				

			}, rComplimentary = () => {
				if(document.querySelectorAll('[data-input="complimentary"] .ff__field-group').length >= 10) return;

				let key = tProfile.uniqid();
				let output = tProfile.template(document.getElementById('tpl-complimentary').innerHTML, {
					key: key
				})

				let container = document.createElement('div');
				container.innerHTML = output;
				container.childNodes[1].classList.add('active');

				document.querySelector('[data-input="complimentary"]').append(container.childNodes[1]);
			}, rExperience = () => {
				if(document.querySelectorAll('[data-input="experience"] .ff__field-group').length >= 10) return;

				let key = tProfile.uniqid();
				let output = tProfile.template(document.getElementById('tpl-experience').innerHTML, {
					key: key
				})

				let container = document.createElement('div');
				container.innerHTML = output;
				container.childNodes[1].classList.add('active');

				document.querySelector('[data-input="experience"]').append(container.childNodes[1]);
				$('.experiencepp').select2({
					allowClear: true
				}).on('select2:select select2:clear', function () {
					this.dispatchEvent(new Event('change', { bubbles: true }));
				});

			};

			document.addEventListener('click', evt => {
				if(evt.target.matches('[data-action="add-lang"]')){
					evt.preventDefault();
					evt.target.textContent = 'Agregar otro idioma';
					rLangs();
				}
				if(evt.target.matches('[data-action="add-studies"]')){
					evt.preventDefault();
					evt.target.textContent = 'Agregar otra formación';
					rStudies();
				}
				if(evt.target.matches('[data-action="add-complimentary"]')){
					evt.preventDefault();
					evt.target.textContent = 'Agregar otra expecialidad';
					rComplimentary();
				}
				if(evt.target.matches('[data-action="add-experience"]')){
					evt.preventDefault();
					evt.target.textContent = 'Agregar otra experiencia';
					rExperience();
				}

				if(evt.target.matches('.ff__field-group [data-action="remove"]')){
					evt.preventDefault();
					evt.target.closest('.ff__field-group').remove();
				}
				
				if(evt.target.matches('[data-input="repeater-item"] [data-action="remove"]')){
					evt.preventDefault();
					evt.target.closest('[data-input="repeater-item"]').remove();
				}
				if(evt.target.matches('.ff__field-group--title')){
					evt.preventDefault();
					rnz.slideToggle(evt.target.closest('.ff__field-group').querySelector('.ff__field-group--content'), ( elm, type) => {
						if( 'up' == type ) elm.closest('.ff__field-group').classList.remove('active'); else elm.closest('.ff__field-group').classList.add('active');
					}, 300, 'flex');
				}
			})

			document.addEventListener('change', evt => {
				if(evt.target.matches('.ff__field-group :is([data-input="grade"],[data-input="specialty"])')){
					let parent = evt.target.closest('.ff__field-group'), title = parent.querySelector('.ff__field-group--title span'), tokens = [];

					if( parent.querySelector('[data-input="grade"]').value != '' ){
						tokens.push(parent.querySelector('[data-input="grade"]').value);
					}

					if( parent.querySelector('[data-input="specialty"]').value != '' ){
						tokens.push(parent.querySelector('[data-input="specialty"]').value);
					}

					title.textContent = tokens.join(' - ');
				}

				if(evt.target.matches('.ff__field-group :is([data-input="type_course"],[data-input="complementary_course"])')){
					let parent = evt.target.closest('.ff__field-group'), title = parent.querySelector('.ff__field-group--title span'), tokens = [];

					if( parent.querySelector('[data-input="type_course"]').value != '' ){
						tokens.push(parent.querySelector('[data-input="type_course"]').value);
					}

					if( parent.querySelector('[data-input="complementary_course"]').value != '' ){
						tokens.push(parent.querySelector('[data-input="complementary_course"]').value);
					}

					title.textContent = tokens.join(' - ');
				}

				if(evt.target.matches('.ff__field-group :is([data-input="position"],[data-input="company"])')){
					let parent = evt.target.closest('.ff__field-group'), title = parent.querySelector('.ff__field-group--title span'), tokens = [];

					if( parent.querySelector('[data-input="position"]').value != '' ){
						tokens.push(parent.querySelector('[data-input="position"]').value);
					}

					if( parent.querySelector('[data-input="company"]').value != '' ){
						tokens.push(parent.querySelector('[data-input="company"]').value);
					}

					title.textContent = tokens.join(' - ');
				}
			})
		},
		pushAlert: (text, type = 'error') => {
			let alert = document.createElement('div');
			alert.classList.add('account-alert', type);
			alert.innerHTML = text;

			document.querySelector('.turimet-account__alerts').append(alert);
			tProfile.scrollTop();

			tProfile.fadeOut(alert, (target, type) => {
				document.querySelector('.turimet-account__alerts').removeChild(alert);
			}, 5000);
		},
		setupForm: () => {
			document.querySelector('#profile-form').addEventListener('submit', evt => {
				evt.preventDefault();
				let data = new FormData(document.querySelector('#profile-form'));
				data.append('action', 'turimet-profile');
				data.append('nonce', tuProfile.nonce);
				data.delete('cv');

				let inputs_with_error = [];
				document.querySelectorAll(':is(input,select,textarea).has-error').forEach(input => {
					input.classList.remove('has-error');
					input.removeAttribute('data-tippy-content');
					if(typeof input._tippy != 'undefined'){
						input._tippy.destroy();		
					}
				})
				/*try {
					inputs_with_error.forEach(item => {
						let input = document.getElementById(item);
						input.classList.remove('has-error');
						input.removeAttribute('data-tippy-content');
						if(typeof input._tippy != 'undefined'){
							input._tippy.destroy();		
						}
					})
				} catch (error) {
					
				}*/

				fetch(tuProfile.ajax_url, {
					method: 'POST',
					body: data
				}).then(response => {return response.json()}).then(response => {

					if( response.success ){
						let alert = document.createElement('div');
						alert.classList.add('account-alert', 'success');
						alert.innerHTML = response.data.msg;


						document.querySelector('.turimet-account__alerts').append(alert);
						tProfile.scrollTop();

						try {
							document.querySelector('#avatar').value = null;
						} catch (error) {}

						tProfile.fadeOut(alert, (target, type) => {
							document.querySelector('.turimet-account__alerts').removeChild(alert);
						}, 5000);
					} else {
						console.log(response)
						if (response.data.error !== undefined) {
							let alert = document.createElement('div');
							alert.classList.add('account-alert', 'error');
							alert.innerHTML = response.data.error || response.error;
							// Declarar fuera para que persista
							

							// Luego dentro de tu lógica
							if (response.data.error == "Debe agregar al menos una formación a su perfil" && !click) {
								click = true;
								document.getElementById('add-formation-btn').click();
							}

							
							document.querySelector('.turimet-account__alerts').append(alert);

							tProfile.fadeOut(alert, (target, type) => {
								document.querySelector('.turimet-account__alerts').removeChild(alert);
							}, 5000);
						}


						if (response.data.errors != undefined) {
							try {
								let alert = document.createElement('div');
								alert.classList.add('account-alert', 'error');

								const fieldLabels = {
									address: 'Dirección',
									born_date: 'Fecha de nacimiento',
									country_born: 'País de nacimiento',
									country_res: 'País de residencia',
									document_type: 'Tipo de documento',
									document_number: 'Número de documento',
									gender: 'Sexo',
									profile: 'Descripción de Perfil profesional',
									mobile: 'Celular',
									mobile: 'Celular',

								};


								let message = 'Por favor revisa los campos marcados con rojo. <br>';
								let errorList = '<ul>';
								
								Object.entries(response.data.errors).forEach(([field, errorMessage]) => {
									if (field.startsWith('studies_') || field.startsWith('complementary_') || field.startsWith('experience_')) {
								        // Mostrar solo el mensaje de error
										errorList += `<li><strong>${errorMessage}</strong></li>`;
									} else {
								        // Mostrar label traducido o el nombre del campo
										const label = fieldLabels[field] || field;
										errorList += `<li><strong>${label}:</strong> ${errorMessage}</li>`;
									}
								});
								errorList += '</ul>';


								alert.innerHTML = `${message}${errorList}`;

								document.querySelector('.turimet-account__alerts').append(alert);

								tProfile.fadeOut(alert, (target, type) => {
									document.querySelector('.turimet-account__alerts').removeChild(alert);
								}, 5000);

								Object.keys(response.data.errors).forEach(item => {
									let input = document.getElementById(item);
									inputs_with_error.push(input);

									input.classList.add('has-error'); 


									if (item === 'mobile' && document.getElementById('mobile_visible')) {
										let mobileVisibleInput = document.getElementById('mobile_visible');
										mobileVisibleInput.classList.add('has-error'); 
									}

									input.dataset.tippyContent = response.data.errors[item];

									if (typeof input._tippy != 'undefined') {
										input._tippy.destroy();
									}
									tippy(input, { arrow: true, trigger: 'focus', hideOnClick: false, theme: 'light-border' });
								});


								if (document.querySelectorAll('.form2[data-step="1"] .has-error').length > 0) {
									tProfile.gotoStep(1);
								} else {
									tProfile.gotoStep(2);
								}
							} catch (error) {
								console.log(error);
							}
						}


						tProfile.scrollTop();
					}
					console.log(response);
				})
			})
},
validateForm: () => {
	document.addEventListener('keyup', evt => {
		if(evt.target.matches('#profile-form :is(input,textarea)')){
			let input = evt.target;
			let regex = /(\s*([\0\b\'\"\n\r\t\%\_\\]*\s*(((select\s*.+\s*from\s*.+)|(insert\s*.+\s*into\s*.+)|(update\s*.+\s*set\s*.+)|(delete\s*.+\s*from\s*.+)|(drop\s*.+)|(truncate\s*.+)|(alter\s*.+)|(exec\s*.+)|(\s*(all|any|not|and|between|in|like|or|some|contains|containsall|containskey)\s*.+[\=\>\<=\!\~]+.+)|(let\s+.+[\=]\s*.*)|(begin\s*.*\s*end)|(\s*[\/\*]+\s*.*\s*[\*\/]+)|(\s*(\-\-)\s*.*\s+)|(\s*(contains|containsall|containskey)\s+.*)))(\s*[\;]\s*)*)+)/i,
			has_error = false, msg = false;

			if( input.value != '' && regex.test(input.value) ){
				msg = '¡Intento de hackeo! No se permiten sentencias SQL';
				has_error = true;
			}

			if(typeof input.dataset.validate != 'undefined'){
				switch(input.dataset.validate){
				case 'name':
					regex = /^((\p{L}|\p{Mn}|\p{Pd}|\\'|\\x{2019}|\s)+)$/gmu;
					if( !regex.test(input.value) ) has_error = true;

					regex = /((\\'|\\x{2019}|-|\s){2,})/gmu;
					if( regex.test(input.value) ) has_error = true;

					regex = /^(.)\1+$/;
					if( regex.test(input.value) ) has_error = true;

					if( has_error ) msg = 'Ingrese un nombre válido.';
					break;
				case 'email':
					regex = /^((?!\.)[\w-_.]*[^.])(@\w+)(\.\w+(\.\w+)?[^.\W])$/gm;
					if( regex.exec(input.value) == null ){
						has_error = true;
						msg = 'Ingrese un email válido';
					}
					break;
				case 'int':
					if( input.value != '' ){
						if( /^([0-9]+)$/gm.exec(input.value) == null || /^([0-9])\1+$/.exec(input.value) != null ){
							has_error = true;
							msg = 'Ingrese un número válido';
						}
					}
					break;
				case 'username':
					if( input.value != '' ){
						if( /^([a-zA-Z0-9\.\-\_]+)$/gm.exec(input.value) == null ){
							has_error = true;
							msg = 'Solo se permiten caracteres alfanuméricos y los símbolos (.,-,_)';
						} else if( /(\.|\-){2,}/gm.exec(input.value) != null ){
							has_error = true;
							msg = 'No se permiten puntos o guiones consecutivos';
						} else if( /^([!@#$%^&*0-9]+)$/gm.exec(input.value) != null || /^(.)\1+$/.exec(input.value) != null ){
							has_error = true;
							msg = 'Por favor ingrese un texto válido';
						}
					}
					break;
				case 'text':
					if( /^([!@#$%^&*0-9]+)$/gm.exec(input.value) != null || /^(.)\1+$/.exec(input.value) != null ){
						has_error = true;
						msg = 'Por favor ingrese un texto válido';
					}
					break;
				case 'document_number':
					let document_type = document.querySelector('select[name="document_type"]').value;

					switch( document_type ){
					case 'DNI':
						if( /^([0-9]{8})$/gm.exec(input.value) == null ) has_error = true;
						break;
					case 'Pasaporte':
						if( /^([a-zA-Z0-9]{8,12})$/gm.exec(input.value) == null ) has_error = true;
						break;
					case 'RUC':
						if( /^([0-9]{11})$/gm.exec(input.value) == null ) has_error = true;
						break;
					case 'Carnet Extrangería':
					case 'Carnet Extranjería':
					case 'Carnet Extrangeria':
					case 'Carnet Extranjeria':
					case 'CE':
					case 'ce':
						if( /^([a-zA-Z0-9]{8,12})$/gm.exec(input.value) == null ) has_error = true;
						break;
					}

					if( has_error ) msg = 'Ingresa un número de documento válido';
					break
				}
			}

			if( has_error ){
				input.classList.add('has-error');

				if( msg != false ){
					input.dataset.tippyContent = msg;
					tippy(input, {arrow: true, trigger: 'focus', hideOnClick: false, theme: 'light-border'});
					input._tippy.show();
				}
			} else {
				input.classList.remove('has-error');
				input.removeAttribute('data-tippy-content');

				if(typeof input._tippy != 'undefined'){
					input._tippy.destroy();
				}

			}
		}
	})
	document.addEventListener('change', evt => {
		if(evt.target.matches('#profile-form :is(input,textarea):not([type=file], [type=checkbox], [type=radio])')){
			let input = evt.target;
			input.value = input.value.trim();
			input.value = input.value.replace(/(\s{2,})/gm, ` `);

			tProfile.dispatch('keyup', input);
		}
		if(evt.target.matches('[name="document_type"]')){
			tProfile.dispatch('change', document.querySelector('[name="document_number"]'));
		}
		if(evt.target.matches('[name="has_colegiatura"]')){
			let sibling = evt.target.nextElementSibling;
			if(evt.target.value==1){
				sibling.classList.remove('hidden');
				sibling.querySelectorAll('input').forEach(input => {input.removeAttribute('disabled')})
			} else {
				sibling.classList.add('hidden');
				sibling.querySelectorAll('input').forEach(input => {input.disabled = 'disabled'})
			}
		}
		if(evt.target.matches('[id*="study_now"]')){
			let elm = evt.target.closest('.ff__field-group--content').querySelector('[id*="year_end"]');
			(evt.target.checked == true) ? elm.disabled = 'disabled' : elm.removeAttribute('disabled');
		}
		if(evt.target.matches('[id*="currently_work"]')){
			let elm = evt.target.closest('.ff__field-group--content').querySelector('[id*="date_final"]');
			if(evt.target.checked == true){
				elm.disabled = 'disabled';
				elm.value = '';
			} else {
				elm.removeAttribute('disabled');
			}
		}
		if(evt.target.matches('[id*="not_share_salary"]')){
			let elm = evt.target.closest('.ff__field-group--content').querySelector('[id*="_salary"]');
			if(evt.target.checked == true){
				elm.disabled = 'disabled';
				elm.value = '';
			} else {
				elm.removeAttribute('disabled');
			}
		}
		if(evt.target.matches('[id*="date_initial"]')){
			let final = evt.target.closest('.ff__field-group--content').querySelector('[id*="date_final"]');
			( evt.target.value != '') ? final.min = evt.target.value : final.removeAttribute('min');

		}
		if(evt.target.matches('[id*="date_initial"]') || evt.target.matches('[id*="date_final"]') || evt.target.matches('[id*="currently_work"]') ){
			let parent = evt.target.closest('.ff__field-group--content'),
			di_val = parent.querySelector('[id*="date_initial"]').value,
			df_val = parent.querySelector('[id*="date_final"]').value,
			cw_val = parent.querySelector('[id*="currently_work"]').checked;

			let initial = new Date(di_val), final = '';

			if( cw_val ){
				final = new Date();
			} else if( df_val != '' ){
				final = new Date(df_val);
			}

			if( final != '' ){
				let diff_ms = Math.abs(final - initial);
				let diff_days = Math.floor(diff_ms / (1000 * 60 * 60 * 24));

				let anios = Math.floor(diff_days / 365),
				meses = Math.floor((diff_days % 365) / 30),
				dias = (diff_days % 365) % 30;

				let resultado = '';
				if (anios > 0) {
					resultado += anios + (anios === 1 ? " año, " : " años, ");
				}
				if (meses > 0) {
					resultado += meses + (meses === 1 ? " mes, " : " meses, ");
				}
				resultado += dias + (dias === 1 ? " día" : " días");

				parent.querySelector('[id*="_time"]').value = resultado;
			} else {
				parent.querySelector('[id*="_time"]').value = '';
			}

		}
		if(evt.target.matches('#born_date')){
			let fechaNacimiento = new Date(evt.target.value), hoy = new Date();

			let edad = hoy.getFullYear() - fechaNacimiento.getFullYear(),
			diferenciaMeses = hoy.getMonth() - fechaNacimiento.getMonth();

			if (diferenciaMeses < 0 || (diferenciaMeses === 0 && hoy.getDate() < fechaNacimiento.getDate())) edad--;

			if( isNaN(edad)) edad = 0;

			document.querySelector('#years_old').value = edad;
		}
		if(evt.target.matches('[id*="year_start"]')){
			let sibling = evt.target.nextElementSibling;

			for (var i = 0; i < sibling.options.length; i++){
				sibling.options[i].disabled = false;
				sibling.options[i].classList.remove('hidden');
			}

			var selectedYear = parseInt(evt.target.value);

			for (var i = 0; i < sibling.options.length; i++) {
				let year = parseInt(sibling.options[i].value);
				if (year < selectedYear) {
					sibling.options[i].disabled = true;
					sibling.options[i].classList.add('hidden');
					sibling.options[i].removeAttribute('selected');
				}
			}

			if( selectedYear > parseInt(sibling.value) ){
				sibling.value = '';
				tProfile.dispatch('change', sibling);
			}
		}
	})
},
dispatch: (eventName, element) => {
	try {
		let event = new Event(eventName);
		Object.defineProperty(event, 'target', {writable: false, value: element});
		document.dispatchEvent(event);
	} catch (error) {}
},
setup: () => {
	tProfile.countrySelect();
	tProfile.avatarChange();
	tProfile.setupChoices();
	tProfile.setupSteps();
	tProfile.setupRepeaters();
	tProfile.setupForm();
	tProfile.validateForm();
	tProfile.setupMobile();

},
template: (tpl, data) => {
	'use strict';

	const template_get = (obj, path, def) => {
		var stringToPath = function (path) {
			if (typeof path !== 'string') return path;
			var output = [];

			path.split('.').forEach(function (item) {
				item.split(/\[([^}]+)\]/g).forEach(function (key) {
					if (key.length > 0) output.push(key);
				})
			});
			
			return output;
		};

		path = stringToPath(path);
		var current = obj;

		for (var i = 0; i < path.length; i++) {
			if (!current[path[i]]) return def;
			current = current[path[i]];
		}
		return current;
	}

	tpl = typeof (tpl) === 'function' ? tpl() : tpl;
	if (['string', 'number'].indexOf(typeof tpl) === -1) throw 'PlaceholdersJS: please provide a valid template';

	if (!data) return tpl;

	tpl = tpl.replace(/\{\{([^}]+)\}\}/g, match => {
		match = match.slice(2, -2);
		var val = template_get(data, match.trim());
		if (!val) return '{{' + match + '}}';
		return val;
	})

	return tpl;
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
uniqid: () => {
	let output = String( Date.now().toString(32) + Math.random().toString(16) ).replace(/\./g, '');
	let start = Math.floor(Math.random() * 6);

	return output.substring(start, start + 12);
}
}

try {
	tProfile.setup()
} catch (error) {
	console.log(error)
}
})