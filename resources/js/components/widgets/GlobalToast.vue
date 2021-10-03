<template>
<Toast position="top-right" />
</template>

<script setup lang="ts">
import store from '@/store';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';

store.dispatch('toast/setToast', useToast());
</script>

<style scoped lang="scss">

</style>
