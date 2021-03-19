import RegionAPI from '../../api/region';

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        regions: [],
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
        regions (state) {
            return state.regions;
        },
    },
    mutations: {
        ['FETCHING_REGIONS'](state) {
            state.regions = [];
        },
        ['FETCHING_REGIONS_SUCCESS'](state, regions) {
            state.regions = regions;
        },
        ['FETCHING_REGIONS_ERROR'](state, error) {
            state.regions = [];
        },
    },
    actions: {
        fetchAll ({commit}) {
            commit('FETCHING_REGIONS');
            return RegionAPI.getAll()
                .then(res => commit('FETCHING_REGIONS_SUCCESS', res.data))
                .catch(err => commit('FETCHING_REGIONS_ERROR', err));
        },
    },
}