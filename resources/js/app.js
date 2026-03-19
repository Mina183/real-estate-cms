import './bootstrap';

import Alpine from 'alpinejs';
import './calendar';
import $ from 'jquery';

window.$ = window.jQuery = $;
window.Alpine = Alpine;

Alpine.start();
