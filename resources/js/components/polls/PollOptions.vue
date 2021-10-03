<template>
<Accordion :multiple="true" :activeIndex="[...options.keys()]">
   <AccordionTab v-for="option in options" :key="option.id">
      <template #header>
         <div class="poll-option__row">
            <div class="poll-option__category">
               <FontAwesomeIcon class="poll-option__icon" :class="`poll-option__icon--${option.icon.modifier}`" :icon="option.icon.name"/>
               <div class="poll-option__label">{{ option.name }}</div>
            </div>
            <div class="poll-option__counter">{{ option.users.length }}</div>
         </div>
      </template>
      <div v-if="option.users.length > 0" class="poll-option__users">
         <div
            v-for="(userList, index) in getUserChunks(option.users)"
            :key="index"
            class="poll-option__list"
         >
            <div
               v-for="(user, index) in userList"
               :key="index"
               class="poll-option__fio"
            >{{ user }}</div>
         </div>
      </div>
   </AccordionTab>
</Accordion>
</template>


<script setup lang="ts">
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import Accordion from 'primevue/accordion';
import AccordionTab from 'primevue/accordiontab';
import chunk from 'lodash/chunk';
import { PollAtWorkOption } from '@/types/polls';

const props = defineProps<{
   options: PollAtWorkOption[]
}>();

const getUserChunks = (users: string[]): string[][] => {
   return users.length > 10 ? chunk(users, Math.ceil(users.length / 2)) : [users];
};

</script>

<style lang="scss">
@use 'resources/scss/abstract' as *;

.p-accordion-header {
   border: unset !important;
   background-color: unset !important;
}
.p-accordion-tab {
   &:not(&:last-child) {
      .p-accordion-header {
         border-bottom: 1px solid $border !important;
      }
      .poll-option__users {
         border-bottom: 1px solid $border;
      }
   }
   &:last-child {
      .poll-option__users {
         border-top: 1px solid $border;
      }
   }
}
.p-accordion-content {
   border: unset !important;
}
.p-accordion-header-link {
   border: unset !important;
   background-color: unset !important;
   color: unset !important;
   font-weight: unset !important;
   justify-content: space-between;
   transition: background-color .1s !important;
   padding: 10px 10px 10px 20px !important;
   &:hover {
      background-color: rgba(39, 72, 80, 0.05) !important;
   }

   @media screen and (max-width: $md) {
      padding: 5px 5px 5px 15px !important;
   }
}
.p-accordion-toggle-icon {
   order: 1;
}
</style>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.poll-option {
   @extend %card;
   &__item {
      padding: 10px 20px;
      &:not(&:last-child) {
         border-bottom: 1px solid $border;
      }
   }
   &__row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-grow: 1;
      margin: 0 10px 0 0;
   }
   &__counter {
      font-size: 1.5rem;
   }
   &__category {
      display: flex;
      align-items: center;
   }
   &__icon {
      margin: 0 10px 0 0;
      font-size: 1.25rem;
      &--blue {
         color: $icon-blue;
      }
      &--red {
         color: #E28181FF;
      }
      &--green {
         color: #87ABAAFF;
      }
   }
   &__label {
      font-weight: 700;
      font-style: italic;
   }
   &__fio {
      @extend %mb;

      white-space: nowrap;
   }
   &__users {
      display: grid;
      grid-template-columns: 1fr 1fr;
      grid-column-gap: 3px;
   }

   &__list {
      padding: 10px 20px;
      &:not(&:last-child) {
         border-right: 1px solid $border;
      }

      @media screen and (max-width: $md) {
         padding: 7px 7px 7px 15px;
      }
   }
}
</style>
