<template>
<div class="vacations__category" :class="`vacations__category--${styleModifier}`">
   <div class="vacations__title">{{ title }}</div>
   <div class="vacations__list">
      <div v-for="vacation in vacations" :key="vacation.key" class="vacations__item vacation">
         <VacationCard :vacation="vacation"/>
         <div class="vacation__sidebar">
            <Dropdown
               :options="options"
               optionLabel="name"
               optionValue="action"
               @change="(event) => input(event, vacation)"
            />
            <div v-if="vacation.changed" class="vacation__changed" v-tooltip="'Изменено'">
               <FontAwesomeIcon icon="pen-alt"/>
            </div>
         </div>
      </div>
   </div>
</div>
</template>

<script setup lang="ts">
import VacationCard from '@/components/vacations/VacationCard.vue';
import Dropdown from 'primevue/dropdown';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { useVacationListActions } from '@/composables/vacations/vacation-list-actions';
import { VacationFormEdit } from '@/types/vacations';

const props = defineProps<{
   styleModifier: string
   title: string
   vacations: VacationFormEdit[]
}>();

const emit = defineEmits(['edit', 'remove']);

const { options, input } = useVacationListActions(emit);
</script>

<style scoped lang="scss">
@use 'resources/scss/vacations/vacation-list-edit';

.vacation {
   padding: 15px 0 15px 20px;
}
</style>
