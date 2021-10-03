<template>
<div v-if="model" class="card-view">
   <ViewNav :pagination="model.pagination" @changePage="changePage"/>
   <div class="card-view__list">
      <div v-for="(part, index) in chunkedSessionList" :key="index" class="card-view__col">
         <router-link
            v-for="session in part"
            :key="session.uuid"
            :to="getSignSessionReadRoute(session.uuid)"
            class="card-view__item session-card"
         >
            <CardOverlay label="Просмотреть"/>
            <div class="session-card__header">{{ session.title }}</div>
            <div class="session-card__body">
               <div class="session-card__signers">
                  <div v-for="(signer, index) in session.signers" :key="index" class="session-card__signer">
                     <FontAwesomeIcon icon="check" class="session-card__icon valid"/>
                     <div class="session-card__value">{{ signer.fio }}</div>
                  </div>
               </div>
               <div class="session-card__info">
                  <div class="session-card__col">
                     <div class="session-card__label">Создано</div>
                     <div class="session-card__value">{{ session.createdAt }}</div>
                  </div>
                  <div class="session-card__col">
                     <div class="session-card__label">Автор</div>
                     <div class="session-card__value">{{ session.author }}</div>
                  </div>
                  <div class="session-card__col">
                     <div class="session-card__label">Выполнено</div>
                     <div class="session-card__value">{{ session.signedAt }}</div>
                  </div>
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
import ProgressSpinner from 'primevue/progressspinner';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import ViewNav from '@/components/layout/ViewNav.vue';
import store from '@/store';
import { useViewPagination } from '@/composables/sign-session/view-pagination';
import { useChunkedSignSessionList } from '@/composables/sign-session/view-chunked-list';
import CardOverlay from '@/components/layout/CardOverlay.vue';
import { View } from '@/modules/forms/formatting';
import { getSignSessionReadRoute } from '@/router/sign-sessions';
import { SignSessionFinished, SignSessionViews } from '@/types/sign-sessions';

const model = computed<View<SignSessionFinished>>(() => {
   return store.getters['navigation/getView'](SignSessionViews.Finished);
});

const { changePage } = useViewPagination(SignSessionViews.Finished, model);
const { chunkedSessionList } = useChunkedSignSessionList<SignSessionFinished>(model);
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;
@use 'resources/scss/sign-sessions/sign-session-view';

.session-card {
   &__col {
      &:last-child {
         flex-grow: 1;
         display: flex;
         flex-direction: column;
         align-items: flex-end;
      }
   }

   &__label {
      font-weight: 300;
      border-bottom: 1px solid $border;
      padding: 0 0 5px 0;
      margin: 0 0 5px 0;
   }
}

</style>
