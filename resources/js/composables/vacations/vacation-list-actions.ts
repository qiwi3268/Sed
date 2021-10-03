
type ActionList = Array<{
  name: string
  action: Function
}>

type VacationListActionsModule = {
   options: ActionList
   input: Function
}

export const useVacationListActions = (emit: Function): VacationListActionsModule => {
   const options = [
      { name: 'Редактировать', action: (vacation) => emit('edit', vacation) },
      { name: 'Удалить', action: (vacation) => emit('remove', vacation) }
   ];

   const input = (event, vacation) => {
      event.value(vacation);
   };

   return {
      options,
      input
   };
};

