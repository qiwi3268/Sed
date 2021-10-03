<template>
<div class="small-wrapper conference-view">
   <ProgressSpinner v-if="!model" animationDuration=".8s" class="conference-view__spinner"/>
   <div v-else-if="model.length > 0" class="conference-view__cards">
      <ConferenceCard
         v-for="(conference, index) in model"
         :key="index"
         :model="conference"
      />
   </div>
   <div v-else class="conference-view__empty">Запланированные совещания отсутствуют</div>
</div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import ConferenceCard from '@/components/conferences/ConferenceCard.vue';
import store from '@/store';
import ProgressSpinner from 'primevue/progressspinner';
import { ConferenceRead, ConferenceViews } from '@/types/conferences';

const model = computed<ConferenceRead[]>(() => store.getters['navigation/getView'](ConferenceViews.MyPlanned));
</script>

<style scoped lang="scss">
@use 'resources/scss/conferences/conference-view';
</style>
