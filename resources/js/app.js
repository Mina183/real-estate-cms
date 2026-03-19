import './bootstrap';

import Alpine from 'alpinejs';
import './calendar';
import $ from 'jquery';
import 'select2';

window.$ = window.jQuery = $;
window.Alpine = Alpine;

Alpine.start();
