import SecurityAPI from '../api/security';
import _ from 'lodash';

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        errorPassword: null,
        success: false,
        isAuthenticated: false,
        roles: [],
        group: [],
    },
    getters: {
        isLoading(state) {
            return state.isLoading;
        },
        hasError(state) {
            return state.error !== null;
        },
        error(state) {
            return state.error;
        },
        passwordHasError(state) {
            return state.errorPassword !== null;
        },
        errorPassword(state) {
            return state.errorPassword;
        },
        success(state) {
            return state.success;
        },
        isAuthenticated(state) {
            return state.isAuthenticated;
        },
        group(state) {
            return state.group;
        },
        rememberMeToken(state) {
            try {
                return localStorage.getItem('REMEMBER_ME_TOKEN')
            } catch (error) {
                return null;
            }
        },
        hasRole(state) {
            return role => {
                return state.roles && state.roles.indexOf(role) !== -1;
            }
        },
    },
    mutations: {
        ['AUTHENTICATING'](state) {
            state.isLoading = true;
            state.error = null;
            state.isAuthenticated = false;
            state.roles = [];
            state.group = [];
        },
        ['AUTHENTICATING_SUCCESS'](state, user) {
            state.isLoading = false;
            state.error = null;
            state.isAuthenticated = true;
            state.roles = user.roles;
            state.group = user.group;
        },
        ['AUTHENTICATING_ERROR'](state, error) {
            state.isLoading = false;
            state.error = error;
            state.isAuthenticated = false;
            state.roles = [];
            state.group = [];
        },
        ['PROVIDING_DATA_ON_REFRESH_SUCCESS'](state, payload) {
            state.isLoading = false;
            state.error = null;
            state.errorPassword = null;
            state.isAuthenticated = payload.isAuthenticated;
            state.roles = payload.roles;
            state.group = payload.group;
        },
        ['LOGOUT'](state) {
            state.isLoading = true;
            state.error = null;
            state.isAuthenticated = false;
            state.roles = [];
            state.group = [];
        },
        ['LOGOUT_SUCCESS'](state) {
            state.isLoading = false;
            state.error = null;
            state.isAuthenticated = false;
            state.roles = [];
            state.group = [];
        },
        ['FORGOT_PASSWORD'](state) {
            state.isLoading = true;
            state.errorPassword = null;
            state.success = false;
        },
        ['FORGOT_PASSWORD_SUCCESS'](state, result) {
            state.isLoading = false;
            state.errorPassword = null;
            state.success = true;
        },
        ['FORGOT_PASSWORD_ERROR'](state, error) {
            state.isLoading = false;
            state.errorPassword = error;
            state.success = false;
        },
    },
    actions: {
        login({commit}, payload) {
            commit('AUTHENTICATING');
            return SecurityAPI.login(payload.login, payload.password, payload.rememberMe)
                .then(res => {
                    if (payload.rememberMe) {
                        try {
                            localStorage.setItem('REMEMBER_ME_TOKEN', res.data.rememberMeToken);
                        } catch (error) {
                            // let's do nothing.
                        }
                    }
                    commit('AUTHENTICATING_SUCCESS', res.data)
                })
                .catch(err => commit('AUTHENTICATING_ERROR', err));
        },
        onRefresh({commit}, payload) {
            commit('PROVIDING_DATA_ON_REFRESH_SUCCESS', payload);
        },
        rememberMeLogin({commit}, payload) {
            commit('AUTHENTICATING');
            return SecurityAPI.rememberMeLogin(payload.rememberMeToken)
                .then(res => commit('AUTHENTICATING_SUCCESS', res.data))
                .catch(err => commit('AUTHENTICATING_ERROR', err));
        },
        logout({commit}) {
            commit('LOGOUT');
            return SecurityAPI.logout()
                .then(res => commit('LOGOUT_SUCCESS', res.data));
        },
        forgotPassword({commit}, email) {
            commit('FORGOT_PASSWORD');
            return SecurityAPI.forgotPassword(email)
                .then(res => commit('FORGOT_PASSWORD_SUCCESS', res.data))
                .catch(err => commit('FORGOT_PASSWORD_ERROR', err));
        },
        resetPassword({commit}, params) {
            commit('FORGOT_PASSWORD');
            return SecurityAPI.resetPassword(params.token, params.password)
                .then(res => commit('FORGOT_PASSWORD_SUCCESS', res.data))
                .catch(err => commit('FORGOT_PASSWORD_ERROR', err));
        },
    },
}