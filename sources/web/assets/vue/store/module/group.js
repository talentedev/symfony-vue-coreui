import GroupAPI from '../../api/group';

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
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
        groups (state) {
            return state.groups;
        },
    },
    mutations: {
        ['FETCHING_GROUPS'](state) {
            state.groups = [];
        },
        ['FETCHING_GROUPS_SUCCESS'](state, groups) {
            state.groups = groups;
        },
        ['FETCHING_GROUPS_ERROR'](state, error) {
            state.groups = [];
        },
        ['CREATING_GROUP'](state) {
            state.isLoading = true;
            state.error = null;
        },
        ['CREATING_GROUP_SUCCESS'](state, group) {
            state.isLoading = false;
            state.error = null;
            state.groups.unshift(group);
        },
        ['CREATING_GROUP_ERROR'](state, error) {
            state.isLoading = false;
            state.error = error;
            state.groups = [];
        },
    },
    actions: {
        fetchGroups ({commit}) {
            commit('FETCHING_GROUPS');
            return GroupAPI.getAll()
                .then(res => commit('FETCHING_GROUPS_SUCCESS', res.data))
                .catch(err => commit('FETCHING_GROUPS_ERROR', err));
        },
        createGroup ({commit}, name) {
            commit('CREATING_GROUP');
            return GroupAPI.createGroup(name)
                .then(res => {commit('CREATING_GROUP_SUCCESS', res.data)})
                .catch(err => {commit('CREATING_GROUP_ERROR', err)});
        },
    },
}