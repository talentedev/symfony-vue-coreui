<template>
  <div class="card">
    <div class="card-header">
      <h3>{{ $t("navs.export-data") }}</h3>
    </div>
    <div class="card-body">
      <a href="/api/export-production" class="btn btn-primary">{{ $t("navs.export-data") }}</a>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'Export'
  }
</script>
