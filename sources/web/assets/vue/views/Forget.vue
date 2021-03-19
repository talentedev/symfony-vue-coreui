<template>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-8">
                <div class="card-group">
                    <div class="card p-4">
                        <div class="card-body">
                            <h1>Forgot password ?</h1>
                            <p class="text-muted">Please enter your email to reset your password</p>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="icon-lock"></i></span>
                                </div>
                                <input v-model="email" type="email" class="form-control"/>
                            </div>
                            <div v-if="isLoading" class="row col mb-3">
                                <p>Loading ...</p>
                            </div>
                            <div v-if="success" class="mb-3" role="alert">
                                <div class="alert alert-success col" role="alert">We sent you an email.</div>
                            </div>
                            <div v-if="hasError" class="mb-3" role="alert">
                                <div class="alert alert-danger col" role="alert">
                                    {{ error.response.data.message }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <button class="btn btn-primary px-4" type="submit"
                                            @click="performForget()"
                                            :disabled="email.length === 0 || isLoading">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Forget",
        data() {
            return {
                email: '',
            }
        },
        computed: {
            success() {
                return this.$store.getters['security/success'];
            },
            hasError() {
                return this.$store.getters['security/passwordHasError'];
            },
            error() {
                return this.$store.getters['security/errorPassword'];
            },
            isLoading() {
                return this.$store.getters['security/isLoading'];
            }
        },
        methods: {
            performForget() {
                this.$store.dispatch('security/forgotPassword', this.email);
            }
        }
    }
</script>

<style scoped>

</style>