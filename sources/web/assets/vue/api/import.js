import axios from 'axios';

export default {
    importData (file) {
        let formData = new FormData();
        formData.append('file', file);
        return axios.post('/api/save-import-online-database', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
    },
    fetchResult (commodites, types, countries, from, to) {
        if (commodites) {
            commodites = commodites.join(',');
        }
        if (types) {
            types = types.join(',');
        }
        if (countries) {
            countries = countries.join(',');
        }
        return axios.get('/api/get-online-database-result', {
            params: {
                commodity: commodites,
                type: types,
                country: countries,
                from: from,
                to: to
            }
        });
    },
    fetchPairs () {
        return axios.get('/api/get-imported-commodities-types');
    },
    fetchRegionCountries () {
        return axios.get('/api/get-imported-regions-countries');
    }
}