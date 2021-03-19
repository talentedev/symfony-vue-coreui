<template>
  <b-modal ref="confirmation-modal" :id="id" title="Confirmation" @ok="handleOk">
    <p class="my-4">{{ content && content.text }}</p>
  </b-modal>
</template>

<script>
  export default {
    name: 'ConfirmationModal',
    props: {
      id: {
        type: String,
        default: 'confirmation'
      },
      content: {
        type: Object
      },
    },
    methods: {
      handleOk() {
        if (this.content.type === 'company') {
          this.$store.dispatch('company/closeCompany', this.content.id)
            .then(res => this.$emit('ok', true));
        } else if (this.content.type === 'commodity') {
          this.$store.dispatch('commodity/stopCommodity', this.content.id)
            .then(res => this.$emit('ok', true));
        }
      },
    }
  }
</script>