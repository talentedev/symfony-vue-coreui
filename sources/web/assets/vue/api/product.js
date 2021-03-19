import axios from 'axios';

export default {
    createProduct (commodityId, companyId, startDate, capacity) {
        return axios.post(
            '/api/create-product',
            {
                commodityId : commodityId,
                companyId: companyId,
                startDate: startDate,
                capacity: capacity,
            }
        );
    },
}