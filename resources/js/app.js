import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'


createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el)
  },
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