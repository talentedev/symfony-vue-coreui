import axios from 'axios';

export default {
    getAll (regionIds) {
        if (regionIds) {
            regionIds = regionIds.join(',')
        }
        return axios.get('/api/countries', {
            params: {
                region_ids: regionIds,
            }
        });
    },
}