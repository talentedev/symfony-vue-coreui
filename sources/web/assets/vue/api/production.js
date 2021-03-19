import axios from 'axios';

export default {
    getAll (groupId, date) {
        return axios.get('/api/productions', {
            params: {
                group_id: groupId,
                date: date
            }
        });
    },
    createProduction (productId, productions) {
        return axios.post(
            '/api/save-production',
            {
                product_id : productId,
                productions: productions,
            }
        );
    },
    getExportData () {
        return axios.get('/api/export-production');
    },
}