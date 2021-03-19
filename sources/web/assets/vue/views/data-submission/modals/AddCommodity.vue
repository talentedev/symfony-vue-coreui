<template>
  <b-modal ref="add-commodity-modal" :id="id" title="Add commodity">
    <b-form novalidate>
      <b-form-group
        label-cols-sm="4"
        label-cols-lg="3"
        label="Commodity"
        >
        <multiselect
          v-model="form.commodity"
          :options="restCommodities"
          :multiple="false"
          label="name" track-by="id"></multiselect>
      </b-form-group>
      <b-form-group
        label-cols-sm="4"
        label-cols-lg="3"
        label="Start date"
        >
        <datepicker v-model="form.startDate" :disabledDates="disabledDates" minimum-view="month" format="MM/yyyy" :bootstrap-styling="true">
          <span slot="afterDateInput" class="animated-placeholder">
            <i class="cui-calendar"></i>
          </span>
        </datepicker>
      </b-form-group>
      <b-form-group
        label-cols-sm="4"
        label-cols-lg="3"
        label="Capacity"
        label-for="capacity"
        >
        <b-form-input type="number" id="capacity" v-model="form.capacity" :state="Boolean(form.capacity)" aria-describedby="capacityFeedback"/>
        <b-form-invalid-feedback id="capacityFeedback">
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
  import Datepicker from 'vuejs-datepicker';
  import Multiselect from 'vue-multiselect';
  import Loading from 'vue-loading-overlay';
  import moment from 'moment';
  moment.locale('en');

  export default {
    name: 'AddCommodityModal',
    components: { Datepicker, Multiselect, Loading },
    props: {
      id: {
        type: String,
        default: 'add-commodity'
      },
      company: {
        type: Object
      },
    },
    data: () => {
      return {
        selectedCompany: null,
        restCommodities: [],
        disabledDates: {
          from: new Date()
        },
        form: {
          commodity: '',
          startDate: new Date(),
          capacity: '',
        },
      }
    },
    created (){
      this.$store.dispatch('commodity/fetchAll');
    },
    computed: {
      isLoading () {
        return this.$store.getters['product/isLoading'];
      },
      invalidForm () {
        return !this.form.commodity || !this.form.startDate || !this.form.capacity;
      },
      allCommodities () {
        return this.$store.getters['commodity/commodities'];
      },
    },
    methods: {
      hideModal() {
        this.$refs['add-commodity-modal'].hide();
      },
      onSubmit() {
        let date = moment(this.form.startDate, "MMM YYYY").startOf('month').add(14, 'days').add(12, 'hours').format();
        const params = {
          commodityId : this.form.commodity.id,
          companyId: this.selectedCompany.id,
          startDate: date,
          capacity: this.form.capacity,
        }
        this.$store.dispatch('product/createProduct', params)
          .then(res => {
            this.hideModal();
            this.$emit('submitted', true);
            this.form = {
              commodity: '',
              startDate: new Date(),
              capacity: '',
            }
          });
      },
    },
    watch: {
      company (newVal, oldVal) {
        this.selectedCompany = newVal;
        let addedCommodities = [];
        this.selectedCompany.products.forEach(product => {
          addedCommodities.push(product.commodity);
        });
        this.restCommodities = this.allCommodities.filter(commodity => {
          let isExist = true;
          addedCommodities.forEach(value => {
            if (value.id === commodity.id) {
              isExist = false;
            }
          });
          return isExist;
        });
      },
    },
  }
</script>