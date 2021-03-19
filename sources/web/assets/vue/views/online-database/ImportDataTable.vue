<template>
  <general-table :table-data="data" :fields="fields" :per-page=20 hover small striped bordered fixed></general-table>
</template>

<script>
  import GeneralTable from '../../components/GeneralTable.vue';
  import moment from 'moment';
  moment.locale('en');

  export default {
    name: 'import-data-table',
    components: { GeneralTable },
    inheritAttrs: false,
    props: {
      tableData: {
        type: Array
      },
      from: {
        type: Date
      },
      to: {
        type: Date
      }
    },
    data: () => {
      return {
        data: [],
        fields: []
      }
    },
    created (){
      this.setTableFields(this.from, this.to);
      this.convertData(this.tableData);
    },
    methods: {
      setTableFields(fromDate, toDate) {
          this.fields = [
              {key: 'commodity', sortable: true, thStyle: { whiteSpace: 'nowrap'}},
              {key: 'type', sortable: true},
              {key: 'country', sortable: true}
          ];
          let fromDateMoment = moment(fromDate, "MMM YYYY").startOf('month').add(14, 'days').add(12, 'hours')
          let toDateMoment = moment(toDate, "MMM YYYY").startOf('month').add(14, 'days').add(12, 'hours')
          let monthDiffMoment = moment(toDateMoment).diff(moment(fromDateMoment), 'months')

          for (var i = 0; i <= (monthDiffMoment); i++) {
              this.fields.push({
                  key: fromDateMoment.format("MMM YYYY"),
                  sortable: true
              });
              fromDateMoment = fromDateMoment.add(1, 'months');
          }
      },
      convertData (data) {
        let arr = [];
        data.forEach(record => {
          if (record && record.data) {
            let details = record.data.split(';');
            details.forEach(detail => {
              let splits = detail.split('=');
              let date = moment(splits[0]);
              record[date.format("MMM YYYY")] = this.formatNumber(splits[1].replace(/,/g, ''));

            });
          }
          arr.push(record);
        });
        this.data = arr;
      },
      formatNumber(num) {
        return num.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
      },
    },
    watch: {
      tableData (newVal, oldVal) {
        this.convertData(newVal);
        this.setTableFields(this.from, this.to);
      }
    },
  }
</script>
