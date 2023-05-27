import App from './Components/App.vue'
import Login from './Components/Auth/Login.vue'
import Register from './Components/Auth/Register'
import ForgotPassword from './Components/Auth/ForgotPassword.vue'
import ResetPassword from './Components/Auth/ResetPassword.vue'
import NotFoundPage from './Components/NotFoundPage.vue'

export default {
    mode: 'history',
    base: process.env.BASE_URL,
    fallback: true,
    routes: [
        {
            path: '/',
            component: App,
            name: 'home',
            meta: {
                requiresAuth: true
            },
        },
        {
            path: '/login',
            name: 'login',
            component: Login,
        },
        {
            path: '/register',
            name: 'register',
            component: Register,
        },
        {
            path: '/reset-password',
            name: 'reset-password',
            component: ResetPassword,
        },
        {
            path: '/forgot-password',
            name: 'forgot-password',
            component: ForgotPassword,
        },
        {
            path:'/404',
            name:'not-found',
            component: NotFoundPage,
        },
        {
            path: '*',
            redirect: '/404',
        },
    ]
}
