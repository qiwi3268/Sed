<template>
<div v-if="model" class="card-view">
   <ViewNav :pagination="model.pagination" @changePage="changePage"/>
   <div class="card-view__list">
      <div v-for="(part, index) in chunkedSessionList" :key="index" class="card-view__col">
         <router-link v-for="session in part"
              :key="session.uuid"
              :to="getSignSessionSigningRoute(session.uuid)"
              class="card-view__item session-card"
         >
            <CardOverlay label="Подписать"/>
            <div class="session-card__header">{{ session.title }}</div>
            <div class="session-card__body">
               <div class="session-card__author">
                  <div class="session-card__label">Автор</div>
                  <FontAwesomeIcon icon="user-edit" class="session-card__icon"/>
                  <div class="session-card__value">{{ session.author }}</div>
               </div>
               <div class="session-card__info">
                  <div class="session-card__date">
                     <div class="session-card__label">Создано</div>
                     <div class="session-card__value">{{ session.createdAt }}</div>
                  </div>
                  <div class="session-card__status" :class="{ valid: session.status.id === SignSessionStatuses.Finished }">{{ session.status.name }}</div>
               </div>
            </div>
         </router-link>
      </div>
   </div>
</div>
<ProgressSpinner v-else animationDuration=".8s" class="card-view__spinner"/>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import store from '@/store';
import ProgressSpinner from 'primevue/progressspinner';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import ViewNav from '@/components/layout/ViewNav.vue';
import { useViewPagination } from '@/composables/sign-session/view-pagination';
import { useChunkedSignSessionList } from '@/composables/sign-session/view-chunked-list';
import CardOverlay from '@/components/layout/CardOverlay.vue';
import { View } from '@/modules/forms/formatting';
import { getSignSessionSigningRoute } from '@/router/sign-sessions';
import { SignSessionStatuses, SignSessionViews, SignSessionWaitingAction } from '@/types/sign-sessions';

const model = computed<View<SignSessionWaitingAction>>(() => {
   return store.getters['navigation/getView'](SignSessionViews.WaitingAction);
});

const { changePage } = useViewPagination(SignSessionViews.WaitingAction, model);
const { chunkedSessionList } = useChunkedSignSessionList<SignSessionWaitingAction>(model);

</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;
@use 'resources/scss/sign-sessions/sign-session-view';

.session-card {
   &__label {
      font-weight: 300;
      margin: 0 10px 0 0;

      @media screen and (max-width: $md) {
         margin: 0 7px 0 0;
      }

      @media screen and (max-width: $sm) {
         margin: 0 5px 0 0;
      }
   }
}

</style>
