<template>
  <div class="card">
    <div class="card-header">
      <h3>{{ $t("navs.import-data") }}</h3>
    </div>
    <div class="card-body">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <b-form novalidate>
            <b-form-group
              label-cols-sm="4"
              label-cols-lg="3"
              :label="this.$t('views.import.file')"
              label-for="file"
              >
              <b-form-file
                id="file"
                v-model="form.file"
                :state="Boolean(form.file)"
                accept=".xls, .xlsx"
                :placeholder="this.$t('views.import.choose-file')"
                drop-placeholder="Drop file here..."
                aria-describedby="fileFeedback"
              />
              <b-form-invalid-feedback id="fileFeedback">
                This field is required
              </b-form-invalid-feedback>
            </b-form-group>
            <button type="button" class="btn btn-primary float-right" v-on:click="onSubmit" :disabled="invalidForm">{{ $t("views.global.save") }}</button>
          </b-form>
        </div>
      </div>
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div v-if="success" class="alert alert-success mt-5" role="alert">
            {{ $t("views.import.success") }}
          </div>
          <div v-if="error" class="alert alert-danger mt-5" role="alert">
            {{ $t("views.import.fail") }}
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
    name: 'ImportData',
    components: { Loading },
    data: () => {
      return {
        form: {
          file: '',
        },
      }
    },
    created (){
    },
    computed: {
      isLoading () {
        return this.$store.getters['importData/isLoading'];
      },
      success () {
        return this.$store.getters['importData/success'];
      },
      error () {
        return this.$store.getters['importData/error'];
      },
      invalidForm () {
        return !this.form.file;
      },
    },
    methods: {
      onSubmit() {
        this.$store.dispatch('importData/importData', this.form.file);
      },
      init() {
        this.success = false;
        this.form.file = '';
      }
    },
  }
</script>
