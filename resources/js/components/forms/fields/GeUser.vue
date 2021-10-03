<template>
<div class="form-field"
     :class="{ invalid: unit.$error, required: unit.mRequired || unit.required }"
>
   <div class="form-field__label">{{ label }}</div>
   <div class="form-field__body">
      <Dropdown
         v-model="unit.$model"
         :options="users"
         optionLabel="label"
         :filter="true"
         :filterFields="['label']"
         :showClear="true"
         scrollHeight="400px"
         @beforeShow.once="fetchUsers"
         @change="$emit('select')"
         placeholder="Выберите сотрудника"
         class="form-field__dropdown"
      >
         <template #empty>
            <ProgressSpinner style="width:50px;height:50px" animationDuration=".8s"/>
         </template>
      </Dropdown>
      <div v-if="unit.$error" class="form-field__error">{{ unit.$errors[0].$message }}</div>
   </div>
</div>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, watch } from 'vue';
import Dropdown from 'primevue/dropdown';
import ProgressSpinner from 'primevue/progressspinner';
import { FormUnit } from '@/modules/forms/validators';
import store from '@/store';
import { MiscItem } from '@/store/modules/misc';

const props = defineProps<{
   label: string
   field: FormUnit<MiscItem>
}>();

const emit = defineEmits(['select']);

const unit = reactive<FormUnit<MiscItem>>(props.field);

const users = computed<MiscItem[]>(() => store.getters['organization/getUsers']);
const fetchUsers = async() => store.dispatch('organization/fetchUsers');

watch(
   () => store.state.organization.usersFetched,
   () => {
      if (unit.$model) {
         unit.$model = users.value.find(user => user.id === unit.$model.id)!;
      }
   },
   { immediate: false }
);

onMounted(() => {
   if (unit.$model) {
      store.dispatch('organization/setUser', unit.$model);
   }
});

</script>

<style scoped lang="scss">
</style>
