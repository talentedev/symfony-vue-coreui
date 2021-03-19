<template>
    <div class="container">
        <form>
            <div class="form-group">
                <label for="login">Login</label>
                <input v-model="login" type="text" class="form-control"
                       id="login" aria-describedby="loginHelp" placeholder="Enter un login">
                <small id="loginHelp" class="form-text text-muted">Vous devez comuniquer votre login a personne.</small>
            </div>
            <div class="form-group">
                <label for="login">Email</label>
                <input v-model="email" type="email" class="form-control"
                       id="email" aria-describedby="emailHelp" placeholder="Enter un email">
                <small id="emailHelp" class="form-text text-muted">Nous de comuniquerons votre adresse mail a personne.</small>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input v-model="password" type="password" class="form-control"
                       id="password" placeholder="Entrer un mot de passe">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="role">Role</label>
                </div>
                <select v-model="role" class="custom-select" id="role">
                    <option selected>Choisir le role de l'utilisateur.</option>
                    <option value="ROLE_FOO">Foo</option>
                    <option value="ROLE_BAR">Bar</option>
                    <option value="ROLE_BAZ">Baz</option>
                </select>
            </div>
            <button type="button" @click="performUser()" class="btn btn-primary">Sauvegarder l'utilisateur</button>
        </form>
    </div>
</template>

<script>
    export default {
        name: "User",
        data () {
            return {
                login: '',
                password: '',
                email: '',
                role: ''
            };
        },
        methods: {
            performUser () {
                this.$store.dispatch('user/createUser', {
                        login: this.$data.login,
                        password: this.$data.password,
                        role : this.$data.role,
                        email: this.$data.email
                    }
                ).then(() => {
                    this.$data.login = '';
                    this.$data.password = '';
                    this.$data.role = '';
                    this.$data.email = '';
                });
            },
        },
    }
</script>

<style scoped>

</style>