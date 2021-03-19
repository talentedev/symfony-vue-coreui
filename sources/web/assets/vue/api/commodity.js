import axios from 'axios';

export default {
    getAll () {
        return axios.get('/api/commodities');
    },
    stopCommodity (productId) {
        return axios.get('/api/stop-commodity', {
            params: {
                product_id: productId
            }
        });
    },
    reopenCommodity (productId) {
        return axios.get('/api/reopen-commodity', {
            params: {
                product_id: productId
            }
        });
    },
}