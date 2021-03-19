import ImportAPI from '../../api/import';

export default {
    namespaced: true,
    state: {
        isLoading: false,
        success: false,
        error: null,
        data: [],
        pairs: [],
        regionCounties: []
    },
    getters: {
        isLoading (state) {
            return state.isLoading;
        },
        hasError (state) {
            return state.error !== null;
        },
        success (state) {
            return state.success;
        },
        error (state) {
            return state.error;
        },
        data (state) {
            return state.data;
        },
        pairs (state) {
            return state.pairs;
        },
        regionCounties (state) {
            return state.regionCounties;
        }
    },
    mutations: {
        ['IMPORTING_DATA'](state) {
            state.isLoading = true;
            state.success = false;
            state.error = null;
        },
        ['IMPORTING_DATA_SUCCESS'](state, report) {
            state.isLoading = false;
            state.success = true;
            state.error = null;
        },
        ['IMPORTING_DATA_ERROR'](state, error) {
            state.isLoading = false;
            state.success = false;
            state.error = error;
        },
        ['FETCHING_DATA'](state) {
            state.isLoading = true;
            state.error = null;
            state.data = [];
        },
        ['FETCHING_DATA_SUCCESS'](state, data) {
            state.isLoading = false;
            state.error = null;
            state.data = data;
        },
        ['FETCHING_DATA_ERROR'](state, error) {
            state.isLoading = false;
            state.error = error;
            state.data = [];
        },
        ['FETCHING_PAIRS'](state) {
            state.error = null;
            state.pairs = [];
        },
        ['FETCHING_PAIRS_SUCCESS'](state, pairs) {
            state.error = null;
            state.pairs = pairs;
        },
        ['FETCHING_PAIRS_ERROR'](state, error) {
            state.error = error;
            state.pairs = [];
        },
        ['FETCHING_REGION-COUNTRIES'](state) {
            state.error = null;
            state.regionCounties = [];
        },
        ['FETCHING_REGION-COUNTRIES_SUCCESS'](state, regionCounties) {
            state.error = null;
            state.regionCounties = regionCounties;
        },
        ['FETCHING_REGION-COUNTRIES_ERROR'](state, error) {
            state.error = error;
            state.regionCounties = [];
        },
    },
    actions: {
        importData ({commit}, file) {
            commit('IMPORTING_DATA');
            return ImportAPI.importData(file)
                .then(res => {commit('IMPORTING_DATA_SUCCESS', res.data)})
                .catch(err => {commit('IMPORTING_DATA_ERROR', err)});
        },
        fetchResult ({commit}, params) {
            commit('FETCHING_DATA');
            return ImportAPI.fetchResult(params.commodities, params.types, params.countries, params.from, params.to)
                .then(res => commit('FETCHING_DATA_SUCCESS', res.data))
                .catch(err => commit('FETCHING_DATA_ERROR', err));
        },
        fetchPairs ({commit}) {
            commit('FETCHING_PAIRS');
            return ImportAPI.fetchPairs()
                .then(res => commit('FETCHING_PAIRS_SUCCESS', res.data))
                .catch(err => commit('FETCHING_PAIRS_ERROR', err));
        },
        fetchRegionCountries ({commit}) {
            commit('FETCHING_REGION-COUNTRIES');
            return ImportAPI.fetchRegionCountries()
                .then(res => commit('FETCHING_REGION-COUNTRIES_SUCCESS', res.data))
                .catch(err => commit('FETCHING_REGION-COUNTRIES_ERROR', err));
        }
    },
}