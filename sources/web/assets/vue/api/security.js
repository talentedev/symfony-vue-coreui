import axios from 'axios';

export default {
    login (login, password, rememberMe) {
        return axios.post(
            '/api/security/login',
            {
                username: login,
                password: password,
                rememberMe: rememberMe,
            }
        );
    },
    rememberMeLogin (rememberMeToken) {
        return axios.post(
            '/api/security/remember-me-login',
            {
                rememberMeToken: rememberMeToken,
            }
        );
    },
    logout () {
        return axios.get('/api/security/logout');
    },
    forgotPassword(email) {
        return axios.post('/api/security/forgetPassword', {
           email
        });
    },
    resetPassword (token, password) {
        return axios.post(
            '/api/security/reset-password',
            {
                token : token,
                password: password
            }
        );
    },
}