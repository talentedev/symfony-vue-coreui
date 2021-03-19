<template>
  <div class="custom-table">
    <b-table :dark="dark" :hover="hover" :striped="striped" :bordered="bordered" :small="small" :fixed="fixed" responsive="sm" :items="items" :fields="captions" :current-page="currentPage" :per-page="perPage">
      <template slot="file" slot-scope="data">
        <a :href="data.item.file" download="">Download</a>
        <span v-if="isAdmin"> - </span>
        <a v-if="isAdmin" href="javascript:;" v-on:click="deleteReport(data.item.id)">Delete</a>
      </template>
    </b-table>
    <nav>
      <b-pagination :total-rows="totalRows" :per-page="perPage" v-model="currentPage" prev-text="Prev" next-text="Next" hide-goto-end-buttons/>
    </nav>
  </div>
</template>

<script>


export default {
  name: 'report-table',
  inheritAttrs: false,
  props: {
    caption: {
      type: String,
      default: 'Table'
    },
    hover: {
      type: Boolean,
      default: false
    },
    striped: {
      type: Boolean,
      default: false
    },
    bordered: {
      type: Boolean,
      default: false
    },
    small: {
      type: Boolean,
      default: false
    },
    fixed: {
      type: Boolean,
      default: false
    },
    tableData: {
      type: [Array, Function],
      default: () => []
    },
    fields: {
      type: [Array, Object],
      default: () => []
    },
    perPage: {
      type: Number,
      default: 5
    },
    dark: {
      type: Boolean,
      default: false
    }
  },
  data: () => {
    return {
      currentPage: 1,
    }
  },
  computed: {
      isAdmin () {
          const isAdmin = this.$store.getters['security/hasRole']('ADMIN');
          return isAdmin;
      },
    items: function() {
      const items =  this.tableData
      return Array.isArray(items) ? items : items()
    },
    totalRows: function () { return this.getRowCount() },
    captions: function() { return this.fields }
  },
  methods: {
    getRowCount: function () {
      return this.items.length
    },
    deleteReport: function (reportId) {
      this.$emit('clicked', reportId)
    }
  }
}
</script>
