<template>
  <b-modal ref="add-company-modal" :id="id" title="Add company">
    <b-form novalidate>
      <b-form-group
        label-cols-sm="4"
        label-cols-lg="3"
        label="Group"
        label-for="group"
        >
        <b-form-input type="text" id="group" v-model="form.group" disabled/>
      </b-form-group>
      <b-form-group
        label-cols-sm="4"
        label-cols-lg="3"
        label="Company Name"
        label-for="company"
        >
        <b-form-input type="text" id="company" v-model="form.company" :state="Boolean(form.company)" aria-describedby="companyFeedback"/>
        <b-form-invalid-feedback id="companyFeedback">
          This field is required
        </b-form-invalid-feedback>
      </b-form-group>
      <b-form-group
        label-cols-sm="4"
        label-cols-lg="3"
        label="Country"
        label-for="country"
        >
        <b-form-select id="country" v-model="form.country" :options="countries" :state="Boolean(form.country)" aria-describedby="countryFeedback"/>
        <b-form-invalid-feedback id="countryFeedback">
          This field is required
        </b-form-invalid-feedback>
      </b-form-group>
    </b-form>
    <div slot="modal-footer">
      <button type="button" class="btn btn-secondary" v-on:click="hideModal">Close</button>
      <button type="button" class="btn btn-primary" v-on:click="onSubmit" :disabled="isLoading || invalidForm">Save</button>
    </div>
    <loading :active.sync="isLoading" :is-full-page="false"></loading>
  </b-modal>
</template>

<script>
  import Loading from 'vue-loading-overlay';

  export default {
    name: 'AddCompanyModal',
    components: { Loading },
    props: {
      id: {
        type: String,
        default: 'add-company'
      },
      group: {
        type: Object
      },
    },
    data: () => {
      return {
        selectedGroup: null,
        form: {
          group: '',
          company: '',
          country: '',
        },
      }
    },
    created (){
      this.$store.dispatch('country/fetchCountries');
    },
    computed: {
      isLoading () {
        return this.$store.getters['company/isLoading'];
      },
      invalidForm () {
        return !this.form.company || !this.form.country;
      },
      countries () {
        const countries = this.$store.getters['country/countries'];
        let arr = [];
        countries.forEach(country => arr.push({ value: country.id, text: country.name }));
        return arr;
      },
    },
    watch: {
      group (newVal, oldVal) {
        this.selectedGroup = newVal;
        this.form.group = newVal.name;
      }
    },
    methods: {
      hideModal() {
        this.$refs['add-company-modal'].hide();
      },
      onSubmit() {
        const params = {
          countryId: this.form.country,
          groupId: this.selectedGroup.id,
          name: this.form.company
        }
        this.$store.dispatch('company/createCompany', params)
          .then(res => {
            this.hideModal();
            this.$emit('submitted', true);
            this.form.company = '';
            this.form.country = '';
          });
      },
    }
  }
</script>