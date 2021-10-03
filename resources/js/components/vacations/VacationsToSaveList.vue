<template>
<div class="vacations__category vacations__category--green">
   <div class="vacations__title">Созданные отпуски</div>
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
         </div>
      </div>
   </div>
</div>
</template>

<script setup lang="ts">
import Dropdown from 'primevue/dropdown';
import VacationCard from '@/components/vacations/VacationCard.vue';
import { useVacationListActions } from '@/composables/vacations/vacation-list-actions';
import { VacationForm } from '@/types/vacations';

const emit = defineEmits(['edit', 'remove']);

const props = defineProps<{
   vacations: VacationForm[]
}>();

const { options, input } = useVacationListActions(emit);
</script>

<style scoped lang="scss">
@use 'resources/scss/vacations/vacation-list-edit';

.vacation {
   padding: 15px 0 15px 20px;
}

</style>
