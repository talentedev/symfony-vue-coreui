<template>
  <AppHeaderDropdownIntro right no-caret>
    <template slot="header">
      <i class="nav-icon fa fa-globe"></i>
    </template>
    <template slot="dropdown">
      <b-dropdown-header
        tag="div"
        class="text-center">
        <strong>{{ $t("headers.language") }}</strong>
      </b-dropdown-header>
      <b-dropdown-item @click="changeLocale('en')">{{ $t("langs.english") }}</b-dropdown-item>
      <b-dropdown-item @click="changeLocale('cn')">{{ $t("langs.chinese") }}</b-dropdown-item>
    </template>
  </AppHeaderDropdownIntro>
</template>

<script>
import { HeaderDropdown as AppHeaderDropdownIntro } from '@coreui/vue'
export default {
  name: 'DefaultHeaderDropdownTrans',
  components: {
    AppHeaderDropdownIntro
  },
  methods: {
    changeLocale(lang) {
      this.$i18n.locale = lang;
    }
  }
}
</script>
