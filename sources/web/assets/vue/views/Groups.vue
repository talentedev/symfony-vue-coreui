<template>
  <div class="card">
    <div class="card-header">
      <h3>{{ $t("navs.create-group") }}</h3>
    </div>
    <div class="card-body">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <b-form novalidate>
            <b-form-group
              label-cols-sm="4"
              label-cols-lg="3"
              :label="this.$t('views.group.name')"
              label-for="name"
              >
              <b-form-input type="text" id="name" v-model="form.name"/>
            </b-form-group>
            <button type="button" class="btn btn-primary float-right" v-on:click="onSubmit" :disabled="invalidForm">{{ $t("views.global.save") }}</button>
          </b-form>
          <div v-if="success" class="alert alert-success mt-5" role="alert">
            {{ $t("views.group.success") }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'Groups',
    data: () => {
      return {
        form: {
          name: '',
        },
        success: false,
      }
    },
    created (){
    },
    computed: {
      invalidForm () {
        return !this.form.name;
      },
    },
    methods: {
      onSubmit() {
        this.$store.dispatch('group/createGroup', this.form.name)
          .then(res => {
            this.success = true;
            setTimeout(() => this.init(), 2000);
          });
      },
      init() {
        this.success = false;
        this.form.name = '';
      }
    },
  }
</script>
