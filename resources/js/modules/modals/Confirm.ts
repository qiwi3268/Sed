import { ConfirmOptions } from '@/store/modules/widgets/confirm';
import store from '@/store';
import { NavigationGuardNext } from 'vue-router';

export const confirmLeaving = (next: NavigationGuardNext): void => {
   openConfirm({
      message: 'Вы уверены, что хотите выйти?',
      header: 'Подтверждение',
      icon: 'pi pi-exclamation-triangle',
      accept: () => next(),
      reject: () => next(false)
   });
};

export const openConfirm = (options: ConfirmOptions): void => {
   store.dispatch('confirm/openConfirm', options);
};

export class Confirm {
   public static logout(accept: () => void): void {
      openConfirm({
         message: 'Вы уверены, что хотите выйти?',
         header: 'Подтверждение',
         icon: 'pi pi-exclamation-triangle',
         accept
      });
   }

   public static deleteConference(accept: () => void): void {
      openConfirm({
         message: 'Вы уверены, что хотите удалить совещание?',
         header: 'Подтверждение',
         acceptClass: 'p-button-danger',
         icon: 'pi pi-exclamation-triangle',
         accept
      });
   }

   public static deleteSignSession(accept: () => void): void {
      openConfirm({
         message: 'Вы уверены, что хотите удалить сессию?',
         header: 'Подтверждение',
         acceptClass: 'p-button-danger',
         icon: 'pi pi-exclamation-triangle',
         accept
      });
   }

   public static deleteVacation(accept: () => void): void {
      openConfirm({
         message: 'Вы уверены, что хотите удалить отпуск?',
         header: 'Подтверждение',
         icon: 'pi pi-exclamation-triangle',
         accept
      });
   }
}
