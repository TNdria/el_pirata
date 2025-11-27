import { useAuthStore } from '../store/authStore'

export default defineNuxtRouteMiddleware((to) => {
    const auth = useAuthStore();

    if (!auth.isAuthenticated && to.path.startsWith('/dashboard')) {
        return navigateTo('/login', { replace: true });
    }

    if (auth.isAuthenticated && to.path === '/login') {
        return navigateTo('/dashboard', { replace: true });
    }
});