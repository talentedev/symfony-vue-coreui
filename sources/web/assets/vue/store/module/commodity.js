import CommodityAPI from '../../api/commodity';

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        commodities: [],
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
        commodities (state) {
            return state.commodities;
        },
    },
    mutations: {
        ['FETCHING_COMMODITIES'](state) {
            state.commodities = [];
        },
        ['FETCHING_COMMODITIES_SUCCESS'](state, commodities) {
            state.commodities = commodities;
        },
        ['FETCHING_COMMODITIES_ERROR'](state, error) {
            state.commodities = [];
        },
        ['STOP_COMMODITY'](state) {
            state.error = null;
        },
        ['STOP_COMMODITY_SUCCESS'](state, commodity) {
            state.error = null;
        },
        ['STOP_COMMODITY_ERROR'](state, error) {
            state.error = error;
        },
    },
    actions: {
        fetchAll ({commit}) {
            commit('FETCHING_COMMODITIES');
            return CommodityAPI.getAll()
                .then(res => commit('FETCHING_COMMODITIES_SUCCESS', res.data))
                .catch(err => commit('FETCHING_COMMODITIES_ERROR', err));
        },
        stopCommodity ({commit}, productId) {
            commit('STOP_COMMODITY');
            return CommodityAPI.stopCommodity(productId)
                .then(res => commit('STOP_COMMODITY_SUCCESS', res.data))
                .catch(err => commit('STOP_COMMODITY_ERROR', err));
        },
        reopenCommodity ({commit}, productId) {
            commit('STOP_COMMODITY');
            return CommodityAPI.reopenCommodity(productId)
                .then(res => commit('STOP_COMMODITY_SUCCESS', res.data))
                .catch(err => commit('STOP_COMMODITY_ERROR', err));
        },
    },
}