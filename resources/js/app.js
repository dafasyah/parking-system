import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import { Html5Qrcode } from 'html5-qrcode';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Chart = Chart;
window.Html5Qrcode = Html5Qrcode;
window.Swal = Swal;

window.toast = function (options = {}) {
	const {
		icon = 'success',
		title = '',
		text = '',
		timer = 2500,
	} = options;

	return Swal.fire({
		toast: true,
		position: 'top-end',
		showConfirmButton: false,
		timer,
		timerProgressBar: true,
		icon,
		title,
		text,
	});
};

window.alertModal = function (options = {}) {
	const {
		icon = 'info',
		title = '',
		text = '',
		confirmButtonText = 'OK',
	} = options;

	return Swal.fire({
		icon,
		title,
		text,
		confirmButtonText,
	});
};

Alpine.start();
