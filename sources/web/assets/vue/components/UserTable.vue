<template>
  <div class="custom-table">
    <b-table :dark="dark" :hover="hover" :striped="striped" :bordered="bordered" :small="small" :fixed="fixed" responsive="sm" :items="items" :fields="captions" :current-page="currentPage" :per-page="perPage" :sort-compare="sortCompare">
      <template slot="action" slot-scope="data">
        <!--
        <a href="javascript:;" v-on:click="updateUser(data.item)">Modifier</a>
        <span> - </span>
        -->
        <a href="javascript:;" v-on:click="deleteUser(data.item.id)">Delete</a>
      </template>
    </b-table>
    <nav>
      <b-pagination :total-rows="totalRows" :per-page="perPage" v-model="currentPage" prev-text="Prev" next-text="Next" hide-goto-end-buttons/>
    </nav>
  </div>
</template>

<script>


export default {
  name: 'user-table',
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
    deleteUser: function (userId) {
      this.$emit('delete', userId)
    },
    updateUser: function (user) {
      this.$emit('update', user)
    },
    sortCompare(a, b, key, sortDesc) {
      if (typeof a[key] === 'number' && typeof b[key] === 'number') {
        // If both compared fields are native numbers
        return a[key] < b[key] ? -1 : a[key] > b[key] ? 1 : 0
      } else {
        // Stringify the field data and use String.localeCompare
        if (this.toString(a[key]) === '') {
          return sortDesc ? -1 : 1
        } else {
          if (this.toString(b[key]) === '') {
            return sortDesc ? 1 : -1
          } else {
            return this.toString(a[key]).localeCompare(this.toString(b[key]), undefined, {
              numeric: true
            })
          }
        }
      }
    },
    toString(value) {
      if (!value) {
        return ''
      } else if (value instanceof Object) {
        return keys(value)
          .sort()
          .map(key => toString(value[key]))
          .join(' ')
      }
      return String(value)
    }
  }
}
</script>
