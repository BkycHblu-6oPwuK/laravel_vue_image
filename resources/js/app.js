import './bootstrap';
import { createApp } from 'vue';
import Index from './components/Index.vue';
import router from './router';
const app = createApp({});
app.use(router)
app.component('index-component', Index);
app.mount('#app');
