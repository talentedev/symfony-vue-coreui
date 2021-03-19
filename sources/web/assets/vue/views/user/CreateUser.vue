<template>
  <div class="card">
    <div class="card-header">
      <span class="h3">Add new user</span>
    </div>
    <div class="card-body">
      <div class="animated fadeIn">
        <b-form novalidate>
          <b-form-group
            label-cols-sm="4"
            label-cols-lg="3"
            label="Login (email)"
            label-for="login"
            description="Please enter valid mail address."
            >
            <b-form-input type="email" id="login" v-model="form.login" :state="validEmail(form.login)" aria-describedby="loginFeedback" placeholder="Login (email)"/>
            <b-form-invalid-feedback id="loginFeedback">
              This field is invalid
            </b-form-invalid-feedback>
          </b-form-group>

          <!--

          <b-form-group
            label-cols-sm="4"
            label-cols-lg="3"
            label="Email"
            label-for="email"
            placeholder="Enter un email"
            >
            <b-form-input type="email" id="email" v-model="form.email" :state="validEmail(form.email)" aria-describedby="emailFeedback"/>
            <b-form-invalid-feedback id="emailFeedback">
              This field is invalid
            </b-form-invalid-feedback>
          </b-form-group>

          -->

          <b-form-group
            label-cols-sm="4"
            label-cols-lg="3"
            label="Role"
            label-for="role"
            >
            <b-form-select id="role" v-model="form.role" :options="roles" :state="Boolean(form.role)" aria-describedby="roleFeedback"/>
            <b-form-invalid-feedback id="roleFeedback">
              This field is required
            </b-form-invalid-feedback>
          </b-form-group>
          <b-form-group
              label-cols-sm="4"
              label-cols-lg="3"
              label="Group"
              label-for="group"
              >
              <b-form-select id="group" v-model="form.group" :options="groups" :state="Boolean(form.group)" aria-describedby="groupFeedback"/>
              <b-form-invalid-feedback id="groupFeedback">
                This field is required
              </b-form-invalid-feedback>
            </b-form-group>
          <button type="button" class="btn btn-primary" v-on:click="performUser" :disabled="invalidForm">Save</button>
        </b-form>
      </div>
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div v-if="success" class="alert alert-success mt-5" role="alert">
            User successfully saved. An email has been sent to {{ savedLogin }}.
          </div>
          <div v-if="error" class="alert alert-danger mt-5" role="alert">
            Error: {{ error.response.data.message }}
          </div>
        </div>
      </div>
      <loading :active.sync="isLoading" :is-full-page="false"></loading>
    </div>
  </div>
</template>

<script>
  import Loading from 'vue-loading-overlay';

  export default {
    name: "CreateUser",
    components: { Loading },
    data () {
      return {
        form: {
          login: '',
          role: null,
          group: null,
        },
        savedLogin: '',
      };
    },
    created () {
      this.$store.dispatch('user/fetchRoles');
      this.$store.dispatch('user/fetchGroups');
    },
    computed: {
      roles () {
        const roles = this.$store.getters['user/roles'];
        let arr = [{value: null, text: "Choose user role"}];
        roles.forEach(role => {
          arr.push({
            value: role,
            text: role
          })
        });
        return arr;
      },
      groups () {
        const groups = this.$store.getters['user/groups'];
        let arr = [{value: null, text: "Choose user group"}];
        groups.forEach(group => {
          arr.push({
            value: group.id,
            text: group.name
          })
        });
        return arr;
      },
      isLoading () {
        return this.$store.getters['user/isLoading'];
      },
      success () {
        return this.$store.getters['user/success'];
      },
      error () {
        return this.$store.getters['user/error'];
      },
      invalidForm() {
        return !this.form.login || !this.form.role || !this.form.group || !this.validEmail(this.form.login);
      }
    },
    methods: {
      performUser () {
        this.savedLogin = this.form.login;
        this.$store.dispatch('user/registerUser', {
            login: this.form.login,
            role : this.form.role,
            email: this.form.login,
            group: this.form.group
          }
        ).then(() => {
          this.form.login = '';
          this.form.role = null;
          this.form.group = null;
        });
      },
      validEmail (email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
      },
    },
  }
</script>

<style scoped>

</style>