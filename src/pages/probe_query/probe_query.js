import Vue from 'vue'
import probe_query from './probe_query.vue'
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';

Vue.config.productionTip = false

Vue.use(ElementUI)
new Vue({
  render: h => h(probe_query),
}).$mount('#app')
