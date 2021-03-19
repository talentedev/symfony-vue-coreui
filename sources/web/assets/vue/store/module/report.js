import ReportAPI from '../../api/report';

export default {
    namespaced: true,
    state: {
        categories: [],
        subCategories: [],
        reports: [],
        isLoading: false,
        error: null,
        isDeleted: false,
    },
    getters: {
        categories (state) {
            return state.categories;
        },
        subCategories (state) {
            return state.subCategories;
        },
        isLoading (state) {
            return state.isLoading;
        },
        hasError (state) {
            return state.error !== null;
        },
        error (state) {
            return state.error;
        },
        reports (state) {
            return state.reports;
        },
        isDeleted (state) {
            return state.isDeleted;
        },
    },
    mutations: {
        ['FETCHING_CATEGORIES'](state) {
            state.error = null;
            state.categories = [];
        },
        ['FETCHING_CATEGORIES_SUCCESS'](state, categories) {
            state.error = null;
            state.categories = categories;
        },
        ['FETCHING_CATEGORIES_ERROR'](state, error) {
            state.error = error;
            state.categories = [];
        },
        ['FETCHING_SUB_CATEGORIES'](state) {
            state.error = null;
            state.subCategories = [];
        },
        ['FETCHING_SUB_CATEGORIES_SUCCESS'](state, subCategories) {
            state.error = null;
            state.subCategories = subCategories;
        },
        ['FETCHING_SUB_CATEGORIES_ERROR'](state, error) {
            state.error = error;
            state.subCategories = [];
        },
        ['CREATING_REPORT'](state) {
            state.isLoading = true;
            state.error = null;
        },
        ['CREATING_REPORT_SUCCESS'](state, report) {
            state.isLoading = false;
            state.error = null;
            state.reports.unshift(report);
        },
        ['CREATING_REPORT_ERROR'](state, error) {
            state.isLoading = false;
            state.error = error;
            state.reports = [];
        },
        ['FETCHING_REPORTS'](state) {
            state.isLoading = true;
            state.error = null;
            state.reports = [];
        },
        ['FETCHING_REPORTS_SUCCESS'](state, reports) {
            state.isLoading = false;
            state.error = null;
            state.reports = reports;
        },
        ['FETCHING_REPORTS_ERROR'](state, error) {
            state.isLoading = false;
            state.error = error;
            state.reports = [];
        },
        ['DELETING_REPORT_SUCCESS'](state, error) {
            state.isDeleted = false;
            state.error = error;
        },
        ['DELETING_REPORT_ERROR'](state, error) {
            state.isDeleted = true;
            state.error = error;
        },
    },
    actions: {
        fetchCategories ({commit}) {
            commit('FETCHING_CATEGORIES');
            return ReportAPI.fetchCategories()
                .then(res => commit('FETCHING_CATEGORIES_SUCCESS', res.data))
                .catch(err => commit('FETCHING_CATEGORIES_ERROR', err));
        },
        fetchSubCategories ({commit}, categoryIds) {
            commit('FETCHING_SUB_CATEGORIES');
            return ReportAPI.fetchSubCategories(categoryIds)
                .then(res => commit('FETCHING_SUB_CATEGORIES_SUCCESS', res.data))
                .catch(err => commit('FETCHING_SUB_CATEGORIES_ERROR', err));
        },
        createReport ({commit}, report) {
            commit('CREATING_REPORT');
            return ReportAPI.createReport(report.name, report.date, report.category, report.subCategory, report.file)
                .then(res => {commit('CREATING_REPORT_SUCCESS', res.data)})
                .catch(err => {commit('CREATING_REPORT_ERROR', err)});
        },
        fetchReports ({commit}, params) {
            commit('FETCHING_REPORTS');
            return ReportAPI.fetchReports(params.categoryIds, params.subCategoryIds, params.offset)
                .then(res => commit('FETCHING_REPORTS_SUCCESS', res.data))
                .catch(err => commit('FETCHING_REPORTS_ERROR', err));
        },
        deleteReport ({commit}, reportId) {
            return ReportAPI.deleteReport(reportId)
                .then(res => commit('DELETING_REPORT_SUCCESS', res.data))
                .catch(err => commit('DELETING_REPORT_ERROR', err));
        },
    },
}