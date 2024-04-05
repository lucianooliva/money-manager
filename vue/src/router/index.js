import { createRouter, createWebHistory } from "vue-router"
import store from "../store"
import { emailSendingIsConfigured } from "../../config/index.js"
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import DefaultLayout from '../views/DefaultLayout.vue'
import MonthlyBills from "../views/MonthlyBills.vue"
import MonthlyIncomes from "../views/MonthlyIncomes.vue"
import Category from "../views/Category.vue"
import ExpenseEditor from "../views/ExpenseEditor.vue"
import IncomeEditor from "../views/IncomeEditor.vue"
import UserMenu from "../views/UserMenu.vue"
import UserProfile from "../views/UserProfile.vue"
import VerifyYourEmail from "../views/VerifyYourEmail.vue"

const routes = [
  {
    path: '/',
    component: DefaultLayout,
    redirect: '/home',
    meta: { requiresAuth: true, requiresVerifiedEmail: emailSendingIsConfigured },
    children: [
      { path: '/home', name: 'Home', component: Home },
      { path: '/monthly-bills', name: 'MonthlyBills', component: MonthlyBills },
      { path: '/monthly-incomes', name: 'MonthlyIncomes', component: MonthlyIncomes },
      { path: '/category/create', name: 'CategoryCreate', component: Category },
      { path: '/category/:id', name: 'CategoryView', component: Category },
      { path: '/expense/create', name: 'ExpenseCreator', component: ExpenseEditor },
      { path: '/expense/:id', name: 'ExpenseEditor', component: ExpenseEditor },
      { path: '/income/create', name: 'IncomeCreator', component: IncomeEditor },
      { path: '/income/:id', name: 'IncomeEditor', component: IncomeEditor },
      { path: '/user', name: 'UserProfile', component: UserProfile },
    ]
  },
  { 
    path: '/verify-your-email',
    name: 'VerifyYourEmail',
    component: VerifyYourEmail,
    meta: { requiresAuth: true, requiresVerifiedEmail: false },
  },

  {
    path: '/user-menu',
    name: 'UserMenu',
    component: UserMenu,
    meta: { requiresAuth: true, requiresVerifiedEmail: emailSendingIsConfigured },
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { isGuest: true, requiresVerifiedEmail: emailSendingIsConfigured }
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { isGuest: true, requiresVerifiedEmail: emailSendingIsConfigured }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const verifiedEmail = store.state.auth.user.verifiedEmail === 'true'
  const hasSession = store.state.auth.user.token

  if (to.meta.requiresAuth && !hasSession) {
    next({name: 'Login'})
  }
  else if (to.meta.requiresAuth && to.meta.requiresVerifiedEmail && !verifiedEmail) {
    next({name: 'VerifyYourEmail'})
  }
  else if (to.meta.requiresAuth && !to.meta.requiresVerifiedEmail && verifiedEmail) {
    next({name: 'Home'})
  }
  else if (to.meta.isGuest && hasSession && verifiedEmail) {
    next({name: 'Home'})
  }
  else {
    next()
  }
})


export default router
