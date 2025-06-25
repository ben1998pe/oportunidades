'use strict';
document.addEventListener('DOMContentLoaded', () => {
    const tr_download = (url, name) => {
        if( name == undefined && /\.[^/.]+$/.test(url) ){
            const urlParts = url.split('/');
            name = urlParts[urlParts.length - 1];
        }
        const anchor = document.createElement('a')
        anchor.setAttribute('href', url)
        anchor.setAttribute('download', name)
        anchor.click()
    }
    const tr_modal = {
        open: (file) => {
            if(file != undefined && file)
                document.querySelector('.modal__download form').dataset.file = atob(file);
            document.querySelectorAll('.modal__overlay, .modal__download').forEach(elm => {elm.classList.remove('disabled')})
        },
        close: () => {
            document.querySelectorAll('.modal__overlay, .modal__download').forEach(elm => {elm.classList.add('disabled')})
            document.querySelectorAll('.modal__download form input:not([type=submit])').forEach(elm => {elm.value = ''})
        }
    }

    document.querySelectorAll('a[data-action="brochure"]').forEach(elm => {
        elm.addEventListener('click', evt => {
            evt.preventDefault();
            tr_modal.open(elm.dataset.media)
        })
    })

    document.addEventListener('click', evt => {
        if( evt.target.matches('a[data-action="brochure"]') ){
            evt.preventDefault();
            tr_modal.open()
        }
        if( evt.target.matches('.modal__overlay') ){
            evt.preventDefault();
            tr_modal.close()
        }
    })


    document.querySelectorAll('.modal__download form').forEach(frm => {
        let frm_errors = {};

        frm.addEventListener('keyup', evt => {
            if( evt.target.matches('[name="name"]') || evt.target.matches('[name="email"]') ){
                let input = evt.target, has_error = false, msg = false;

                if(evt.target.matches('[name="name"]')){
                    let regex = /^((\p{L}|\p{Mn}|\p{Pd}|\\'|\\x{2019}|\s)+)$/gmu;
                    if( !regex.test(input.value) ) has_error = true;

                    regex = /((\\'|\\x{2019}|-|\s){2,})/gmu;
                    if( regex.test(input.value) ) has_error = true;

                    regex = /^(.)\1+$/;
                    if( regex.test(input.value) ) has_error = true;

                    if( has_error ) msg = 'Ingrese un nombre válido.';
                } else if(evt.target.matches('[name="email"]')){
                    let regex = /^((?!\.)[\w-_.]*[^.])(@\w+)(\.\w+(\.\w+)?[^.\W])$/gm;
                    if( regex.exec(input.value) == null ){
                        has_error = true;
                        msg = 'Ingrese un email válido';
                    }
                }

                if( has_error ){
                    frm_errors[input.name] = 1;
                    input.classList.add('has-error');

                    if( msg != false ){
                        input.dataset.tippyContent = msg;
                        tippy(input, {arrow: true, trigger: 'focus', hideOnClick: false, theme: 'light-border'});
                        input._tippy.show();
                    }
                } else {
                    frm_errors[input.name] = 0;
                    input.classList.remove('has-error');
                    input.removeAttribute('data-tippy-content');

                    if(typeof input._tippy != 'undefined'){
                        input._tippy.destroy();
                    }
                }
            }
        })
        frm.addEventListener('submit', evt => {
            evt.preventDefault();

            let count = 0;

            if( Object.keys(frm_errors).length != 0 ){
                Object.keys(frm_errors).forEach(key => {
                    count += frm_errors[key];
                })
            } else {
                alert('Revisa los campos para continuar');
                return;
            }

            if( count > 0 ){
                alert('Revisa los campos para continuar');
                return;
            }

            if( frm.querySelector('[name="acceptance"]').checked == false ){
                alert('Debes aceptar los términos para continuar');
                return;
            }

            let data = new FormData(frm);

            if(frm.dataset.file != undefined)
                tr_download(frm.dataset.file);
            tr_modal.close()
            
        })
    })
})