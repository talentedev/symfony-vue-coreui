<template>
  <div class="card online-database">
    <div class="card-header">
      <div class="row">
        <div class="col-sm-12 col-md-4">
          <span class="h3">{{ $t("navs.online-database") }}</span>
        </div>
        <div class="col-sm-12 col-md-8">
          <b-button variant="warning" class="ml-3 text-white float-right" @click="exportFile('csv')">{{ $t("views.online-db.export-csv") }}</b-button>
          <b-button variant="warning" class="text-white float-right" @click="exportFile('xlsx')">{{ $t("views.online-db.export-xls") }}</b-button>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="animated fadeIn">
        <div class="row">
          <div class="col-sm-12 col-md-6 col-lg-3">
            <div class="row mt-3">
              <div class="col-sm-12 col-md-4 text-center pt-2">
                <label>{{ $t("views.global.commodity") }}:</label>
              </div>
              <div class="col-sm-12 col-md-8">
                <multiselect
                  v-model="selectedCommodity"
                  :options="commodities"
                  :multiple="true" :taggable="true"
                  @input="changeCommodity"></multiselect>
              </div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6 col-lg-3">
            <div class="row mt-3">
              <div class="col-sm-12 col-md-4 text-center pt-2">
                <label>{{ $t("views.global.type") }}:</label>
              </div>
              <div class="col-sm-12 col-md-8">
                <multiselect
                  v-model="selectedType"
                  :options="types"
                  :multiple="true" :taggable="true"
                  @input="changeType"></multiselect>
              </div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6 col-lg-3">
            <div class="row mt-3">
              <div class="col-sm-12 col-md-4 text-center pt-2">
                <label>{{ $t("views.global.region") }}:</label>
              </div>
              <div class="col-sm-12 col-md-8">
                <multiselect
                  v-model="selectedRegion"
                  :options="regions"
                  :multiple="true" :taggable="true"
                  @input="changeRegion"></multiselect>
              </div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6 col-lg-3">
            <div class="row mt-3">
              <div class="col-sm-12 col-md-4 text-center pt-2">
                <label>{{ $t("views.global.country") }}:</label>
              </div>
              <div class="col-sm-12 col-md-8">
                <multiselect
                  v-model="selectedCountry"
                  :options="countries"
                  :multiple="true" :taggable="true"
                  @input="changeCountry"></multiselect>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-6 col-lg-3">
            <div class="row mt-3">
              <div class="col-sm-12 col-md-4 text-center pt-2">
                <label>{{ $t("views.global.from") }}:</label>
              </div>
              <div class="col-sm-12 col-md-8">
                <datepicker v-model="fromDate" :disabledDates="{from: lastMonth}" minimum-view="month" format="MM/yyyy" :bootstrap-styling="true">
                  <span slot="afterDateInput" class="animated-placeholder">
                    <i class="cui-calendar"></i>
                  </span>
                </datepicker>
              </div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6 col-lg-3">
            <div class="row mt-3">
              <div class="col-sm-12 col-md-4 text-center pt-2">
                <label>{{ $t("views.global.to") }}:</label>
              </div>
              <div class="col-sm-12 col-md-8">
                <datepicker v-model="toDate" :disabledDates="{from: lastMonth}" minimum-view="month" format="MM/yyyy" :bootstrap-styling="true">
                  <span slot="afterDateInput" class="animated-placeholder">
                    <i class="cui-calendar"></i>
                  </span>
                </datepicker>
              </div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6 col-lg-3">
            <div class="row mt-3 justify-content-end">
              <div class="col-sm-12 col-md-8">
                <b-button block variant="success" @click="getResult">{{ $t("views.global.filter") }}</b-button>
              </div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6 col-lg-3">
            <div class="row mt-3 justify-content-end">
              <div class="col-sm-12 col-md-8">
                <b-button block variant="primary" @click="init">{{ $t("views.global.clear") }}</b-button>
              </div>
            </div>
          </div>
        </div>

        <import-data-table class="mt-4" :tableData="data" :from="fromDate" :to="toDate"></import-data-table>
        <loading :active.sync="isLoading" :is-full-page="false"></loading>
      </div>
    </div>
  </div>
