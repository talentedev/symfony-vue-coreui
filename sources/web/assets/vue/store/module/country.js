import CountryAPI from '../../api/country';

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        countries: [],
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
        countries (state) {
            return state.countries;
        },
    },
    mutations: {
        ['FETCHING_COUNTRIES'](state) {
            state.countries = [];
        },
        ['FETCHING_COUNTRIES_SUCCESS'](state, countries) {
            state.countries = countries;
        },
        ['FETCHING_COUNTRIES_ERROR'](state, error) {
            state.countries = [];
        },
    },
    actions: {
        fetchCountries ({commit}, regionIds) {
            commit('FETCHING_COUNTRIES');
            return CountryAPI.getAll(regionIds)
                .then(res => commit('FETCHING_COUNTRIES_SUCCESS', res.data))
                .catch(err => commit('FETCHING_COUNTRIES_ERROR', err));
        },
    },
}