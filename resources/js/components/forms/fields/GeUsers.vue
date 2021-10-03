<template>
<BaseMultiselect
   @open="fetchUsers"
   :label="label"
   :field="unit"
   :items="users"
   class="ge-users"
/>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, watch, withDefaults } from 'vue';
import { MiscItem } from '@/store/modules/misc';
import store from '@/store';
import { FormUnit } from '@/modules/forms/validators';
import BaseMultiselect from '@/components/forms/fields/BaseMultiselect.vue';

const props = withDefaults(defineProps<{
   label: string
   field: FormUnit<MiscItem[]>
   exclude?: MiscItem[]
}>(), { exclude: () => [] });

const unit = reactive(props.field);

watch(
   () => props.exclude,
   () => {
      props.exclude.forEach(user => {
         const replacements = unit.$model;
         const index = replacements.findIndex(replacement => replacement.id === user.id);

         if (index !== -1) {
            replacements.splice(index, 1);
         }
      });
   },
   { immediate: false }
);

const users = computed(() => {
   return props.exclude.length === 0
      ? store.getters['organization/getUsers']
      : store.getters['organization/getUsers'].filter(user => !props.exclude.find(excludedUser => excludedUser.id === user.id));
});

const fetchUsers = async() => { store.dispatch('organization/fetchUsers'); };

watch(
   () => store.state.organization.usersFetched,
   () => {
      if (unit.$model.length > 0) {
         unit.$model = unit.$model.map(selectedUser => {
            return users.value.find(user => user.id === selectedUser.id)!;
         });
      }
   },
   { immediate: false }
);

onMounted(() => {
   unit.$model.forEach(user => store.dispatch('organization/setUser', user));
});

</script>


<style lang="scss">
.ge-users {
   .misc-modal {
      &__selected-item,
      &__item {
         white-space: nowrap;
      }
   }
}
</style>
