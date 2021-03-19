import ProductAPI from '../../api/product';

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        products: [],
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
        products (state) {
            return state.products;
        },
    },
    mutations: {
        ['CREATING_PRODUCT'](state) {
            state.isLoading = true;
            state.error = null;
        },
        ['CREATING_PRODUCT_SUCCESS'](state, product) {
            state.isLoading = false;
            state.error = null;
            state.products.unshift(product);
        },
        ['CREATING_PRODUCT_ERROR'](state, error) {
            state.isLoading = false;
            state.error = error;
            state.products = [];
        },
    },
    actions: {
        createProduct ({commit}, product) {
            commit('CREATING_PRODUCT');
            return ProductAPI.createProduct(product.commodityId, product.companyId, product.startDate, product.capacity)
                .then(res => {commit('CREATING_PRODUCT_SUCCESS', res.data)})
                .catch(err => {commit('CREATING_PRODUCT_ERROR', err)});
        },
    },
}