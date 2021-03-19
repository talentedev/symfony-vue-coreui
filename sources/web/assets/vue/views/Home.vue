<template>
    <DefaultContainer :isHome="true"></DefaultContainer>
</template>

<script>
    import DefaultContainer from "../containers/DefaultContainer";

    export default {
        name: 'Home',
        components: {DefaultContainer},
    }
</script>