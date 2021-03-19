import axios from 'axios';

export default {
    createCompany (groupId, countryId, name) {
        return axios.post(
            '/api/save-company',
            {
                groupId : groupId,
                countryId: countryId,
                name: name,
            }
        );
    },
    closeCompany (companyId) {
        return axios.get('/api/close-company', {
            params: {
                company_id: companyId
            }
        });
    },
    relaunchCompany (companyId) {
        return axios.get('/api/relaunch-company', {
            params: {
                company_id: companyId
            }
        });
    },
}