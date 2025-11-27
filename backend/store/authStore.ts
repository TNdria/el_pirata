import CryptoJS from 'crypto-js'

interface User {
    name: string
    email: string
    id: Number,
    role: Object
    // Ajoute d'autres propriétés si nécessaire
}

export const useAuthStore = defineStore('auth', {
    state: (): {
        user: User | null
        token: string | null
        isAuthenticated: boolean
    } => ({
        user: null,
        token: null,
        isAuthenticated: false
    }),

    actions: {
        setUser(user: User) {
            this.user = user
            this.isAuthenticated = true
        },

        setToken(token: string) {
            this.token = token
        },

        logout() {
            this.user = null
            this.token = null
            this.isAuthenticated = false
        },

        async setLoginInfo(payload: { user: User; token: string }) {
            try {
                this.setUser(payload.user)
                this.setToken(payload.token)
            } catch (error) {
                console.error('Erreur lors du login', error)
            }
        }
    },
    persist: typeof window !== 'undefined' ? {
        storage: localStorage,
        serializer: {
            serialize: (value) => {
                // Utilisation d'une clé fixe pour éviter les problèmes SSR
                const secretKey = '52LkLH4vi4wVGveTH' // Clé de chiffrement fixe
                return CryptoJS.AES.encrypt(JSON.stringify(value), secretKey).toString()
            },
            deserialize: (value) => {
                const secretKey = '52LkLH4vi4wVGveTH' // Clé de chiffrement fixe
                const bytes = CryptoJS.AES.decrypt(value, secretKey)
                return JSON.parse(bytes.toString(CryptoJS.enc.Utf8))
            },
        },
    } : false,
})
