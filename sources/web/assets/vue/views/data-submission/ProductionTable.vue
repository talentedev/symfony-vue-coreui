<template>
  <div class="production-table">
    <div class="table-responsive">
      <table class="table table-striped table-bordered table-hover table-sm" :id="id">
        <thead>
          <th width="10%"></th>
          <th width="15%" v-for="(month, index) in months" :id="'header'+index">{{ month }}</th>
        </thead>
        <tbody>
          <tr>
            <td>Production</td>
            <td contenteditable="false"
              v-for="(item, key) in data" :key="key"
              :headers="'header'+key"
              :class="{'superior': item.production > item.capacity}">
              <vue-autonumeric v-if="item.production !== 'N/A'" v-model="item.production" :options="{digitGroupSeparator: ',', decimalCharacter: '.', decimalPlaces: '0', modifyValueOnWheel: false, emptyInputBehavior: 'null'}"></vue-autonumeric>
            </td>
          </tr>
          <tr>
            <td>Capacity</td>
            <td contenteditable="false" v-for="(item, key) in data" :key="key">
              <vue-autonumeric v-if="item.production !== 'N/A'" v-model="item.capacity" :options="{digitGroupSeparator: ',', decimalCharacter: '.', decimalPlaces: '0', modifyValueOnWheel: false, emptyInputBehavior: 'null'}"></vue-autonumeric>
            </td>
          </tr>
          <tr>
            <td>Inventory</td>
            <td contenteditable="false" v-for="(item, key) in data" :key="key">
              <vue-autonumeric v-if="item.production !== 'N/A'" v-model="item.inventory" :options="{digitGroupSeparator: ',', decimalCharacter: '.', decimalPlaces: '0', modifyValueOnWheel: false, emptyInputBehavior: 'null'}"></vue-autonumeric>
            </td>
          </tr>
        </tbody>
      </table>
      <button type="button" class="btn btn-success float-right" @click="saveCommodity">{{ $t("views.global.save") }}</button>
    </div>
  </div>
</template>

<script>
    import VueAutonumeric from 'vue-autonumeric';
    import moment from 'moment';
    moment.locale('en');

    export default {
  name: 'production-table',
    components: { VueAutonumeric },
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default: ''
    },
    months: {
      type: Array
    },
    tableData: {
      type: Array
    },
    product: {
      type: Number
    }
  },
  data: () => {
    return {
      data: [],
      storedCell: null,
      storedCellValue: null
    }
  },
  created (){
    this.data = this.convertTableData(this.tableData);
  },
  mounted () {
    document.querySelectorAll('#' + this.id + ' td').forEach(cell => {
      cell.addEventListener('focus', this.handleFocus);
      cell.addEventListener('blur', this.handleBlur);
      cell.addEventListener('keypress', this.handlePress);
      cell.addEventListener('keydown', this.handleKeydown);
    });
  },
  methods: {
    saveCommodity: function () {
      let productions = [];
        let dataTable = this.data;
        let months = this.months;
        for (let item in dataTable) {
            if (typeof this.months[item] !== 'undefined') {
                let production = {
                    production: dataTable[item].production,
                    capacity: dataTable[item].capacity,
                    inventory: dataTable[item].inventory,
                    date: this.getMiddleDateInMonth(months[item])
                }
                productions.push(production);
            }
        }
      const params = {
        productId: this.product,
        productions: productions
      }
      this.$store.dispatch('production/saveProductions', params)
        .then(res => this.$emit('updated', true));
    },
    getValueOrNull(value) {
      if (value === 'N/A' || value === '-') {
        return null;
      }
      return parseInt(value);
    },
    getMiddleDateInMonth(value) {
        return moment(value, "MMM YYYY").startOf('month').add(14, 'days').add(12, 'hours').format();
    },
    convertTableData(productions) {
      let tableData = [];
      for (var i = 0; i < 6; i++) {
        tableData.push({
          production: 'N/A',
          capacity: 'N/A',
          inventory: 'N/A'
        });
      }
      productions.forEach((production, index) => {
          let header = moment(production.date).format('MMM YYYY');
          let offset = this.months.findIndex(function (el) { return el == header; });
          tableData[offset] = production;
      });
      return tableData;
    },
    handleFocus (e) {
      const content = e.target.innerText;
      this.storedCellValue = content;
      this.storedCell = e.target;
      if (content === 'N/A' || content === '-') {
        e.target.innerText = '';
      }
    },
    handleBlur (e) {
      const content = e.target.innerText;
      if (content == '') {
        e.target.innerText = this.storedCellValue;
      }
      // Modify all capacity from modified month to current one if a capacity is modified.
      if (this.storedCell.parentElement.firstChild.innerText === 'Capacity') {
        const cells = Array.from(this.storedCell.parentElement.childNodes).filter(childNode => {
          return childNode.tagName && childNode.tagName === 'TD';
        });
        if (e.target.innerText !== this.storedCellValue) {
            for (var i = this.storedCell.cellIndex; i < cells.length; i++) {
                cells[i].innerText = this.storedCell.innerText;
            }
        }
      }
    },
    handlePress (e) {
      // Allow user to input only numbers.
      if (isNaN(String.fromCharCode(e.which)) || e.which === 13) {
        e.preventDefault();
      } else if (e.target.innerText === '0') {
        e.target.innerText = '';
      }
    },
    handleKeydown (e) {
      // when key is backspace or delete.
      if (e.which === 8 || e.which === 46) {
        if (e.target.innerText === '0') {
          e.preventDefault();
        }
        e.target.innerText.length === 1 ? e.target.innerText = '0' : null;
      }
    }
  },
  filters: {
    formatNumber(num) {
      return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    }
  },
  destroyed () {
    document.querySelectorAll('#' + this.id + ' td').forEach(cell => {
      cell.removeEventListener('focus', this.handleFocus);
      cell.removeEventListener('blur', this.handleBlur);
      cell.removeEventListener('keypress', this.handlePress);
      cell.removeEventListener('keydown', this.handleKeydown);
    });
  }
}
</script>
