<template xmlns:v-on="http://www.w3.org/1999/xhtml">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-8">
                <div class="card-group">
                    <div class="card p-4">
                        <div class="card-body">
                            <h1>Welcome to Web extranet</h1>
                            <p class="text-muted">Please login</p>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="icon-user"></i></span>
                                </div>
                                <input class="form-control" type="text" placeholder="Login" v-model="login">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="icon-lock"></i></span>
                                </div>
                                <input class="form-control" type="password" placeholder="Password" v-model="password" v-on:keyup.13="performLogin()">
                            </div>
                            <div v-if="isLoading" class="row col mb-3">
                                <p>
                                    Login ...
                                </p>
                            </div>
                            <div v-if="hasError" class="mb-3" role="alert">
                                <div class="alert alert-danger col" role="alert">
                                    Invalid login or password.
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-12 col-form-label">
                                    <div class="form-check form-check-inline mr-1">
                                        <input type="checkbox" v-model="rememberMe" class="mr-1"
                                               id="remember">
                                        <label class="form-check-label" for="remember">Remember me</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <button class="btn btn-primary px-4" type="button"
                                            @click="performLogin()"
                                            :disabled="login.length === 0 || password.length === 0 || isLoading">
                                        Login
                                    </button>
                                </div>
                                <div class="col-6 text-right">
                                    <button class="btn btn-link px-0" type="button" @click="redirectForget">
                                        Forgot password?
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!--<div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%">-->
                    <!--<div class="card-body text-center">-->
                    <!--<div>-->
                    <!--<h2>Sign up</h2>-->
                    <!--<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor-->
                    <!--incididunt ut labore et dolore magna aliqua.</p>-->
                    <!--<button class="btn btn-primary active mt-3" type="button">Register Now!</button>-->
                    <!--</div>-->
                    <!--</div>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Login',
        data() {
            return {
                login: '',
                password: '',
                rememberMe: false,
            };
        },
        created() {
            let redirect = this.$route.query.redirect;

            if (this.$store.getters['security/isAuthenticated']) {
                if (typeof redirect !== 'undefined'  && redirect !== '/') {
                    this.$router.push({path: redirect});
                } else {
                    this.$router.push({path: '/home'});
                }
            }
        },
        computed: {
            isLoading() {
                return this.$store.getters['security/isLoading'];
            },
            hasError() {
                return this.$store.getters['security/hasError'];
            },
            error() {
                return this.$store.getters['security/error'];
            },
        },
        methods: {
            performLogin() {
                let payload = {
                    login: this.$data.login,
                    password: this.$data.password,
                    rememberMe: this.$data.rememberMe
                };

                let redirect = this.$route.query.redirect;

                this.$store.dispatch('security/login', payload)
                    .then(() => {
                        if (!this.$store.getters['security/hasError']) {
                            if (typeof redirect !== 'undefined' && redirect !== '/') {
                                this.$router.push({path: redirect});
                            } else {
                                this.$router.push({path: '/home'});
                            }
                        }
                    });
            },
            redirectForget() {
                this.$router.push({path: '/forget'});
            }
        }
    }
</script>