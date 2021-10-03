<template>
<div class="date-navigation">
   <div class="date-navigation__row date-navigation__row--split">
      <div class="date-navigation__block">
         <div class="date-navigation__list">
            <div
               v-for="year in yearList"
               :key="year"
               @click="selectYear(year)"
               class="date-navigation__item"
               :class="{
                  'date-navigation__item--selected': selectedYear === year && currentView === VacationsReadViews.ForYearAndMonth,
                  'date-navigation__item--current': year === getCurrentYear(),
               }"
            >{{ year }}</div>
         </div>
      </div>
      <div
         @click="selectForNext30Days"
         class="date-navigation__item date-navigation__item--blue"
         :class="{ 'date-navigation__item--selected': currentView === VacationsReadViews.ForNext30Days }"
      >Ближайшие 30 дней</div>
   </div>
   <div class="date-navigation__row">
      <div class="date-navigation__list date-navigation__list--months">
         <div
            v-for="month in monthList"
            :key="month.number"
            @click="selectMonth(month)"
            class="date-navigation__item"
            :class="{
               'date-navigation__item--selected': selectedMonthNumber === month.number && currentView === VacationsReadViews.ForYearAndMonth,
               'date-navigation__item--current': month.number === getCurrentMonth(),
            }"
         >{{ month.shortName }}</div>
      </div>
   </div>
</div>
</template>

<script setup lang="ts">
import { computed, ref, watchEffect } from 'vue';
import { getCurrentMonth, getCurrentYear, getMonthList, Month } from '@/modules/utils/calendar';
import store from '@/store';
import { VacationsReadViews } from '@/types/vacations';

const emit = defineEmits(['selectDate', 'changeView']);

const yearList = ref([2020, 2021]);
const selectedYear = ref(getCurrentYear());
const selectYear = (year: number) => {
   selectedYear.value = year;
   currentView.value = VacationsReadViews.ForYearAndMonth;
   selectDate();
};

const monthList = ref<Month[]>(getMonthList());
const selectedMonthNumber = ref(getCurrentMonth());
const selectMonth = (month) => {
   selectedMonthNumber.value = month.number;
   currentView.value = VacationsReadViews.ForYearAndMonth;
   selectDate();
};

const selectForNext30Days = () => {
   currentView.value = VacationsReadViews.ForNext30Days;
   selectedMonthNumber.value = getCurrentMonth();
   selectedYear.value = getCurrentYear();
   store.dispatch('vacations/getForNext30DaysVacations');
};

const selectedDate = computed(() => ({
   year: selectedYear.value,
   month: selectedMonthNumber.value
}));
const selectDate = () => emit('selectDate', selectedDate.value);

const currentView = ref<VacationsReadViews>(VacationsReadViews.ForNext30Days);

watchEffect(() => {
   emit('changeView', currentView.value);
});
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.date-navigation {
   margin: 0 0 10px 0;
   font-size: 1.125rem;
   @extend %card;

   &__row {
      padding: 0 10px;
      display: flex;

      &:not(&:first-child) {
         border-top: 1px solid $border;
      }

      &--split {
         justify-content: space-between;
      }
   }

   &__block {
      display: flex;
      align-items: center;
   }

   &__label {
      margin: 0 10px 0 0;
      font-weight: 700;
   }

   &__list {
      display: flex;
   }

   &__item {
      padding: 12px 12px 10px 12px;
      cursor: pointer;
      display: flex;
      align-items: center;

      border-bottom: 2px solid $border;
      border-bottom: 2px solid #fff;

      transition: border-color .1s, background-color .1s;

      &:hover {
         border-color: #3e6d6c;
         background-color: #ddecec;
      }

      &--selected {
         border-color: #589493;
         background-color: #f1f8f8;

         &.date-navigation__item--blue {
            border-color: $blue;
            background-color: #F5F8FB;
         }
      }

      &--current {
         font-weight: 700;
      }

      &--blue {
         &:hover {
            border-color: $icon-blue;
            background-color: #E8EEF8;
         }
      }
   }

   @media screen and (max-width: $md) {
      &__item {
         padding: 8px 8px 6px 8px;
         justify-content: center;
      }

      &__list {
         &--months {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            flex-grow: 1;
         }
      }
   }

   @media screen and (max-width: $sm) {
      &__item {
         padding: 5px 5px 4px 5px;
      }
   }

}

</style>
