<template>
<div class="conference-for-date">
   <div class="conference-for-date__body">
      <ProgressSpinner v-if="!model" animationDuration=".8s" class="conference-for-date__spinner"/>
      <div v-else-if="model.length > 0" class="conference-for-date__cards">
         <ConferenceCard
            v-for="(conference, index) in model"
            :key="index"
            :model="conference"
         />
      </div>
      <div v-else class="conference-for-date__empty">{{ getEmptyMessage() }}</div>
   </div>
   <div class="conference-for-date__navigation">
      <div class="conference-for-date__calendar">
         <Calendar
            v-model="date"
            :inline="true"
            :selectOtherMonths="true"
            @dateSelect="selectDate"
            @monthChange="changeDate"
         >
            <template #date="slotProps">
               <div
                  class="calendar__day"
                  :class="{ 'calendar__day--green': hasConference(slotProps.date) }"
               >{{ slotProps.date.day }}</div>
            </template>
         </Calendar>
      </div>
      <div class="conference-for-date__refresh">
         <RefreshButton @click="refresh"/>
      </div>
   </div>
</div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import Calendar from 'primevue/calendar';
import store from '@/store';
import { formatToJsonDate } from '@/modules/forms/formatting';
import ConferenceCard from '@/components/conferences/ConferenceCard.vue';
import { isPast } from 'date-fns';
import ProgressSpinner from 'primevue/progressspinner';
import RefreshButton from '@/components/widgets/RefreshButton.vue';
import { ConferenceRead } from '@/types/conferences';

const date = ref(store.state.conferences.selectedDate);
const model = computed(() => store.state.conferences.conferencesForDate as ConferenceRead[]);

const selectDate = (date) => {
   changeDate({ year: date.getFullYear() });
   store.dispatch('conferences/fetchConferencesForDate', date);
};
const refresh = () => {
   store.dispatch('conferences/updateConferencesForDateView');
};

const conferenceCalendar = computed(() => store.getters['conferences/getConferenceCalendar']);
const hasConference = (date) => {
   const dateStr = formatToJsonDate(new Date(date.year, date.month, date.day));
   return conferenceCalendar.value.includes(dateStr);
};

const changeDate = ({ year }) => {
   if (year !== store.state.conferences.selectedYear) {
      store.dispatch('conferences/changeSelectedYear', year);
   }
};

const getEmptyMessage = () => {
   return isPast(date.value)
      ? 'Совещания в выбранную дату не проводились'
      : 'Совещания на выбранную дату не запланированы';
};
</script>

<style lang="scss">
@use 'resources/scss/abstract' as *;

.conference-for-date {
   .p-datepicker {
      @extend %card;
      border: 0;
      padding: 0;

      .p-highlight {
         color: unset !important;
         background: unset !important;

         .calendar__day {
            text-align: center;
            background-color: #E3F2FD;
            color: #495057;

            &:hover {
               background-color: #cedfea;
            }
         }
      }

      table td > span {
         border: unset !important;
      }
   }

   @media screen and (max-width: $sm) {
      .p-calendar,
      .p-datepicker,
      .p-datepicker-group-container {
         width: 100%;
      }
   }
}
</style>

<style scoped lang="scss">
@use 'resources/scss/conferences/conference-for-date';
</style>
