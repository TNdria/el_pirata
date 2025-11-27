import axios from "axios";
import { useAuthStore } from "../store/authStore"; // ajuste le chemin selon ton projet
import { toast } from "vue3-toastify"
import { storeToRefs } from "pinia";

export const useApi = () => {
    const config = useRuntimeConfig();
    const authStore = useAuthStore();
    const { token } = storeToRefs(authStore);

    const api = axios.create({
        baseURL: config.public.apiUrl,
        headers: {
            'Content-Type': 'application/json',
        },
        // withCredentials: true,
    });

    api.interceptors.request.use((request) => {
       
        if (token.value) {
            request.headers.Authorization = `Bearer ${token.value}`;
        }

        return request;
    }, (error) => {
        return Promise.reject(error);
    });

    api.interceptors.response.use(
        (response) => response,
        (error) => {
            if (error.response && error.response.status === 401) {
                authStore.logout(); // méthode logout dans ton store
                toast("Votre session a expiré. Veuillez vous reconnecter.", {
                    position: "top-right",
                    type: "warning",
                    theme: "colored",
                    closeOnClick: true,
                    pauseOnHover: false,
                    showCloseButtonOnHover: false,
                    hideProgressBar: false,
                });
                
            }
            return Promise.reject(error);
        }
    );

    return api;
};
