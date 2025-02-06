import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';


console.log(window.Inertia);
createInertiaApp({
  // Usamos importación dinámica para resolver las páginas
  resolve: name => import(`./Pages/${name}.vue`),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el);
  },
});