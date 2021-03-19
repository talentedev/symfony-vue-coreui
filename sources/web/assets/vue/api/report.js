import axios from 'axios';

export default {
    fetchCategories () {
        return axios.get('/api/report-categories');
    },
    fetchSubCategories (categoryIds) {
        return axios.get('/api/report-sub-categories', {
             params: {
                report_categories_id: categoryIds.join(',')
             }
        });
    },
    createReport (name, date, category, subCategory, file) {
        let formData = new FormData();
        formData.append('name', name);
        formData.append('date', date);
        formData.append('report_categories_id', category);
        formData.append('report_sub_categories_id', subCategory);
        formData.append('file', file);
        return axios.post('/api/save-report', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
    },
    fetchReports (categoryIds, subCategoryIds, offset) {
        if (categoryIds) {
            categoryIds = categoryIds.join(',')
        }
        if (subCategoryIds) {
            subCategoryIds = subCategoryIds.join(',')
        }
        return axios.get('/api/reports', {
            params: {
                report_categories_id: categoryIds,
                report_sub_categories_id: subCategoryIds,
                offset: offset
            }
        });
    },
    deleteReport (reportId) {
        return axios.get('/api/delete-report', {
            params: {
                report_id: reportId
            }
        });
    },
}