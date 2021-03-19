import ProductionAPI from '../../api/production';

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        productions: null,
        maxDate: null,
        minDate: null,
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
        productions (state) {
            return state.productions;
        },
        maxDate (state) {
            return state.maxDate;
        },
        minDate (state) {
            return state.minDate;
        },
    },
    mutations: {
        ['FETCHING_PRODUCTIONS'](state) {
            state.isLoading = true;
            state.productions = null;
        },
        ['FETCHING_PRODUCTIONS_SUCCESS'](state, productions) {
            state.isLoading = false;
            state.productions = productions;
        },
        ['FETCHING_PRODUCTIONS_ERROR'](state, error) {
            state.isLoading = false;
        },
        ['SAVING_PRODUCTIONS'](state) {
            state.isLoading = true;
            state.error = null;
        },
        ['SAVING_PRODUCTIONS_SUCCESS'](state, product) {
            state.isLoading = false;
            state.error = null;
        },
        ['SAVING_PRODUCTIONS_ERROR'](state, error) {
            state.isLoading = false;
            state.error = error;
        },
        ['FETCHING_EXPORTS'](state) {
            state.isLoading = true;
            state.productions = null;
            state.maxDate = null;
            state.minDate = null;
        },
        ['FETCHING_EXPORTS_SUCCESS'](state, data) {
            state.isLoading = false;
            state.productions = data.countries;
            state.maxDate = data.maxDate;
            state.minDate = data.minDate;
        },
        ['FETCHING_EXPORTS_ERROR'](state, error) {
            state.isLoading = false;
            state.maxDate = null;
            state.minDate = null;
        },
    },
    actions: {
        fetchAll ({commit}, params) {
            commit('FETCHING_PRODUCTIONS');
            return ProductionAPI.getAll(params.groupId, params.date)
                .then(res => commit('FETCHING_PRODUCTIONS_SUCCESS', res.data))
                .catch(err => commit('FETCHING_PRODUCTIONS_ERROR', err));
        },
        saveProductions ({commit}, params) {
            commit('SAVING_PRODUCTIONS');
            return ProductionAPI.createProduction(params.productId, params.productions)
                .then(res => {commit('SAVING_PRODUCTIONS_SUCCESS', res.data)})
                .catch(err => commit('SAVING_PRODUCTIONS_ERROR', err));
        },
        getExportData ({commit}) {
            commit('FETCHING_EXPORTS');
            return ProductionAPI.getExportData()
                .then(res => commit('FETCHING_EXPORTS_SUCCESS', res.data))
                .catch(err => commit('FETCHING_EXPORTS_ERROR', err));
        }
    },
}