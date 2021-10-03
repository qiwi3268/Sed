<template>
<div class="medium-wrapper">
   <div class="sub-header">
      <div class="sub-header__title">
         <div class="sub-header__label">Менеджмент отпусков</div>
         <Button @click="displayDialog = true" label="Добавить отпуск" class="p-button-outlined"/>
      </div>
      <SubHeaderActions @ok="saving = true" @cancel="leave" :handlingAction="saving"/>
   </div>
   <div class="vacations">
      <VacationsToSaveList
         v-if="vacationsToSave.length > 0"
         :vacations="vacationsToSave"
         @edit="editVacation"
         @remove="(vacation) => removeVacation(vacation, vacationsToSave)"
      />
      <VacationManagementList
         v-if="nextVacations.length > 0"
         :vacations="nextVacations"
         @edit="editNextVacation"
         @remove="removeNextVacation"
         title="Актуальные отпуски"
         styleModifier="blue"
      />
      <VacationManagementList
         v-if="pastVacations.length > 0"
         :vacations="pastVacations"
         @edit="editPastVacation"
         @remove="removePastVacation"
         title="Прошедшие отпуски"
         styleModifier="dark-green"
      />
      <div v-if="!fetchedPastVacations" class="vacations__download">
         <Button
            @click="downloadPast"
            label="Загрузить прошедшие"
            class="p-button-rounded"
         />
      </div>
   </div>
</div>
<VacationDialog
   v-model:visible="displayDialog"
   :form="v$"
   @save="handleVacationChanges"
   @hide="clearModal"
/>


</template>

<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue';
import SubHeaderActions from '@/components/widgets/SubHeaderActions.vue';
import VacationDialog from '@/components/vacations/VacationDialog.vue';
import Button from 'primevue/button';
import useVuelidate from '@vuelidate/core';
import store from '@/store';
import { clone, deepClone } from '@/modules/lib';
import { isVacationsEqual } from '@/modules/forms/mapping/vacations';
import VacationManagementList from '@/components/vacations/VacationManagementList.vue';
import VacationsToSaveList from '@/components/vacations/VacationsToSaveList.vue';
import { returnToVacationList } from '@/router/vacations';
import { useRouteLeaveGuard } from '@/composables/navigation';
import { Confirm } from '@/modules/modals/Confirm';
import { VacationForm, VacationFormEdit } from '@/types/vacations';

const model = reactive(store.getters['vacations/getVacationEmptyForm']);
const rules = computed(() => store.getters['vacations/getVacationFormRules']);
const v$ = useVuelidate(rules, model);

const displayDialog = ref(false);
const clearModal = () => Object.assign(model, store.getters['vacations/getVacationEmptyForm']);

const vacationsToSave = ref<VacationForm[]>([]);
const handleVacationChanges = (key) => {
   const index = vacationsToSave.value.findIndex(vacation => vacation.key === key);
   if (index !== -1) {
      vacationsToSave.value.splice(index, 1, clone(model));
   } else if (nextVacations.value.find(vacation => vacation.key === key)) {
      updateVacationToEdit(key, store.state.vacations.nextVacations, cachedNextVacations.value);
   } else if (pastVacations.value.find(vacation => vacation.key === key)) {
      updateVacationToEdit(key, store.state.vacations.pastVacations, cachedPastVacations.value);
   } else {
      vacationsToSave.value.push(clone(model));
   }
};

const updateVacationToEdit = (key, collection, cache) => {
   const formerValue = cache.find(vacation => vacation.key === key);
   model.changed = !isVacationsEqual(formerValue!, model);

   const targetVacation = collection.find(vacation => vacation.key === model.key);
   Object.assign(targetVacation, model);
};

const saving = ref(false);
watch(
   saving,
   () => {
      if (saving.value) {
         saveChanges();
      }
   },
   { immediate: false }
);

const saveChanges = async() => {
   const changes = {
      vacationsToSave: vacationsToSave.value,
      vacationsToDelete: vacationsToDelete.value
   };

   await store.dispatch('vacations/handleVacations', changes);
   saving.value = false;
};

const editVacation = (vacation) => {
   Object.assign(model, deepClone(vacation));
   displayDialog.value = true;
};

const vacationsToDelete = ref<VacationFormEdit[]>([]);
const removeVacation = (vacationToRemove, collection) => {
   Confirm.deleteVacation(() => {
      const index = collection.findIndex(vacation => vacation.key === vacationToRemove.key);
      collection.splice(index, 1);
   });
};

const nextVacations = computed<VacationFormEdit[]>(() => store.state.vacations.nextVacations);
const cachedNextVacations = ref<VacationFormEdit[]>([]);
const editNextVacation = (vacation) => {
   cachedNextVacations.value.push(deepClone(vacation));
   editVacation(vacation);
};
const removeNextVacation = (vacation) => {
   vacationsToDelete.value.push(vacation);
   removeVacation(vacation, store.state.vacations.nextVacations);
};

store.dispatch('vacations/clearPastVacations');
const fetchedPastVacations = ref(false);
const downloadPast = () => {
   fetchedPastVacations.value = true;
   store.dispatch('vacations/getPastVacations');
};
const pastVacations = computed<VacationFormEdit[]>(() => store.state.vacations.pastVacations);
const cachedPastVacations = ref<VacationFormEdit[]>([]);
const editPastVacation = (vacation) => {
   cachedPastVacations.value.push(deepClone(vacation));
   editVacation(vacation);
};
const removePastVacation = (vacation) => {
   vacationsToDelete.value.push(vacation);
   removeVacation(vacation, store.state.vacations.pastVacations);
};

const leave = () => returnToVacationList();
useRouteLeaveGuard();
</script>

<style lang="scss">
.vacation {
   &__sidebar {
      .p-dropdown-label {
         display: none;
      }

      .p-dropdown-trigger {
         width: unset !important;
         padding: 7px;
      }
   }
}
</style>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;
@use 'resources/scss/layout/sub-header';

.sub-header {
   &__title {
      justify-content: space-between;
      padding: 10px 20px;

      @media screen and (max-width: $md) {
         padding: 7px 15px;
      }

      @media screen and (max-width: $sm) {
         padding: 5px 10px;

      }
   }
}

.vacations {
   &__download {
      text-align: center;
   }
}

</style>
