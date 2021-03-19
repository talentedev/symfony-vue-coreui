<template>
  <div class="card">
    <div class="card-header">
      <span class="h3">{{ $t("views.user.list") }}</span>
        <!--
      <b-button variant="outline-primary" class="float-right"
        v-if="isAdmin"
        @click="showModal">{{ $t("views.user.create") }}
      </b-button>
      -->
    </div>
    <div class="card-body">
      <div class="animated fadeIn">
        <div class="row mb-3">
          <div class="col-3 text-center pt-2">
            <label>{{ $t("views.global.groups") }} :</label>
          </div>
          <div class="col-4">
            <multiselect
              v-model="selectedGroup"
              :options="groups"
              :multiple="true" :taggable="true"
              @input="changeGroup"
              label="text" track-by="value"></multiselect>
          </div>
        </div>

        <user-table :table-data="users" :fields="fields" @delete="deleteUser" :per-page=20 hover small striped bordered fixed></user-table>
        <!--
        <user-table :table-data="users" :fields="fields" @delete="deleteUser" @update="updateUser" :per-page=20 hover small striped bordered fixed></user-table>
        -->
        <loading :active.sync="isLoading" :is-full-page="false"></loading>

        <!-- Modal -->
        <!--
        <b-modal ref="addUserModalRef" :title="modalTitle">
          <b-form novalidate>
            <b-form-group
              label-cols-sm="4"
              label-cols-lg="3"
              label="Login"
              label-for="login"
              placeholder="Enter un login"
              description="Vous devez comuniquer votre login a personne."
              >
              <b-form-input type="text" id="login" v-model="form.login" :state="Boolean(form.login)" aria-describedby="loginFeedback"/>
              <b-form-invalid-feedback id="loginFeedback">
                This field is required
              </b-form-invalid-feedback>
            </b-form-group>
            <b-form-group
              label-cols-sm="4"
              label-cols-lg="3"
              label="Email"
              label-for="email"
              placeholder="Enter un email"
              description="Nous de comuniquerons votre adresse mail a personne."
              >
              <b-form-input type="email" id="email" v-model="form.email" :state="validEmail(form.email)" aria-describedby="emailFeedback"/>
              <b-form-invalid-feedback id="emailFeedback">
                This field is invalid
              </b-form-invalid-feedback>
            </b-form-group>
            <b-form-group
              label-cols-sm="4"
              label-cols-lg="3"
              label="Password"
              label-for="password"
              placeholder="Entrer un mot de passe"
              >
              <b-form-input type="password" id="password" v-model="form.password" :state="Boolean(form.password)" aria-describedby="passwordFeedback"/>
              <b-form-invalid-feedback id="passwordFeedback">
                This field is required
              </b-form-invalid-feedback>
            </b-form-group>
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
          </b-form>
          <div slot="modal-footer">
            <button type="button" class="btn btn-primary" v-on:click="onSubmit" :disabled="invalidForm">Sauvegarder l'utilisateur</button>
          </div>
        </b-modal>
         -->
      </div>
    </div>
  </div>
</template>

<script>
  import Multiselect from 'vue-multiselect'
  import Loading from 'vue-loading-overlay';
  import UserTable from '../../components/UserTable.vue'

  export default {
    name: "ManageUser",
    components: { Multiselect, Loading, UserTable },
    data()
    {
      return {
        selectedGroup: null,
        fullPageLoading: false,
        modalTitle: 'Create user',
        isModify: false,
        defaultFields: [
          {key: 'login', sortable: true},
          {key: 'email', sortable: true},
          {key: 'group', sortable: true},
          {key: 'roles', sortable: true}
        ],
        form: {
          login: '',
          email: '',
          role: null,
          group: null,
          password: null
        },
      }
    },
    created () {
      this.$store.dispatch('user/fetchRoles');
      this.$store.dispatch('user/fetchGroups');
      this.$store.dispatch('user/fetchUsers', {
        groupIds: null
      });
    },
    computed: {
      isLoading () {
        return this.$store.getters['user/isLoading'];
      },
      hasError () {
        return this.$store.getters['user/hasError'];
      },
      error () {
        return this.$store.getters['user/error'];
      },
      users () {
        const users = this.$store.getters['user/users'];
        let arr = []
        users.forEach(user => {
          arr.push({
            id: user && user.id,
            login: user && user.login,
            email: user && user.email,
            group: user && user.group && user.group.name,
            roles: user && user.roles.join(', ')
          })
        })
        return arr;
      },
      roles () {
        const roles = this.$store.getters['user/roles'];
        let arr = [{value: null, text: "Choisir le role de l'utilisateur"}];
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
        let arr = [{value: null, text: "Choisir le groupe de l'utilisateur"}];
        groups.forEach(group => {
          arr.push({
            value: group.id,
            text: group.name
          })
        });
        return arr;
      },
      fields () {
        let fields = this.defaultFields;
        if (this.isAdmin) {
          fields.push({key: 'action'});
        }
        return fields;
      },
      isAdmin () {
        const isAdmin = this.$store.getters['security/hasRole']('ADMIN');
        return isAdmin;
      },
      invalidForm() {
        return !this.form.login || !this.form.email || !this.form.password || !this.form.role || !this.form.group
      }
    },
    methods: {
      initModal() {
        this.isModify = false
        this.modalTitle = 'Create user'
        this.form.login = ''
        this.form.email = ''
        this.form.password = ''
        this.form.role = null
        this.form.group = null
      },
      showModal() {
        this.initModal()
        this.$refs.addUserModalRef.show()
      },
      hideModal() {
        this.$refs.addUserModalRef.hide()
      },
      validEmail (email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
      },
      onSubmit(evt) {
        evt.preventDefault()
        let storeEvent = 'user/createUser'
        if (this.isModify) {
          storeEvent = 'user/updateUser'
        }
        this.$store.dispatch(storeEvent, {
            login: this.form.login,
            password: this.form.password,
            role : this.form.role,
            email: this.form.email,
            group: this.form.group,
            id: this.form.id || null
          }
        ).then(res => {
          this.hideModal()
          this.$store.dispatch('user/fetchUsers', {
            groupIds: null
          });
        })
      },
      deleteUser(userId) {
        this.$store.dispatch('user/deleteUser', userId)
           .then(res => {
             this.$store.dispatch('user/fetchUsers', {
                 groupIds: null
             })
           })
      },
      updateUser(user) {
        // this.form = {
        //   id: user.id,
        //   login: user.login,
        //   email: user.email,
        //   role: user.roles[0],
        //   password: null
        // }
        // this.modalTitle = 'Update user'
        // this.isModify = true
        // this.$refs.addUserModalRef.show()
      },
      changeGroup(groups) {
        let groupIds = []
        groups.forEach(group => groupIds.push(group.value))
        this.$store.dispatch('user/fetchUsers', {
          groupIds: groupIds
        })
      },
    }
  }
</script>
