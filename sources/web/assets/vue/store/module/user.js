import UserAPI from '../../api/user';

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        success: false,
        users: [],
        roles: [],
        groups: [],
    },
    getters: {
        isLoading (state) {
            return state.isLoading;
        },
        hasError (state) {
            return state.error !== null;
        },
        error (state) {
            return state.error;
        },
        success (state) {
            return state.success;
        },
        hasRole (state) {
            return state.roles;
        },
        users (state) {
            return state.users;
        },
        roles (state) {
            return state.roles;
        },
        groups (state) {
            return state.groups;
        },
    },
    mutations: {
        ['CREATING_USER'](state) {
            state.isLoading = true;
            state.error = null;
            state.success = false;
        },
        ['CREATING_USER_SUCCESS'](state, user) {
            state.isLoading = false;
            state.error = null;
            state.success = true;
            state.users.unshift(user);
        },
        ['CREATING_USER_ERROR'](state, error) {
            state.isLoading = false;
            state.error = error;
            state.success = false;
            state.users = [];
        },
        ['FETCHING_USERS'](state) {
            state.isLoading = true;
            state.error = null;
            state.users = [];
        },
        ['FETCHING_USERS_SUCCESS'](state, users) {
            state.isLoading = false;
            state.error = null;
            state.users = users;
        },
        ['FETCHING_USERS_ERROR'](state, error) {
            state.isLoading = false;
            state.error = error;
            state.users = [];
        },
        ['DELETING_USER'](state) {
            state.isLoading = true;
            state.error = null;
        },
        ['DELETING_USER_SUCCESS'](state) {
            state.isLoading = false;
            state.error = null;
        },
        ['DELETING_USER_ERROR'](state, error) {
            state.isLoading = false;
            state.error = error;
        },
        ['FETCHING_ROLES'](state) {
            state.roles = [];
        },
        ['FETCHING_ROLES_SUCCESS'](state, roles) {
            state.roles = roles;
        },
        ['FETCHING_ROLES_ERROR'](state, error) {
            state.roles = [];
        },
        ['FETCHING_GROUPS'](state) {
            state.groups = [];
        },
        ['FETCHING_GROUPS_SUCCESS'](state, groups) {
            state.groups = groups;
        },
        ['FETCHING_GROUPS_ERROR'](state, error) {
            state.groups = [];
        },
    },
    actions: {
        createUser ({commit}, user) {
            commit('CREATING_USER');
            return UserAPI.createUser(user.login, user.password, user.role, user.email, user.group)
                .then(res => {commit('CREATING_USER_SUCCESS', res.data)})
                .catch(err => {commit('CREATING_USER_ERROR', err)});
        },
        registerUser ({commit}, user) {
            commit('CREATING_USER');
            return UserAPI.registerUser(user.login, user.email, user.role, user.group)
                .then(res => {commit('CREATING_USER_SUCCESS', res.data)})
                .catch(err => {commit('CREATING_USER_ERROR', err)});
        },
        activateUser ({commit}, user) {
            commit('CREATING_USER');
            return UserAPI.activateUser(user.token, user.password)
                .then(res => {commit('CREATING_USER_SUCCESS', res.data)})
                .catch(err => {commit('CREATING_USER_ERROR', err)});
        },
        fetchUsers ({commit}, params) {
            commit('FETCHING_USERS');
            return UserAPI.getAll(params.groupIds)
                .then(res => commit('FETCHING_USERS_SUCCESS', res.data))
                .catch(err => commit('FETCHING_USERS_ERROR', err));
        },
        deleteUser ({commit}, userId) {
            commit('DELETING_USER');
            return UserAPI.deleteUser(userId)
                .then(res => commit('DELETING_USER_SUCCESS', res.data))
                .catch(err => commit('DELETING_USER_ERROR', err));
        },
        updateUser ({commit}, user) {
            commit('CREATING_USER');
            return UserAPI.updateUser(user.id, user.login, user.password, user.role, user.email, user.group)
                .then(res => {commit('CREATING_USER_SUCCESS', res.data)})
                .catch(err => {commit('CREATING_USER_ERROR', err)});
        },
        fetchRoles ({commit}) {
            commit('FETCHING_ROLES');
            return UserAPI.getRoles()
                .then(res => commit('FETCHING_ROLES_SUCCESS', res.data))
                .catch(err => commit('FETCHING_ROLES_ERROR', err));
        },
        fetchGroups ({commit}) {
            commit('FETCHING_GROUPS');
            return UserAPI.getGroups()
                .then(res => commit('FETCHING_GROUPS_SUCCESS', res.data))
                .catch(err => commit('FETCHING_GROUPS_ERROR', err));
        },
    },
}