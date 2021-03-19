<template>
    <router-view></router-view>
</template>

<script>
    import axios from 'axios';
    import _ from 'lodash';

    export default {
        name: 'App',
        created () {
            let isAuthenticated = JSON.parse(this.$parent.$el.attributes['data-is-authenticated'].value),
                roles = JSON.parse(this.$parent.$el.attributes['data-roles'].value),
                group = JSON.parse(this.$parent.$el.attributes['data-group'].value);

            let redirect = this.$route.query.redirect;

            if (!isAuthenticated) {
                let rememberMeToken = this.$store.getters['security/rememberMeToken'];
                if (!_.isNil(rememberMeToken)) {
                    let payload = { rememberMeToken: rememberMeToken };
                    this.$store.dispatch('security/rememberMeLogin', payload)
                        .then(() => {
                            if (this.$store.getters['security/isAuthenticated']) {
                                if (!this.$store.getters['security/hasError']) {
                                    if (typeof redirect !== 'undefined' && redirect !== '/') {
                                        this.$router.push({path: redirect});
                                    } else {
                                        this.$router.push({path: '/home'});
                                    }
                                }
                            }
                    });
                }
            } else {
                let payload = { isAuthenticated: isAuthenticated, roles: roles, group: group };
                this.$store.dispatch('security/onRefresh', payload);
            }

            axios.interceptors.response.use(undefined, (err) => {
                return new Promise(() => {
                    if (err.response.status === 403) {
                        this.$router.push({path: '/login'}).then(
                            this.$router.go(0)
                        );
                    } else if (err.response.status === 500) {
                        document.open();
                        document.write(err.response.data);
                        document.close();
                    }
                    throw err;
                });
            });
        },
        computed: {
            isAuthenticated () {
                return this.$store.getters['security/isAuthenticated']
            },
        }
    }
</script>