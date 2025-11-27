import { useAuthStore } from '../store/authStore'

export default defineNuxtRouteMiddleware((to) => {
    if (process.client) {
        
        if (to.query.preview === 'true') {
            return;
        }

        const auth = useAuthStore();

        const redirectTo = to.fullPath
        const redirectCookie = useCookie('redirectTo')
        redirectCookie.value = redirectTo

        if (!auth.isAuthenticated) {
            return navigateTo('/login')
        }
    }
});