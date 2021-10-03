<template>
<div class="small-wrapper poll-read">
   <div class="poll-read__body">
      <ProgressSpinner v-if="fetchingPoll" animationDuration=".8s" class="card-view__spinner"/>
      <div v-else-if="poll" class="card-view">
         <div class="card-view__item poll-card">
            <div class="poll-card__header">
               <div class="poll-card__title">{{ `${formatDate(poll.createdAt) } - ${getWeekDay(poll.createdAt)}` }}</div>
               <div class="poll-card__info">
                  <div class="poll-card__state">{{ poll.statusName }}</div>
                  <div v-if="poll && poll.finishedAt" class="poll-card__finished"> в {{ getFormattedDate(poll.finishedAt) }}</div>
               </div>
            </div>
            <div class="poll-card__body">
               <PollOptions :options="poll.options"/>
            </div>
         </div>
      </div>
      <div v-else class="poll-read__empty">Опрос в выбранную дату не проводился</div>
   </div>
   <div class="poll-read__navigation">
      <div class="poll-read__calendar">
         <Calendar
            v-model="date"
            :inline="true"
            :maxDate="maxDate"
            :selectOtherMonths="true"
            @dateSelect="selectDate"
         />
      </div>
      <div class="poll-read__refresh">
         <RefreshButton @click="selectDate"/>
      </div>
   </div>
</div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import ProgressSpinner from 'primevue/progressspinner';
import Calendar from 'primevue/calendar';
import store from '@/store';
import { formatDate, formatTime } from '@/modules/forms/formatting';
import { getWeekDay } from '@/modules/utils/calendar';
import PollOptions from '@/components/polls/PollOptions.vue';
import RefreshButton from '@/components/widgets/RefreshButton.vue';
import { PollAtWork } from '@/types/polls';

const date = ref(new Date());
const maxDate = new Date();

store.dispatch('polls/getPollAtWork', date.value);
const selectDate = () => store.dispatch('polls/getPollAtWork', date.value);

const poll = computed<PollAtWork>(() => store.state.polls.pollAtWork!);
const fetchingPoll = computed(() => store.state.polls.fetchingPool);

const getFormattedDate = (dateStr: Date | null) => {
   return dateStr ? formatTime(dateStr) : '';
};
</script>

<style lang="scss">
@use 'resources/scss/abstract' as *;

.poll-read {
   .p-datepicker {
      @extend %card;
      border: 0;
      padding: 0;
   }
}
</style>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.poll-read {
   display: flex;
   justify-content: space-between;
   align-items: flex-start;

   &__body {
      flex-grow: 1;
      margin: 0 10px 0 0;
   }

   &__empty {
      @extend %card;
      font-size: 1.25rem;
      text-align: center;
      padding: 15px 20px;
   }

   &__navigation {
      position: sticky;
      top: 10px;
   }

   &__calendar {
      margin: 0 0 10px 0;
   }

   &__refresh {
      display: flex;
      justify-content: center;
   }

   @media screen and (max-width: $sm) {
      flex-direction: column;
      align-items: unset;

      &__body {
         order: 1;
      }

      &__refresh {
         display: none;
      }

      &__navigation {
         position: unset;
         align-self: center;
      }
   }
}


.poll-card {
   @extend %card;

   $pc: &;

   &__header {
      border-bottom: 2px solid $blue;
   }

   &__title {
      padding: 15px 20px;

      @extend %h1;
      text-align: center;
   }

   &__info {
      border-top: 1px solid $border;
      padding: 15px 20px;
      display: flex;
      justify-content: center;
      font-style: italic;
      font-weight: 700;
   }

   &__finished {
      margin: 0 0 0 5px;
   }
}


.card-view {

   &__spinner {
      @extend %center-spinner;
   }

   &__item {
      &:not(&:last-child) {
         margin: 0 0 10px 0;
      }
   }
}


</style>
