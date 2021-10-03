<template>
<router-view/>
<GlobalToast/>
<GlobalConfirmationDialog/>
</template>

<script setup lang="ts">
import GlobalToast from '@/components/widgets/GlobalToast.vue';
import GlobalConfirmationDialog from '@/components/widgets/GlobalConfirmationDialog.vue';
</script>

<style lang="scss">
@use 'resources/scss/main';
</style>
