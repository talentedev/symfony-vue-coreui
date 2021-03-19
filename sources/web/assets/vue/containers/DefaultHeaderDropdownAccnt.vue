<template>
  <AppHeaderDropdown right no-caret>
    <template slot="header">
      <i class="nav-icon icon-user"></i>
    </template>\
    <template slot="dropdown">
      <b-dropdown-header
        tag="div"
        class="text-center">
        <strong>{{ $t("headers.settings") }}</strong>
      </b-dropdown-header>
      <b-dropdown-item @click="logout"><i class="fa fa-lock" /> {{ $t("headers.logout") }}</b-dropdown-item>
    </template>
  </AppHeaderDropdown>
</template>

<script>
import { HeaderDropdown as AppHeaderDropdown } from '@coreui/vue'
export default {
  name: 'DefaultHeaderDropdownAccnt',
  components: {
    AppHeaderDropdown
  },
  methods: {
    logout() {
        if (!_.isNil(localStorage.getItem('REMEMBER_ME_TOKEN'))) {
            localStorage.removeItem('REMEMBER_ME_TOKEN')
        }
        this.$store.dispatch('security/logout').then(() => {
            this.$router.push({path: '/login'})
        })
    }
  }
}
</script>
