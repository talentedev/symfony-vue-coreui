<template>
  <AppHeaderDropdownIntro left>
    <template slot="header">
      {{ $t("headers.activities") }}
    </template>
    <template slot="dropdown">
      <b-dropdown-header>{{ $t("headers.hse-regulatory-affairs") }}</b-dropdown-header>
      <b-dropdown-item href="/hse-regulatory-affairs/overview/">{{ $t("headers.overview") }}</b-dropdown-item>
      <b-dropdown-item href="/hse-regulatory-affairs/environment/">{{ $t("headers.environment") }}
      </b-dropdown-item>
      <b-dropdown-item href="/hse-regulatory-affairs/health/">{{ $t("headers.health") }}
      </b-dropdown-item>
      <b-dropdown-item href="/hse-regulatory-affairs/regulatory-affairs/">{{ $t("headers.regulatory-affairs") }}
      </b-dropdown-item>
      <b-dropdown-item href="/hse-regulatory-affairs/safety/">{{ $t("headers.safety") }}
      </b-dropdown-item>
      <b-dropdown-header>{{ $t("headers.market-research") }}</b-dropdown-header>
      <b-dropdown-item href="/hse-regulatory-affairs/safety/">{{ $t("headers.research-reports") }}
      </b-dropdown-item>
      <b-dropdown-item href="/market-research/trade-data-information/">{{ $t("headers.trade-data") }}
      </b-dropdown-item>
      <b-dropdown-item href="/market-research/monthly-reports/">{{ $t("headers.monthly-reports") }}
      </b-dropdown-item>
      <b-dropdown-item href="/market-research/annual-reports/">{{ $t("headers.annual-reports") }}
      </b-dropdown-item>
      <b-dropdown-item href="/market-research/extra-information/">{{ $t("headers.extra-info") }}
      </b-dropdown-item>
      <b-dropdown-header>{{ $t("headers.event-overview") }}</b-dropdown-header>
      <b-dropdown-item href="/events-overview/Web-2019-annual-conference/">{{ $t("headers.annual-conference") }}
      </b-dropdown-item>
      <b-dropdown-item href="/events-overview/upcoming-events/">{{ $t("headers.upcoming-events") }}
      </b-dropdown-item>
      <b-dropdown-item href="/library/">{{ $t("headers.library") }}
      </b-dropdown-item>
    </template>
  </AppHeaderDropdownIntro>
</template>

<script>
import { HeaderDropdown as AppHeaderDropdownIntro } from '@coreui/vue'
export default {
  name: 'DefaultHeaderDropdownActiv',
  components: {
    AppHeaderDropdownIntro
  },
}
</script>
