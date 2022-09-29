import './bootstrap';
import Alpine from "alpinejs";
import ToastComponent from '../../vendor/usernotnull/tall-toasts/resources/js/tall-toasts'
import tippy from 'tippy.js';
import collapse from '@alpinejs/collapse'


window.tippy = tippy;

tippy('[data-tippy-content]', {
	allowHTML: true,
	placement: 'auto',

});

Alpine.data('ToastComponent', ToastComponent)

window.Alpine = Alpine
Alpine.plugin(collapse)
Alpine.start()