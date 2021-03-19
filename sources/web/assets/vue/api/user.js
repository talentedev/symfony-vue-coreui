import axios from 'axios';

export default {
    createUser (login, password, role, email, group) {
        return axios.post(
            '/api/user/create',
            {
                login : login,
                password: password,
                role: role,
                email: email,
                group: group
            }
        );
    },
    registerUser (login, email, role, group) {
        return axios.post(
            '/api/user/register',
            {
                login : login,
                role: role,
                email: email,
                group: group
            }
        );
    },
    activateUser (token, password) {
        return axios.post(
            '/api/user/activate',
            {
                token : token,
                password: password
            }
        );
    },
    getAll (groupIds) {
        if (groupIds) {
            groupIds = groupIds.join(',');
        }
        return axios.get('/api/users', {
            params: {
                groupIds: groupIds,
            }
        });
    },
    getRoles () {
        return axios.get('/api/roles');
    },
    getGroups () {
        return axios.get('/api/groups');
    },
    deleteUser (userId) {
        return axios.get('/api/user/delete', {
            params: {
                user_id: userId
            }
        });
    },
    updateUser (id, login, password, role, email, group) {
        return axios.put(
            '/api/user/update',
            {
                userId: id,
                login : login,
                password: password,
                role: role,
                email: email,
                group: group
            }
        );
    },
}