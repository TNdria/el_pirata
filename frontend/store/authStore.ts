import CryptoJS from 'crypto-js';


export const useAuthStore = defineStore('auth', {
    state: (): {
        user: any | null
        token: string | null
        isAuthenticated: boolean
    } => ({
        user: null,
        token: null,
        isAuthenticated: false
    }),

    actions: {
        setUser(user: any) {
            this.user = user
            this.isAuthenticated = true
        },

        setToken(token: string) {
            this.token = token
        },

        logout() {
            this.user = null;
            this.token = null;
            this.isAuthenticated = false;
            window.location.href = "/";
        },

        async setLoginInfo(payload: { user: any; token: string }) {
            try {
                this.setUser(payload.user)

                if (payload.token) {
                    this.setToken(payload.token);
                }

            } catch (error) {
                console.error('Erreur lors du login', error)
            }
        }
    },
    persist: typeof window !== 'undefined' ? {
        storage: localStorage,
        serializer: {
            serialize: (value) => {
                // Accède à la configuration uniquement côté client
                const config = useRuntimeConfig()
                const secretKey = config.public.encryptionKey
                return CryptoJS.AES.encrypt(JSON.stringify(value), secretKey).toString()
            },
            deserialize: (value) => {
                const config = useRuntimeConfig()
                const secretKey = config.public.encryptionKey
                const bytes = CryptoJS.AES.decrypt(value, secretKey)
                return JSON.parse(bytes.toString(CryptoJS.enc.Utf8))
            },
        },
    } : false,
})