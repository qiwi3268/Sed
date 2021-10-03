<template>
<ViewLabel
   @click="changeView"
   :counterFetched="isCounterFetched"
   :counter="counter"
   :hasCounter="true"
   :selected="isSelected"
>{{ label }}</ViewLabel>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import store from '@/store';
import router from '@/router';
import ViewLabel from '@/components/widgets/ViewLabel.vue';
import { RouteLocationRaw } from 'vue-router';
import { FetchViewPayload, ViewName } from '@/store/modules/navigation';

const props = defineProps<{
   label: string
   viewName: ViewName
   hasPagination?: boolean
}>();

const counter = computed(() => store.getters['navigation/getViewCounter'](props.viewName));
const isCounterFetched = computed(() => counter.value !== null);

const isSelected = computed(() => store.state.navigation.selectedView === props.viewName);
if (!isSelected.value) {
   store.dispatch('navigation/fetchViewCounter', props.viewName);
}

const changeView = () => {
   const to: RouteLocationRaw = { name: props.viewName };

   if (props.hasPagination) {
      to.query = { page: 1 };
   }

   if (router.currentRoute.value.name !== to.name) {
      router.push(to);
   } else {
      updateCurrentView();
   }
};

const updateCurrentView = () => {
   const payload: FetchViewPayload = { viewName: props.viewName };

   if (props.hasPagination && router.currentRoute.value.query.page) {
      payload.page = router.currentRoute.value.query.page as string;
   }

   store.dispatch('navigation/fetchView', payload);
};
</script>

<style scoped lang="scss">
</style>
