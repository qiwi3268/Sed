<template>
<router-link @click="updateView" :to="{ name: ConferenceViews.ForDate }" class="conference-for-date-label">
   <ViewLabel :selected="isSelected">Все совещания</ViewLabel>
</router-link>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import store from '@/store';
import ViewLabel from '@/components/widgets/ViewLabel.vue';
import { ConferenceViews } from '@/types/conferences';

const isSelected = computed(() => store.state.navigation.selectedView === ConferenceViews.ForDate);

const updateView = () => {
   if (isSelected.value) {
      store.dispatch('conferences/updateConferencesForDateView');
   }
};
</script>

<style scoped lang="scss">
.conference-for-date-label {
   :deep(.view-label__block) {
      justify-content: center;
   }
}
</style>
