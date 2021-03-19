import axios from 'axios';

export default {
    getAll () {
        return axios.get('/api/groups');
    },
    createGroup (name) {
        return axios.post(
            '/api/create-group',
            {
                name : name,
            }
        );
    },
}