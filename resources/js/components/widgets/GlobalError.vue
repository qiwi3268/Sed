<template>
<div @click="close" class="overlay error-overlay"></div>
<div class="modal error-modal">
   <FontAwesomeIcon @click="close" size="4x" class="modal-close" icon="times"/>
   <div class="error-modal__header">
      <FontAwesomeIcon @click="close" size="lg" class="error-modal__icon" icon="exclamation"/>
      <span class="error-modal__title">{{ title }}</span>
   </div>
   <div v-for="message in messages" :key="message" class="error-modal__messages">
      <div class="error-modal__message">{{ message }}</div>
   </div>
   <div v-if="code" class="error-modal__code">{{ code }}</div>
</div>
</template>

<script setup lang="ts">
import store from '@/store';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const close = (): void => {
   store.dispatch('errorModal/close');
};

const { title, messages, code } = store.state.errorModal;
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;
@use 'resources/scss/modals/modal';

.error-modal {
   max-width: 500px;
   z-index: 100;

   transition: 0s;

   &__header {
      border-bottom: 1px solid $grey;
      padding: 10px 15px;
   }

   &__icon {
      color: $error;
      margin: 0 5px 0 0;
   }

   &__title {
      font-size: 1.125rem;
      font-weight: 700;
   }

   &__messages {
      padding: 10px 15px;
   }

   &__message {

      &:not(&:last-child) {
         margin: 0 0 5px 0;
      }
   }

   &__code {
      padding: 0 15px 10px;
   }
}

.error-overlay {
   z-index: 90;
}

</style>
