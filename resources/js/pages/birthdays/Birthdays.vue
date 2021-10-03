<template>
<div class="min-wrapper">
   <div class="sub-header">
      <div class="sub-header__title">
         <div class="sub-header__label">Просмотр дней рождения</div>
      </div>
   </div>

   <div class="birthdays">
      <table class="birthdays__table">
         <thead>
         <tr>
            <td class="birthdays__label">Сотрудник</td>
            <td class="birthdays__label">Дата</td>
         </tr>
         </thead>
         <tbody>
            <tr v-for="(user, index) in model" :key="index">
               <td class="birthdays__value">{{ user.fio }}</td>
               <td class="birthdays__date">{{ formatStringDateWithoutYear(user.dateOfBirth) }}</td>
            </tr>
         </tbody>
      </table>
   </div>
</div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import store from '@/store';
import { UserBirthday } from '@/modules/api/handlers/birthdays/BirthdaysGetter';
import { formatStringDateWithoutYear } from '@/modules/forms/formatting';

const model = computed<UserBirthday[]>(() => store.state.birthdays.birthdays);
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;
@use 'resources/scss/layout/sub-header';

.birthdays {
   @extend %card;

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

   &__label {
      text-align: center;
      padding: 8px 0;

      font-weight: 700;
      font-style: italic;

      border-bottom: 2px solid $blue;
      background-color: #F5F8FB;
   }

   &__value {
      text-align: center;
      padding: 7px;
   }

   &__date {
      text-align: center;
      padding: 7px;
      white-space: nowrap;
   }
}
</style>
