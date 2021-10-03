<template>
<div class="small-wrapper">
   <div class="sub-header">
      <div class="sub-header__title">
         <div class="sub-header__label">Просмотр отпусков</div>
         <router-link v-if="canManageVacations" :to="{ name: Routes.VacationsManagement }">
            <Button label="Менеджмент" class="p-button-outlined"/>
         </router-link>
      </div>
   </div>

   <div v-if="usersOnVacation.length > 0" class="vacation-users">
      <div class="vacation-users__list">
         <div class="vacation-users__title">Сейчас в отпуске</div>
         <div class="vacation-users__items">
            <div v-for="fio in usersOnVacation" :key="fio" class="vacation-users__item">
               <i class="pi pi-circle-on vacation-users__point"></i>
               <div class="vacation-users__fio">{{ fio }}</div>
            </div>
         </div>
      </div>
      <div
         v-if="userOnVacationPercent"
         class="vacation-users__percent"
         v-tooltip="'Процент от общего числа'"
      >{{ userOnVacationPercent }}%</div>
   </div>

   <VacationsReadNav @changeView="changeView" @selectDate="selectDate"/>

   <div class="vacations">
      <ProgressSpinner v-if="fetchingVacations" animationDuration=".8s" class="vacations__spinner"/>
      <VacationReadList
         v-else-if="currentView === VacationsReadViews.ForNext30Days"
         :vacations="vacations"
         title="Ближайшие 30 дней"
         styleModifier="blue"
      />
      <VacationReadList
         v-else
         :vacations="vacations"
         :title="selectedDateTitle"
         styleModifier="dark-green"
      />
   </div>
</div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import Button from 'primevue/button';
import { Routes } from '@/router';
import store from '@/store';
import VacationReadList from '@/components/vacations/VacationReadList.vue';
import ProgressSpinner from 'primevue/progressspinner';
import { getFullMonthNameByNumber } from '@/modules/utils/calendar';
import VacationsReadNav from '@/components/vacations/VacationsReadNav.vue';
import { VacationsReadViews } from '@/types/vacations';

const canManageVacations = computed(() => store.state.vacations.canManageVacations);
const userOnVacationPercent = computed(() => store.state.vacations.userOnVacationPercent);

const fetchingVacations = computed(() => store.state.vacations.fetchingVacationsRead);
const vacations = computed(() => store.state.vacations.vacationsRead);

const selectedDateTitle = ref('');
const selectDate = (selectedDate) => {
   selectedDateTitle.value = `${getFullMonthNameByNumber(selectedDate.month)} - ${selectedDate.year}`;
   const date = { year: selectedDate.year, month: selectedDate.month + 1 };
   store.dispatch('vacations/getVacationsForDate', date);
};

const currentView = ref<VacationsReadViews>();
const changeView = (view) => {
   currentView.value = view;
};

const usersOnVacation = computed(() => store.state.vacations.usersOnVacation);
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;
@use 'resources/scss/layout/sub-header';

.vacations {
   &__spinner {
      @extend %center-spinner;
   }
}

.sub-header {
   &__title {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 20px;
   }
}

.vacation-users {
   @extend %card;
   @extend %mb;
   padding: 10px 20px;
   display: flex;
   align-items: center;
   justify-content: space-between;

   &__title {
      margin: 0 0 10px 0;
      font-size: 1.25rem;
      font-weight: 700;
      font-style: italic;
   }

   &__item {
      display: flex;
      align-items: center;
   }

   &__point {
      @extend %list-point;
   }

   &__percent {
      padding: 20px;
      font-size: 1.75rem;
      background-color: $green-blue;
      border-radius: 5px;
      color: #fff;
      cursor: pointer;

      @media screen and (max-width: $sm) {
         padding: 15px;
         font-size: 1.5rem;
      }
   }
}

</style>
