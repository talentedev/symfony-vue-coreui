<template>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-8">
                <div class="card-group">
                    <div class="card p-4">
                        <div class="card-body">
                            <h1>Password reset</h1>
                            <p class="text-muted">Please reset your password.</p>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="icon-lock"></i></span>
                                </div>
                                <input v-model="password" type="password" class="form-control" />
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <button class="btn btn-primary px-4" type="button"
                                            @click="perform()"
                                            :disabled="password.length === 0">
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
        name: "ResetPassword",
        data() {
            return {
                password: ''
            }
        },
        methods: {
            perform () {
                const token = this.$route.query.token;
                if (token) {
                    this.$store.dispatch('security/resetPassword', {
                        token: token,
                        password: this.password
                    }).then(res => {
                        this.$router.push({path: '/login'});
                    });
                }
            }
        }
    }
</script>

<style scoped>

</style>