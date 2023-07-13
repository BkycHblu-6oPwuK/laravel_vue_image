import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: '/', component: () => import('./components/Posts/Index.vue'),
        name: 'index'
    },
    {
        path: '/:id', component: () => import('./components/Posts/Show.vue'),
        name: 'show'
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

// router.beforeEach((to, from, next) => {
// })

export default router