</template>

<script>
  import Datepicker from 'vuejs-datepicker';
  import Multiselect from 'vue-multiselect';
  import Loading from 'vue-loading-overlay';
  import importDataTable from './ImportDataTable';
  import moment from 'moment';
  moment.locale('en');

  export default {
    name: 'OnlineDatabase',
    components: { Datepicker, Multiselect, Loading, importDataTable },
    data: () => {
      return {
        selectedCommodity: null,
        selectedType: null,
        selectedRegion: [],
        selectedCountry: [],
        fromDate: new Date(),
        toDate: new Date(),
        commodities: [],
        types: [],
        regions: [],
        countries: []
      }
    },
    created (){
      this.$store.dispatch('importData/fetchPairs');
      this.$store.dispatch('importData/fetchRegionCountries');
      this.init();
    },
    computed: {
      isLoading () {
        return this.$store.getters['importData/isLoading'];
      },
      allCommodities () {
        return this.$store.getters['importData/pairs'];
      },
      allRegionCountries () {
        return this.$store.getters['importData/regionCounties'];
      },
      data () {
        return this.$store.getters['importData/data'];
      },
      lastMonth () {
        return new Date(this.getlastMonth(1));
      }
    },
    methods: {
        getlastMonth (months) {
            let now = moment().startOf('month').add(14, 'days').add(12, 'hours');
            return now.subtract(months, 'month');
        },
      init() {
        // Set last 12 months.
        this.fromDate = new Date(this.getlastMonth(12));
        // Set last month.
        this.toDate = new Date(this.getlastMonth(1));

        // Set default commodity and type.
        if (this.commodities.length > 0) {
          if(this.commodities.some(commodity => commodity === "Mn Ore (Wet)")) {
            this.selectedCommodity = ["Mn Ore (Wet)"];
          } else {
            this.selectedCommodity = [this.commodities[0]];
          }
        }
        if (this.types.length > 0) {
          if(this.types.some(type => type === "Production")) {
            this.selectedType = ["Production"];
          } else {
            this.selectedType = [this.types[0]];
          }
        }
        this.selectedCountry = [];
        this.selectedRegion = [];

        this.types = this.getTypes();
      },
      getCommodities (pairs) {
        let commodities = [];
        pairs.forEach(pair => {
          const isExist = commodities.some(commodity => commodity === pair.commodity);
          if (!isExist) {
            commodities.push(pair.commodity);
          }
          // Init selected commodity
          if (this.selectedCommodity === null && pair.commodity === "Mn Ore (Wet)") {
            this.selectedCommodity = [pair.commodity];
            this.changeCommodity();
          }
        });
        // Init selected commodity if there is no "Mn Ore (Wet)".
        if (this.selectedCommodity === null && commodities.length > 0) {
          this.selectedCommodity = [commodities[0]];
          this.changeCommodity();
        }
        return commodities;
      },
      getTypes () {
        const pairs = this.$store.getters['importData/pairs'];
        let types = [];
        pairs.forEach(pair => {
          const isExist = types.some(type => type === pair.type);
          if (!isExist) {
            types.push(pair.type);
          }
          // Init selected type
          if (this.selectedType === null && pair.type === "Production") {
            this.selectedType = [pair.type];
          }
        });
        // Init selected type if there is no "Production".
        if (this.selectedType === null && types.length > 0) {
          this.selectedType = [types[0]];
        }
        return types;
      },
      getRegions (pairs) {
        let regions = [];
        pairs.forEach(pair => {
          const isExist = regions.some(region => region === pair.region);
          if (!isExist) {
            regions.push(pair.region);
          }
        });
        return regions.sort();
      },
      getCountries () {
        const pairs = this.$store.getters['importData/regionCounties'];
        let countries = [];
        pairs.forEach(pair => {
          const isExist = countries.some(country => country === pair.country);
          if (!isExist) {
            countries.push(pair.country);
          }
        });
        return countries.sort();
      },
      getResult() {
        let selectedCountries = this.selectedCountry;
        if (this.selectedRegion.length > 0 && this.selectedCountry.length <= 0) {
          // Select all countries inside of selected region in case of selecting only region.
          selectedCountries = this.countries;
        }
        this.$store.dispatch('importData/fetchResult', {
          commodities: this.selectedCommodity,
          types: this.selectedType,
          countries: selectedCountries,
          from: this.fromDate,
          to: this.toDate
        });
      },
      changeCommodity() {
        if (this.selectedCommodity && this.selectedCommodity.length > 0) {
          const pairs = this.$store.getters['importData/pairs'];
          let types = [];
          pairs.forEach(pair => {
            const isExist = this.selectedCommodity.some(commodity => commodity === pair.commodity);
            const isSame = types.some(type => type === pair.type);
            if (isExist && !isSame) {
              types.push(pair.type);
            }
          });
          this.types = types;
        } else {
          this.types = this.getTypes();
        }
        this.changeType();
      },
      changeType() {
        const pairs = this.$store.getters['importData/pairs'];
        if (this.selectedType && this.selectedType.length > 0) {
          let commodities = [];
          pairs.forEach(pair => {
            const isExist = this.selectedType.some(type => type === pair.type);
            const isSame = commodities.some(commodity => commodity === pair.commodity);
            if (isExist && !isSame) {
              commodities.push(pair.commodity);
            }
          });
          this.commodities = commodities;
        } else {
          this.commodities = this.getCommodities(pairs);
        }
      },
      changeRegion() {
        if (this.selectedRegion && this.selectedRegion.length > 0) {
          const pairs = this.$store.getters['importData/regionCounties'];
          let countries = [];
          pairs.forEach(pair => {
            const isExist = this.selectedRegion.some(region => region === pair.region);
            const isSame = countries.some(country => country === pair.country);
            if (isExist && !isSame) {
              countries.push(pair.country);
            }
          });
          this.countries = countries;
        } else {
          this.countries = this.getCountries();
        }
        this.changeCountry();
      },
      changeCountry() {
        const pairs = this.$store.getters['importData/regionCounties'];
        if (this.selectedCountry && this.selectedCountry.length > 0) {
          let regions = [];
          pairs.forEach(pair => {
            const isExist = this.selectedCountry.some(country => country === pair.country);
            const isSame = regions.some(region => region === pair.region);
            if (isExist && !isSame) {
              regions.push(pair.region);
            }
          });
          this.regions = regions;
        } else {
          this.regions = this.getRegions(pairs);
        }
      },
      exportFile(fileType) {
        let url = '/api/get-online-database-file?';
        let commodities = '';
        let types = '';
        let countries = '';
        if (this.selectedCommodity) {
            commodities = this.selectedCommodity.join(',');
        }
        if (this.selectedType) {
            types = this.selectedType.join(',');
        }
        if (this.selectedCountry) {
            countries = this.selectedCountry.join(',');
        }
        url += 'commodity=' + commodities;
        url += '&type=' + types;
        url += '&country=' + countries;
        url += '&from=' + JSON.stringify(this.fromDate).replace(/"/g, '');
        url += '&to=' + JSON.stringify(this.toDate).replace(/"/g, '');
        url += '&file_type=' + fileType;
        window.open(url, '_blank');
      },
    },
    watch: {
      allCommodities (newVal, oldVal) {
        this.commodities = this.getCommodities(newVal);
        this.types = this.getTypes();
        if (newVal.length > 0) {
          this.getResult();
        }
      },
      allRegionCountries (newVal, oldVal) {
        this.regions = this.getRegions(newVal);
        this.countries = this.getCountries();
      },
      fromDate (newVal, oldVal) {
        this.disabledDatesTo = {
          to: this.fromDate,
          from: new Date()
        };
      }
    },
  }
</script>
