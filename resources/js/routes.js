
import App from './Components/App.vue'
import Login from './Components/Auth/Login.vue'
import Register from './Components/Auth/Register'

export default {
    mode: 'history',
    base: process.env.BASE_URL,
    fallback: true,
    routes: [
        {
            path:'/',
            component:App,
            name:'home',
            meta: {
                requiresAuth: true
            }
        },
        {
            path:'/login',
            name:'login',
            component:Login
        },
        {
            path:'/register',
            name:'register',
            component: Register
        }
        
    ]
}