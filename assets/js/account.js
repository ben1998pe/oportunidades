'use strict';
document.addEventListener('DOMContentLoaded', () => {
    const adjustAside = () => {
        const adminbarHeight = document.getElementById('wpadminbar') != undefined ? document.getElementById('wpadminbar').offsetHeight : 0;
        const headerHeight = document.getElementById('header').offsetHeight;
        const aside = document.querySelector('.turimet-account__aside');

        aside.style.top = `${adminbarHeight + headerHeight}px`;
    }

    adjustAside();
    window.addEventListener('resize', adjustAside);

    document.querySelectorAll('.btn-filter select').forEach(select => {
		let choices = new Choices(select, {itemSelectText: '',searchEnabled:false,shouldSort:false});

		choices.passedElement.element.addEventListener('change', evt => {
			let selectedOption = choices.getValue().value;
			let current_url = select.closest('.btn-filter').dataset.url;

            if( selectedOption != '' )
                location.replace( current_url + '?orderby=' + selectedOption );			
		})
	})
})