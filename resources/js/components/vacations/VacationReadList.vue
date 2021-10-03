<template>
<div v-if="vacations.length > 0" class="vacations__category" :class="`vacations__category--${styleModifier}`">
   <div class="vacations__title">{{ title }}</div>
   <div class="vacations__wrapper">
      <table class="vacations__table">
         <thead>
            <tr>
               <td class="vacation__label">Сотрудник</td>
               <td class="vacation__label">Первый день</td>
               <td class="vacation__label">Последний день</td>
               <td class="vacation__label">День выхода</td>
               <td class="vacation__label">Количество дней</td>
               <td class="vacation__label">Замена</td>
            </tr>
         </thead>
         <tbody>
            <tr v-for="(vacation, index) in vacations" :key="index" class="vacation__row">
               <td class="vacation__value vacation__value--start">{{ vacation.employee.label }}</td>
               <td class="vacation__value">{{ formatDate(vacation.startAt) }}</td>
               <td class="vacation__value">{{ formatDate(vacation.finishedAt) }}</td>
               <td class="vacation__value">{{ formatDate(vacation.goingToWorkAt) }}</td>
               <td class="vacation__value">{{ vacation.duration }}</td>
               <td class="vacation__names">
                  <template v-if="vacation.replacementEmployees.length > 0">
                     <div
                        v-for="employee in vacation.replacementEmployees"
                        :key="employee.label"
                        class="vacation__fio"
                     >{{ employee.label }}</div>
                  </template>
                  <div v-else class="vacation__fio">-</div>
               </td>
            </tr>
         </tbody>
      </table>
   </div>
</div>
<div v-else class="vacation__empty">Отпуска за данный период отсутствуют</div>
</template>

<script setup lang="ts">
import { formatDate } from '@/modules/forms/formatting';
import { VacationRead } from '@/types/vacations';

const props = defineProps<{
   title: string
   styleModifier: string
   vacations: VacationRead[]
}>();
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.vacation {

   &__label {
      text-align: center;
      padding: 8px 0;

      font-weight: 700;
      font-style: italic;

      @media screen and (max-width: $md) {
         font-size: 0.875rem;
      }

      @media screen and (max-width: $sm) {
         font-size: 0.75rem;
      }
   }

   &__row {
      cursor: default;
      transition: background-color .1s;
   }

   &__empty {
      @extend %card;
      padding: 10px 20px;
      font-size: 1.375rem;
      text-align: center;
   }

   &__names {
      display: flex;
      flex-direction: column;
      justify-content: center;
   }

   &__value,
   &__fio {
      text-align: center;
      white-space: nowrap;

      @media screen and (max-width: $md) {
         font-size: 0.875rem;
      }

      @media screen and (max-width: $sm) {
         font-size: 0.75rem;
      }
   }

   &__value,
   &__names {
      padding: 7px 3px;
   }

   &__fio {
      align-items: center;
   }

}

.vacations {
   &__category {
      &--blue {
         .vacations__title {
            border-left: 3px solid $blue;
         }

         .vacation {
            &__label {
               border-bottom: 2px solid $blue;
               background-color: #F5F8FB;
            }

            &__row {
               &:hover {
                  background-color: #E8EEF8;
               }
            }
         }
      }

      &--dark-green {
         .vacations__title {
            border-left: 3px solid #589493;
         }

         .vacation {
            &__label {
               border-bottom: 2px solid #589493;
               background-color: #f1f8f8;
            }

            &__row {
               &:hover {
                  background-color: #ddecec;
               }
            }
         }
      }
   }

   &__wrapper {
      @extend %card;
   }

   &__table {
      border-collapse: collapse;
      width: 100%;
   }

   &__title {
      @extend %card;
      padding: 10px 20px;
      font-size: 1.25rem;
      font-weight: 700;
      font-style: italic;
      margin: 0 0 10px 0;
   }
}



</style>
