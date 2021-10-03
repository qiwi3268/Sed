<template>
<div class="medium-wrapper sign-session-read">
   <div class="session-header">
      <div class="session-header__cell session-header__cell--title">
         <div class="session-header__title">{{ model.title }}</div>
      </div>
      <div class="session-header__actions">
         <ActionButton
            v-if="canUserDeleteSession"
            @click="deleteSession"
            label="Удалить сессию"
            :loading="deleting"
            modifier="red"
         />
         <ActionButton @click="returnToView" label="Выйти"/>
      </div>
   </div>
   <div class="session-header">
      <div class="session-header__cell session-header__cell--wide">
         <div class="session-header__col">
            <div class="session-header__label">Создано</div>
            <FontAwesomeIcon icon="history" class="session-header__icon"/>
            <div class="session-header__text">{{ model.createdAt }}</div>
         </div>
         <div class="session-header__col">
            <div class="session-header__label">Автор</div>
            <FontAwesomeIcon icon="user-edit" class="session-header__icon"/>
            <div class="session-header__text">{{ model.author }}</div>
         </div>
      </div>
      <div class="session-header__cell state-card" :class="{ valid: isValid }">
         <div class="state-card__label">Статус</div>
         <div class="state-card__value">{{ model.statusName }}</div>
      </div>
   </div>
   <SignSessionFormRead :model="model"/>
</div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import SignSessionFormRead from '@/components/sign-sessions/SignSessionFormRead.vue';
import store from '@/store';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { Confirm } from '@/modules/modals/Confirm';
import ActionButton from '@/components/widgets/ActionButton.vue';
import { SignSessionRead, SignSessionStatuses } from '@/types/sign-sessions';

const model = computed<SignSessionRead>(() => store.state.signSessions.signSessionRead!);

const canUserDeleteSession = computed(() => store.state.signSessions.canDeleteSignSession);

const isValid = computed(() => model.value.statusId === SignSessionStatuses.Finished);

const deleting = ref(false);
const deleteSession = () => {
   if (!deleting.value) {
      Confirm.deleteSignSession(async() => {
         deleting.value = true;
         await store.dispatch('signSessions/deleteSignSession', model.value);
         deleting.value = false;
      });
   }
};

const returnToView = () => store.dispatch('signSessions/returnToSelectedView');
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;
@use 'resources/scss/elements/action-button';

.sign-session-read {
   .session-header {
      display: flex;
      @extend %mb;

      &__actions {
         display: flex;

        .action-button {
            &:not(&:last-child) {
               @extend %mr;
            }
         }
      }

      &__cell {
         @extend %card;
         align-items: center;
         display: flex;

         &:not(&:last-child) {
            @extend %mr;
         }

         &--wide {
            display: flex;
            align-items: center;
            flex-grow: 1;
            justify-content: space-around;
            font-size: 1.125rem;

            @media screen and (max-width: $md) {
               font-size: unset;
            }
         }

         &--title {
            flex-grow: 1;
         }
      }

      &__label {
         font-weight: 300;
         margin: 0 7px 0 0;

         @media screen and (max-width: $sm) {
            display: none;
         }
      }

      &__icon {
         margin: 0 4px 0 0;
         color: $icon-blue;
      }

      &__title {
         padding: 15px 20px;
         @extend %h1;

         @media screen and (max-width: $md) {
            padding: 10px 15px;
         }

         @media screen and (max-width: $sm) {
            padding: 5px 10px;
         }
      }

      &__col {
         display: flex;
         align-items: center;
         padding: 15px 20px;

         @media screen and (max-width: $md) {
            padding: 10px 15px;
         }

         @media screen and (max-width: $sm) {
            padding: 5px 10px;

         }
      }

      .state-card {
         font-size: 1.25rem;
         display: flex;
         align-items: center;
         padding: 0 10px;
         background-color: #f5fafd;

         @media screen and (max-width: $md) {
            font-size: unset;
         }

         &.valid {
            background-color: #F3FBF6;

            .state-card__value {
               color: $dark-green;
            }
         }

         &__label {
            font-weight: 300;
            margin: 0 7px 0 0;

            @media screen and (max-width: $sm) {
               display: none;
            }
         }

         &__value {
            font-weight: 700;
            color: #306788;
         }
      }
   }
}

</style>
