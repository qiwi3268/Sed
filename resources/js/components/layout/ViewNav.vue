<template>
<div class="view-nav" v-if="pagination.lastPage > 1">
   <div class="view-nav__pagination pagination">
      <div
         v-for="(link, index) in pagination.links"
         :key="index"
         @click="changePage(link.url)"
         class="pagination__item"
         :class="{
            active: link.active,
            disabled: link.url === null,
         }"
      >
         {{ link.label }}
      </div>
   </div>
   <div class="view-nav__current">{{ pagesLabel }}</div>
</div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { ViewPagination } from '@/types/api';

const props = defineProps<{
   pagination: ViewPagination
}>();

const emit = defineEmits(['changePage']);

const pagesLabel = computed(() => `${props.pagination.currentPage}/${props.pagination.lastPage}`);
const changePage = (url: string | null) => {
   if (url !== null) {
      emit('changePage', url);
   }
};
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.view-nav {
   @extend %card;

   display: flex;
   align-items: center;
   justify-content: space-between;
   padding: 0 3px;

   margin: 0 0 10px 0;

   &__current {
      padding: 10px;
      word-break: normal;

      @media screen and (max-width: $md) {
         padding: 7px;
      }

      @media screen and (max-width: $sm) {
         padding: 4px;
      }
   }
}

.pagination {

   display: flex;
   align-items: center;

   &__item {
      border-radius: 3px;
      cursor: pointer;
      margin: 3px;
      padding: 7px 10px;
      white-space: nowrap;

      @media screen and (max-width: $md) {
         padding: 4px 7px;
         margin: 2px;
      }

      @media screen and (max-width: $sm) {
         padding: 2px 5px;
         margin: 1px;
      }

      transition: background-color .1s;

      &:hover {
         background-color: $background-blue;
      }

      &.active {
         background-color: $shadow-blue;

         &:hover {
            background-color: #ABC9E9;
         }
      }

      &.disabled {
         cursor: default;
         color: #A8A8A8;

         &:hover {
            background-color: unset;
         }
      }
   }
}

</style>
