<template>
<div class="file-info">
   <FontAwesomeIcon size="lg" class="file-info__icon" :icon="getFileIcon(file.originalName)"/>
   <div class="file-info__description">
      <div class="file-info__name">{{ file.originalName }}</div>
      <div class="file-info__size">{{ file.sizeString }}</div>
   </div>
</div>
</template>

<script setup lang="ts">
import { getFileIcon } from '@/modules/modals/files/utils';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { GeFile } from '@/store/modules/modals/files/uploader';

const props = defineProps<{
   file: GeFile
}>();

</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.file-info {
   align-items: center;
   border-radius: 4px;
   cursor: pointer;
   display: flex;
   flex-grow: 1;
   margin: 0 5px 0 0;
   padding: 3px 8px;
   transition: .15s;

   &:hover {
      background-color: $background;
   }

   &__size {
      color: #6e6e6e;
      font-size: 0.75rem;
   }

   &__icon {
      color: $icon-dark-blue;
      cursor: pointer;
      margin: 0 7px 0 0;
   }

}

</style>
