<template>
<router-link :to="getConferenceReadRoute(model.uuid)" class="conference-card">
   <CardOverlay label="Просмотреть"/>
   <div class="conference-card__header">
      <div class="conference-card__topic">{{ model.topic }}</div>
      <div class="conference-card__date">{{ formatDateTime(model.startAt) }}</div>
   </div>
   <div class="conference-card__body">
         <div class="conference-card__main">
            <div class="conference-card__info">
               <div class="conference-card__col">
                  <div class="conference-card__label">Участники</div>
                  <div class="conference-card__value">
                     <div
                        v-for="member in model.members"
                        :key="member"
                        class="conference-card__member"
                     >{{ member }}
                     </div>
                  </div>
               </div>
               <div class="conference-card__col">
                  <div class="conference-card__label">Форма</div>
                  <div class="conference-card__value">{{ model.conferenceForm }}</div>
               </div>
               <div class="conference-card__col">
                  <div class="conference-card__label">Место проведения</div>
                  <div class="conference-card__value">{{ model.conferenceLocation }}</div>
               </div>
            </div>
            <div class="conference-card__actions">
               <div
                  class="conference-card__status"
                  :class="{ 'conference-card__status--filled': isPlanned }"
               >{{ status }}
               </div>
               <a
                  v-if="model.vksHref && isNeedVksLink"
                  @click="(event) => openLink(event, model.vksHref)"
                  :href="model.vksHref"
                  class="conference-card__link"
               >
                  <Button
                     label="Подключиться"
                     icon="pi pi-external-link"
                     iconPos="right"
                  />
               </a>
            </div>
         </div>
         <div v-if="model.connectionResponsible" class="conference-card__row">
            <div class="conference-card__label">Ответственный за подключение:</div>
            <div class="conference-card__value">{{ model.connectionResponsible }}</div>
         </div>
         <div v-if="model.outerMembers" class="conference-card__row">
            <div class="conference-card__label">Внешние участники:</div>
            <div class="conference-card__value">{{ model.outerMembers }}</div>
         </div>
         <div v-if="model.comment" class="conference-card__row">
            <div class="conference-card__label">Комментарий:</div>
            <div class="conference-card__value">{{ model.comment }}</div>
         </div>
      </div>
</router-link>
</template>

<script setup lang="ts">
import Button from 'primevue/button';
import CardOverlay from '@/components/layout/CardOverlay.vue';
import { computed } from 'vue';
import { add, isAfter } from 'date-fns';
import { getConferenceReadRoute } from '@/router/conferences';
import { formatDateTime } from '@/modules/forms/formatting';
import { ConferenceRead, ConferenceStatuses } from '@/types/conferences';

const props = defineProps<{
   model: ConferenceRead
}>();

const status = computed(() => {
   return isAfter(props.model.startAt, new Date()) ? ConferenceStatuses.Planned : ConferenceStatuses.Finished;
});
const isPlanned = computed(() => status.value === ConferenceStatuses.Planned);

const isNeedVksLink = computed(() => {
   return isAfter(add(props.model.startAt, { hours: 2 }), new Date());
});

const openLink = (event, link) => {
   event.preventDefault();
   window.open(link);
};
</script>

<style scoped lang="scss">
@use 'resources/scss/conferences/conference-card';
</style>
