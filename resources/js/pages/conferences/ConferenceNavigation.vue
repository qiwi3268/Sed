<template>
<div class="medium-wrapper conference-navigation">
   <div class="navigation-sidebar">
      <router-link v-if="canCreateConference" :to="{ name: Routes.ConferenceCreate }" class="navigation-sidebar__actions">
         <div class="navigation-sidebar__create p-ripple" v-ripple>Создать совещание</div>
      </router-link>
      <div class="navigation-sidebar__block">
         <router-link :to="getConferenceNavigationRoute()">
            <div class="navigation-sidebar__title p-ripple p-ripple--dark-green" v-ripple>Мои совещания</div>
         </router-link>
         <NavigationLabel
            label="Сегодня"
            :viewName="ConferenceViews.MyTodays"
         />
         <NavigationLabel
            label="Запланированные"
            :viewName="ConferenceViews.MyPlanned"
         />
      </div>
      <div class="navigation-sidebar__block">
         <ConferenceForDateLabel/>
      </div>
   </div>
   <div class="list-view">
      <router-view/>
   </div>
</div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import store from '@/store';
import { Routes } from '@/router';
import ConferenceViewLabel from '@/components/conferences/ConferenceViewLabel.vue';
import ConferenceForDateLabel from '@/components/conferences/ConferenceForDateLabel.vue';
import { getConferenceNavigationRoute } from '@/router/conferences';
import { ConferenceViews } from '@/types/conferences';
import NavigationLabel from '@/components/widgets/NavigationLabel.vue';

store.dispatch('conferences/checkCanCreateConferences');
const canCreateConference = computed(() => store.state.conferences.canCreateConference);
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;
@use 'resources/scss/layout/navigation-sidebar';
@use 'resources/scss/elements/action-button';

.conference-navigation {
   @extend %navigation;

   .list-view {
      flex-grow: 1;
   }

   .navigation-sidebar {
      min-width: 250px;

      @media screen and (max-width: $md) {
         min-width: 150px;
      }
   }
}


</style>
