<template>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2 class="login-title">
                    Admin de El Pirata
                </h2>
            </div>

            <form class="login-form" method="POST" @submit.prevent="login()">
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        v-model="user.email" 
                        required 
                        class="form-input"
                        placeholder="Email"
                    />
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Mot de passe</label>
                    <div class="password-container">
                        <input 
                            :type="!showed_password ? 'password' : 'text'" 
                            id="password" 
                            v-model="user.password"
                            required 
                            class="form-input password-input"
                            placeholder="Mot de passe"
                        />
                        <button 
                            type="button" 
                            @click="showPassword()"
                            class="password-toggle"
                        >
                            <svg v-if="showed_password" class="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 012.24-3.478M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                            </svg>
                            <svg v-else class="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <button 
                        type="submit" 
                        :disabled="loading" 
                        class="login-button"
                    >
                        <svg v-if="loading" class="loading-spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                        </svg>
                        <span>{{ loading ? 'Connexion...' : 'Se connecter' }}</span>
                    </button>
                </div>
            </form>

            <div class="forgot-password">
                <span class="forgot-text">Mot de passe oublié? </span>
                <a href="/forgot_password" class="forgot-link">
                    ici!
                </a>
            </div>
        </div>
    </div>

    <Toast></Toast>

    <Dialog v-model:visible="showDialog" header="Vérification OTP" :modal="true" :closable="false"
        :style="{ width: '400px' }">
        <div>
            <p>Veuillez entrer le code OTP envoyé par email :</p>
            <InputOtp v-model="optCode" :length="6" />
        </div>
        <template #footer>
            <Button label="Annuler" icon="pi pi-times" @click="showDialog = false" class="p-button-text" />
            <Button label="Vérifier" icon="pi pi-check" @click="verifyOtp" />
        </template>
    </Dialog>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { toast } from "vue3-toastify";
import { useAuthStore } from '../store/authStore'
import { useApi } from '../composables/useApi'
import { useStyleLoader } from '../composables/useStyleLoader'

// Charger les styles critiques immédiatement
const { loadCriticalStyles } = useStyleLoader()

onMounted(() => {
  loadCriticalStyles()
})

const user = ref({
    email: null,
    password: null,
});

const optCode = ref(null);
const showed_password = ref(false);
const loading = ref(false);
const showDialog = ref(false);

const authStore = useAuthStore();

const login = async () => {
    loading.value = true;
    let loginResponse = await useApi().post("admin/login", {
        email: user.value.email,
        password: user.value.password
    });

    if (loginResponse.data.state == "success") {
        let data = loginResponse.data;

        if (data.user.auth_two_factor) {
            showDialog.value = true;
            loading.value = false;
            return 0;
        }

        if (data.token) {
            await authStore.setLoginInfo({
                user: {
                    name: data.user.name,
                    email: data.user.email,
                    id: data.user.id,
                    role: data.user.role
                },
                token: data.token
            });

            navigateTo('/dashboard');
        }
    }
    loading.value = false;
    if (loginResponse.data.state == "error") {
        toast(loginResponse.data.message, {
            position: "top-right",
            type: "error",
            theme: "colored",
            timeout: 4000,
            closeOnClick: true,
            pauseOnHover: false,
            showCloseButtonOnHover: false,
            hideProgressBar: false,
        });
    }
};

const showPassword = async () => {
    showed_password.value = !showed_password.value;
};

const verifyOtp = async () => {
    let verifyOPT = await useApi().post('/admin/verify/opt', {
        code: optCode.value
    });
    let result = verifyOPT.data;

    toast(result.message, {
        position: "top-right",
        type: result.state,
        theme: "colored",
        timeout: 4000,
        closeOnClick: true,
        pauseOnHover: false,
        showCloseButtonOnHover: false,
        hideProgressBar: false,
    });

    if (result.state == "success") {
        if (result.token) {
            await authStore.setLoginInfo({
                user: {
                    name: result.user.name,
                    email: result.user.email,
                    id: result.user.id,
                    role: result.user.role
                },
                token: result.token
            });

            navigateTo('/dashboard');
        }
    }
};
</script>

<style scoped>
/* Styles personnalisés pour éviter les conflits avec PrimeVue */
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #111111;
    padding: 48px 16px;
}

.login-card {
    max-width: 400px;
    width: 100%;
    background-color: #111111;
    padding: 32px;
    border-radius: 12px;
    border: 1px solid #2b2b2b;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}

.login-header {
    text-align: center;
    margin-bottom: 24px;
}

.login-title {
    font-size: 28px;
    font-weight: bold;
    color: white;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    margin: 0;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: white;
    margin-bottom: 4px;
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    border-radius: 8px;
    border: 1px solid #374151;
    background-color: #171e26;
    color: #d1d5db;
    font-size: 16px;
    transition: all 0.2s ease;
    box-sizing: border-box;
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input::placeholder {
    color: #9ca3af;
}

.password-container {
    position: relative;
    display: flex;
    align-items: center;
}

.password-input {
    padding-right: 48px;
}

.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s ease;
}

.password-toggle:hover {
    color: #d1d5db;
}

.eye-icon {
    width: 20px;
    height: 20px;
}

.login-button {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px 16px;
    background: linear-gradient(to bottom, #383f4d, #2F3542);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 8px;
}

.login-button:hover:not(:disabled) {
    background: linear-gradient(to bottom, #4a5568, #374151);
    transform: translateY(-1px);
}

.login-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.loading-spinner {
    width: 20px;
    height: 20px;
    margin-right: 8px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.forgot-password {
    text-align: center;
    margin-top: 24px;
}

.forgot-text {
    color: #9ca3af;
    font-size: 14px;
}

.forgot-link {
    color: white;
    font-weight: 500;
    text-decoration: none;
    transition: color 0.2s ease;
}

.forgot-link:hover {
    color: #d1d5db;
}

/* Responsive */
@media (max-width: 640px) {
    .login-container {
        padding: 24px 16px;
    }
    
    .login-card {
        padding: 24px;
    }
    
    .login-title {
        font-size: 24px;
    }
}
</style>