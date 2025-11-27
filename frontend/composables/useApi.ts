import axios from "axios";
import { useAuthStore } from "../store/authStore";

export const useApi = () => {
    const config = useRuntimeConfig();
    const authStore = useAuthStore();

    const api = axios.create({
        baseURL: config.public.apiUrl,
        headers: {
            'Content-Type': 'application/json',
        },
        // withCredentials: true,
    });

    api.interceptors.request.use((request) => {
        const token = authStore.token;

        if (token) {
            request.headers.Authorization = `Bearer ${token}`;
        }

        return request;
    }, (error) => {
        return Promise.reject(error);
    });

    return api;
};
