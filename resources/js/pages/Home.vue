<template>
<TheHeader/>
<div id="content" class="content">
   <router-view/>
</div>
<TheFooter/>
<transition name="fade">
   <div v-if="isErrorOpened">
      <GlobalError/>
   </div>
</transition>
</template>

<script setup lang="ts">
import TheHeader from '@/components/layout/TheHeader.vue';
import TheFooter from '@/components/layout/TheFooter.vue';
import GlobalError from '@/components/widgets/GlobalError.vue';
import { computed } from 'vue';
import store from '@/store';

const isErrorOpened = computed(() => store.state.errorModal.opened);
</script>

<style scoped lang="scss">

</style>
