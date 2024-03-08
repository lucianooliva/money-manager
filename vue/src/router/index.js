import { createRouter, createWebHistory } from "vue-router"
import store from "../store"
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import DefaultLayout from '../views/DefaultLayout.vue'

const routes = [
  {
    path: '/',
    component: DefaultLayout,
    redirect: '/home',
    meta: { requiresAuth: true },
    children: [
      { path: '/home', name: 'Home', component: Home }
    ]
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { isGuest: true }
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { isGuest: true }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !store.state.user.token) {
    next({name: 'Login'})
  }
  else if (store.state.user.token && to.meta.isGuest) {
    next({name: 'Home'})
  }
  else {
    next()
  }
})


export default router
