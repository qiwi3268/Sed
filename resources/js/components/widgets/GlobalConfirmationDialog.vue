<template>
<ConfirmDialog/>
</template>

<script setup lang="ts">
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';
import store from '@/store';

store.dispatch('confirm/setConfirm', useConfirm());
</script>

<style scoped lang="scss">
</style>
