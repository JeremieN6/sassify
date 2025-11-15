import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

// Import Vue.js
import { createApp } from 'vue';

// Import Flowbite dynamically only when needed to reduce initial JS execution
function needsFlowbite() {
	// If page includes data attributes or components that rely on Flowbite
	return document.querySelector('[data-dropdown]') || document.querySelector('[data-collapse]') || document.querySelector('[data-modal]') || document.querySelector('[data-accordion]') || document.querySelector('[data-datepicker]');
}

if (typeof window !== 'undefined' && needsFlowbite()) {
	import('flowbite').then(() => {
		// Flowbite loaded
	}).catch(err => {
		console.warn('Flowbite dynamic import failed', err);
	});
}

// Import main Vue component
import App from './vue/App.vue';

// Create Vue app
const app = createApp(App);

// Mount Vue app only if the target exists (some pages don't render a #app container)
const mountEl = document.getElementById('app');
if (mountEl) {
	app.mount('#app');
} else {
	// No mount target on this page (e.g., server-rendered pages like /quotes/new)
	// Avoid Vue mount error in console
	// console.debug('No #app element found — skipping Vue mount');
}
