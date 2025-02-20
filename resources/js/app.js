import { createApp, h } from 'vue'
import { createInertiaApp, router } from '@inertiajs/vue3'
import 'boxicons';
import FloatingVue from 'floating-vue'
import 'floating-vue/dist/style.css';
import NProgress from 'nprogress';
import 'nprogress/nprogress.css';
import VueHighlightJS from 'vue3-highlightjs'



router.on('start', () => NProgress.done());


createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(FloatingVue)
      .use(VueHighlightJS)
      .mount(el)
  }
})