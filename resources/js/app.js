import { createApp, h } from 'vue'
import { createInertiaApp, router } from '@inertiajs/vue3'

import 'boxicons';
import FloatingVue from 'floating-vue'
import 'floating-vue/dist/style.css';
import NProgress from 'nprogress';
import 'nprogress/nprogress.css';
import VueHighlightJS from 'vue3-highlightjs'
import Vue3Toastify from 'vue3-toastify'
import 'vue3-toastify/dist/index.css';







router.on('start', () => NProgress.done());


createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(FloatingVue)
      .use(VueHighlightJS)
      .use(Vue3Toastify, {
        position: "top-right",
        autoClose: 2000,
        theme: "dark",
        hideProgressBar: true,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,

      })
    app.mount(el);
  }
})

const style = document.createElement('style')
style.textContent = `
  #nprogress { 
    display: none; 
    pointer-events: none;
  }
  #nprogress .bar, 
  #nprogress .spinner,
  #nprogress .peg {
    display: none;
  }
`
document.head.appendChild(style)