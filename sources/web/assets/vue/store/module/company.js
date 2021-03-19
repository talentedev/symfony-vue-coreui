import CompanyAPI from '../../api/company';

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        companies: [],
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
        companies (state) {
            return state.companies;
        },
    },
    mutations: {
        ['CREATING_COMPANY'](state) {
            state.isLoading = true;
            state.error = null;
        },
        ['CREATING_COMPANY_SUCCESS'](state, company) {
            state.isLoading = false;
            state.error = null;
            state.companies.unshift(company);
        },
        ['CREATING_COMPANY_ERROR'](state, error) {
            state.isLoading = false;
            state.error = error;
            state.companies = [];
        },
        ['CLOSE_COMPANY'](state) {
            state.error = null;
        },
        ['CLOSE_COMPANY_SUCCESS'](state, company) {
            state.error = null;
        },
        ['CLOSE_COMPANY_ERROR'](state, error) {
            state.error = error;
        },
        ['RELAUNCH_COMPANY'](state) {
            state.error = null;
        },
        ['RELAUNCH_COMPANY_SUCCESS'](state, company) {
            state.error = null;
        },
        ['RELAUNCH_COMPANY_ERROR'](state, error) {
            state.error = error;
        },
    },
    actions: {
        createCompany ({commit}, company) {
            commit('CREATING_COMPANY');
            return CompanyAPI.createCompany(company.groupId, company.countryId, company.name)
                .then(res => {commit('CREATING_COMPANY_SUCCESS', res.data)})
                .catch(err => {commit('CREATING_COMPANY_ERROR', err)});
        },
        closeCompany ({commit}, companyId) {
            commit('CLOSE_COMPANY');
            return CompanyAPI.closeCompany(companyId)
                .then(res => commit('CLOSE_COMPANY_SUCCESS', res.data))
                .catch(err => commit('CLOSE_COMPANY_ERROR', err));
        },
        relaunchCompany ({commit}, companyId) {
            commit('RELAUNCH_COMPANY');
            return CompanyAPI.relaunchCompany(companyId)
                .then(res => commit('RELAUNCH_COMPANY_SUCCESS', res.data))
                .catch(err => commit('RELAUNCH_COMPANY_ERROR', err));
        },
    },
}