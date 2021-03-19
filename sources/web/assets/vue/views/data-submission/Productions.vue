<template>
  <div class="card productions">
    <div class="card-header">
      <div class="row justify-content-between">
        <div class="col">
          <span class="h3">{{ selectedGroup && selectedGroup.name }}</span>
        </div>
        <div class="col-md-auto align-self-center">
          <div class="d-inline-block">
            <label>{{ $t("views.submission.before") }}: </label>
          </div>
        </div>
        <div class="col-md-auto align-self-center">
          <div class="d-inline-block">
            <datepicker v-model="date" :disabledDates="disabledDates" minimum-view="month" format="MM/yyyy" :bootstrap-styling="true">
              <span slot="afterDateInput" class="animated-placeholder">
                <i class="cui-calendar"></i>
              </span>
            </datepicker>
          </div>
        </div>
        <div class="col-md-auto align-self-center">
          <div v-if="isAdmin" class="d-inline-block">
            <multiselect
              v-model="selectedGroup"
              :options="groups"
              :multiple="false"
              label="name" track-by="id"></multiselect>
          </div>
        </div>
        <div class="col-md-auto align-self-center">
          <button type="button" class="btn btn-primary" @click="getProductions">{{ $t("views.submission.select") }}</button>
          <button v-if="isAdmin" type="button" class="btn btn-success" v-b-modal.add-company>{{ $t("views.submission.add-company") }}</button>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="animated fadeIn">
        <b-card v-for="(production, key) in productions" :key="key" :header="key">
          <div class="card" v-for="company in production" :key="company.id">
            <div class="card-header">
              <span class="h4">{{ company.name }}</span>
              <button v-if="isAdmin && company.status==='active'" type="button" class="btn btn-primary float-right ml-2" v-b-modal.add-commodity @click="addCommodity(company)">{{ $t("views.submission.add-commodity") }}</button>
              <button v-if="isAdmin  && company.status==='active'" type="button" class="btn btn-danger float-right" v-b-modal.confirmation @click="closeCompany(company)">{{ $t("views.submission.close-company") }}</button>
              <button v-if="isAdmin  && company.status==='closed'" type="button" class="btn btn-success float-right" @click="relaunchCompany(company)">{{ $t("views.submission.relaunch-company") }}</button>
            </div>
            <div class="card-body p-0">
              <div v-if="Boolean(company.products.length) && company.status==='active'" v-for="product in company.products" :key="product.id" class="p-3 border-bottom">
                <div class="row justify-content-between pb-2">
                  <div class="col align-self-center align-self-center">
                    <span>{{ product.commodity.name }}</span>
                  </div>
                  <div class="col-md-auto">
                    <button v-if="isAdmin && product.status==='active'" type="button" class="btn btn-danger" v-b-modal.confirmation @click="stopCommodity(product)">{{ $t("views.submission.stop-commodity") }}</button>
                    <button v-if="isAdmin && product.status==='stopped'" type="button" class="btn btn-success" @click="reopenCommodity(product)">{{ $t("views.submission.reopen-commodity") }}</button>
                  </div>
                </div>
                <production-table
                  v-if="product.status==='active'"
                  :months="lastSixMonths"
                  :id="key.replace(/\s/g, '') + company.id + product.id"
                  :product="product.id"
                  :tableData="product.productions"
                  @updated="getProductions">
                </production-table>
              </div>
              <div v-if="!Boolean(company.products.length)" class="m-3">No commodity</div>
            </div>
          </div>
        </b-card>
      </div>
    </div>
    <loading :active.sync="isLoading" :is-full-page="false"></loading>
    <add-company-modal id="add-company" :group="selectedGroup" @submitted="getProductions"></add-company-modal>
    <add-commodity-modal id="add-commodity" :company="selectedCompany" @submitted="getProductions"></add-commodity-modal>
    <confirmation-modal id="confirmation" :content="confirmData" @ok="getProductions"></confirmation-modal>
  </div>
</template>

<script>
  import Datepicker from 'vuejs-datepicker';
  import Multiselect from 'vue-multiselect';
  import Loading from 'vue-loading-overlay';
  import ProductionTable from './ProductionTable';
  import AddCompanyModal from './modals/AddCompany';
  import AddCommodityModal from './modals/AddCommodity';
  import ConfirmationModal from './modals/Confirmation';
  import moment from 'moment';
  moment.locale('en');

  export default {
    name: 'Productions',
    components: { Datepicker, Multiselect, Loading, ProductionTable, AddCompanyModal, AddCommodityModal, ConfirmationModal },
    data: () => {
      return {
        lastSixMonths: null,
        date: new Date(),
        disabledDates: {
          from: new Date()
        },
        selectedGroup: null,
        selectedCompany: null,
        confirmData: null,
        roles: [],
      }
    },
    created (){
      this.$store.dispatch('group/fetchGroups');
      this.getLastSixMonths();
    },
    computed: {
      isLoading () {
        return this.$store.getters['production/isLoading'];
      },
      isAdmin () {
        const group = this.$store.getters['security/group'];
        const isAdmin = this.$store.getters['security/hasRole']('ADMIN');
        if(!isAdmin) {
          this.selectedGroup = group;
            this.getProductions();
        }
        return isAdmin;
      },
      groups () {
        const groups = this.$store.getters['group/groups'];
        if (!this.selectedGroup && groups.length > 0) {
          this.selectedGroup = groups[0];
            this.getProductions();
        }
        return groups;
      },
      productions () {
          return this.$store.getters['production/productions'];
      }
    },
    methods: {
      getProductions() {
        this.getLastSixMonths();
        if (this.selectedGroup) {
            this.$store.dispatch('production/fetchAll', {
                groupId: this.selectedGroup.id,
                date: moment(this.date).format()
            });
        }
      },
      addCommodity(company) {
        this.selectedCompany = company;
      },
      closeCompany(company) {
        this.confirmData = {
          text: 'Are you sure to close this company?',
          type: 'company',
          id: company.id
        };
      },
      relaunchCompany(company) {
        this.$store.dispatch('company/relaunchCompany', company.id)
          .then(res => this.getProductions());
      },
      stopCommodity(product) {
        this.confirmData = {
          text: 'Are you sure to stop this commodity?',
          type: 'commodity',
          id: product.id
        };
      },
      reopenCommodity(product) {
        this.$store.dispatch('commodity/reopenCommodity', product.id)
          .then(res => this.getProductions());
      },
        getLastSixMonths() {
          let momentDate = moment(this.date);
          let months = [];
          for (var i = 0; i < 6; i++) {
              momentDate.subtract(1,'months')
              months.push(momentDate.format('MMM YYYY'));
          }
          this.lastSixMonths = months.reverse();
        },
    }
  }
</script>