<template>
  <AppHeaderDropdownIntro left>
    <template slot="header">
      {{ $t("headers.about-us") }}
    </template>
    <template slot="dropdown">
      <b-dropdown-item href="/about-us/">{{ $t("headers.about-us") }}
      </b-dropdown-item>
      <b-dropdown-item href="/about-us/staff/">{{ $t("headers.staff") }}
      </b-dropdown-item>
      <b-dropdown-header>{{ $t('headers.committees') }}</b-dropdown-header>
      <b-dropdown-item href="/about-us/statistics-committee/">{{ $t("headers.statistics") }}
      </b-dropdown-item>
      <b-dropdown-item href="/about-us/china-committee/">{{ $t("headers.cn-committee") }}
      </b-dropdown-item>
      <b-dropdown-item href="/about-us/hse-committee/">{{ $t("headers.hes-Committee") }}
      </b-dropdown-item>
      <b-dropdown-item href="/about-us/epd-division/">{{ $t("headers.epd-division") }}
      </b-dropdown-item>
      <b-dropdown-header>{{ $t('headers.officers') }}</b-dropdown-header>
      <b-dropdown-item href="/about-us/executive-board/">{{ $t("headers.executive-board") }}
      </b-dropdown-item>
      <b-dropdown-item href="/about-us/general-assembly/">{{ $t("headers.general-assembly") }}
      </b-dropdown-item>
      <b-dropdown-item href="/about-us/supervisory-board/">{{ $t("headers.supervisory-board") }}
      </b-dropdown-item>
      <b-dropdown-item href="/about-us/organizational-structure/">{{ $t("headers.org-structure") }}
      </b-dropdown-item>
      <b-dropdown-item href="/about-us/policies/">{{ $t("headers.policies") }}
      </b-dropdown-item>
    </template>
  </AppHeaderDropdownIntro>
</template>

<script>
import { HeaderDropdown as AppHeaderDropdownIntro } from '@coreui/vue'
export default {
  name: 'DefaultHeaderDropdownIntro',
  components: {
    AppHeaderDropdownIntro
  },
}
</script>
