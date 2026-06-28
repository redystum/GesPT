import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', name: 'home', component: HomeView },
    { path: '/login', name: 'login', component: () => import('../views/LoginView.vue') },
    { path: '/reset-password', name: 'reset-password', component: () => import('../views/ResetPasswordView.vue') },
    { path: '/admin', name: 'admin', component: () => import('../views/AdminView.vue') },
    { path: '/plano/:id', name: 'compare', component: () => import('../views/PlanoView.vue') },
    { path: '/compare', name: 'compare', component: () => import('../views/ComparingView.vue') },
    { path: '/wizard', name: 'wizard', component: () => import('../views/PlanWizardView.vue') },
    { path: '/wizard/edit/:id', name: 'plan-edit-wizard', component: () => import('../views/PlanEditWizardView.vue') },
    { path: '/curso/:id', name: 'course', component: () => import('../views/CourseView.vue') },
    { path: '/uc/:id', name: 'uc-detail', component: () => import('../views/UcDetailView.vue') }
  ]
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  // If the user is NOT logged in, they can ONLY access login and reset-password pages
  const allowedGuestRoutes = ['login', 'reset-password']
  if (!authStore.isLoggedIn && !allowedGuestRoutes.includes(to.name)) {
    return next({ name: 'login' })
  }

  // If the user IS logged in and tries to access the login page, redirect to home
  if (authStore.isLoggedIn && to.path === '/login') {
    return next({ name: 'home' })
  }

  // Protect /admin
  if (to.path === '/admin') {
    const hasAdminAccess = authStore.hasPermission([
      'roles.view_any',
      'users.view_any',
      'login_dominios.view_any',
      'audit_logs.view_any'
    ])
    if (!hasAdminAccess) {
      return next({ name: 'home' })
    }
  }

  // Protect /wizard and /wizard/edit/:id
  if (to.path === '/wizard' || to.path.startsWith('/wizard/edit/')) {
    if (to.path === '/wizard' && !authStore.hasPermission('planos_estudo.create')) {
      return next({ name: 'home' })
    }
    if (to.path.startsWith('/wizard/edit/') && !authStore.hasPermission('planos_estudo.update')) {
      return next({ name: 'home' })
    }
  }

  // Protect /curso/:id
  if (to.path.startsWith('/curso/')) {
    if (!authStore.hasPermission(['cursos.view'])) {
      return next({ name: 'home' })
    }
  }

  // Protect /uc/:id
  if (to.path.startsWith('/uc/')) {
    if (!authStore.hasPermission(['ucs.view'])) {
      return next({ name: 'home' })
    }
  }

  next()
})

export default router
