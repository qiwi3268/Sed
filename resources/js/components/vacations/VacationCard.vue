<template>
<div class="vacation__body">
   <div class="vacation__row">
      <div class="vacation__label">Сотрудник:</div>
      <div class="vacation__value">{{ vacation.employee.label }}</div>
   </div>
   <div class="vacation__row">
      <div class="vacation__label">Первый день:</div>
      <div class="vacation__value">{{ formatDate(vacation.startAt) }}</div>
   </div>
   <div class="vacation__row">
      <div class="vacation__label">Длительность:</div>
      <div class="vacation__value">{{ vacation.duration }}</div>
   </div>
   <div class="vacation__row">
      <div class="vacation__label">Замена:</div>
      <div v-if="vacation.replacementEmployees.length > 0" class="vacation__names">
         <div
            v-for="employee in vacation.replacementEmployees"
            :key="employee.label"
            class="vacation__value"
         >{{ employee.label }}</div>
      </div>
      <div v-else class="vacation__value">-</div>

   </div>
</div>
</template>

<script setup lang="ts">
import { formatDate } from '@/modules/forms/formatting';
import { VacationForm } from '@/types/vacations';

const props = defineProps<{
   vacation: VacationForm
}>();
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.vacation {

   &__body {
      flex-grow: 1;
   }

   &__row {
      display: flex;

      &:not(&:last-child) {
         border-bottom: 1px solid $border;
         padding: 0 0 10px 0;
         margin: 0 0 10px 0;
      }
   }

   &__label {
      font-weight: 300;
      margin: 0 10px 0 0;
      word-break: normal;
   }

}
</style>
