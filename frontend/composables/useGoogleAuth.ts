import axios from "axios";
import { useAuthStore } from '../store/authStore';

import {
    useTokenClient,
    type AuthCodeFlowSuccessResponse,
    type AuthCodeFlowErrorResponse,
    // type CredentialResponse
} from "vue3-google-signin";


export const useGmailAuth = () => {
    // Initialisation de l'authentification One Tap
    const handleOnSuccess = (response: AuthCodeFlowSuccessResponse) => {
        console.log("Access Token: ", response.access_token);

        axios.get('https://www.googleapis.com/oauth2/v1/userinfo?access_token=' + response.access_token).then(userInfo => {
            let user = userInfo.data;

            useApi().post('login/gmail/', {
                userInfo: user
            }).then(async (response: any) => {
                // console.log(response);
                // localStorage.setItem('token', response.data.token);
                const auth = useAuthStore()
                let data = response.data;
                if (data.token) {
                    await auth.setLoginInfo({
                        user: data.user,
                        token: data.token
                    });
                }

                const redirectTo = useCookie('redirectTo');
                navigateTo(redirectTo.value || '/profil');
                redirectTo.value = null;

            }).catch()

        }).catch()


    };

    const handleOnError = (errorResponse: AuthCodeFlowErrorResponse) => {
        console.log("Error: ", errorResponse);
    };

    const { isReady, login } = useTokenClient({
        onSuccess: handleOnSuccess,
        onError: handleOnError,
        // other options
    });

    return {
        login,
        isReady,
    };
};