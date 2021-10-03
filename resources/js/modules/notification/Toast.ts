import { ToastOptions, ToastSeverity } from '@/store/modules/widgets/toast';
import store from '@/store';

export const addToast = (options: ToastOptions): void => {
   store.dispatch('toast/addToast', options);
};

export class Toast {
   public static loginAndPasswordRequired(): void {
      addToast({
         severity: ToastSeverity.Error,
         summary: 'Ошибка при авторизации',
         detail: 'Логин и пароль обязательны для заполнения'
      });
   }

   public static getUserError(): void {
      addToast({
         severity: ToastSeverity.Error,
         summary: 'Ошибка',
         detail: 'Ошибка при получении пользователя'
      });
   }

   public static loginError(errorMessage: string): void {
      addToast({
         severity: ToastSeverity.Error,
         summary: 'Ошибка при авторизации',
         detail: errorMessage
      });
   }

   public static apiError(summary: string, detail: string): void {
      addToast({
         severity: ToastSeverity.Error,
         summary,
         detail,
         life: null
      });
   }

   public static conferenceCreated(): void {
      addToast({
         severity: ToastSeverity.Success,
         summary: 'Успешно',
         detail: 'Совещание создано'
      });
   }

   public static conferenceEdited(): void {
      addToast({
         severity: ToastSeverity.Success,
         summary: 'Успешно',
         detail: 'Совещание отредактировано'
      });
   }

   public static noAccessToUpdateConference(): void {
      addToast({
         severity: ToastSeverity.Error,
         summary: 'Ошибка',
         detail: 'У вас нет доступа для редактирования совещания'
      });
   }

   public static conferenceDeleted(): void {
      addToast({
         severity: ToastSeverity.Success,
         summary: 'Успешно',
         detail: 'Совещание удалено'
      });
   }

   public static noAccessToDeleteConference(): void {
      addToast({
         severity: ToastSeverity.Error,
         summary: 'Ошибка удаления',
         detail: 'У вас нет доступа для удаления совещания'
      });
   }

   public static invalidConferenceCreationDate(): void {
      addToast({
         severity: ToastSeverity.Error,
         summary: 'Ошибка при сохранении совещания',
         detail: 'Дата начала совещания должна быть позже текущей даты',
         life: 7000
      });
   }

   public static certIsNotSelected(): void {
      addToast({
         severity: ToastSeverity.Error,
         summary: 'Ошибка при подписании',
         detail: 'Не выбран сертификат'
      });
   }

   public static noAccessToSignSession(): void {
      addToast({
         severity: ToastSeverity.Error,
         summary: 'Ошибка при открытии сессии подписания',
         detail: 'У вас нет доступа к данной сессии'
      });
   }

   public static savedSignSession(): void {
      addToast({
         severity: ToastSeverity.Success,
         summary: 'Успешно',
         detail: 'Сессия подписания успешно создана'
      });
   }

   public static signSessionSigned(): void {
      addToast({
         severity: ToastSeverity.Success,
         summary: 'Успешно',
         detail: 'Подпись сохранена'
      });
   }

   public static invalidSignSessionSigning(): void {
      addToast({
         severity: ToastSeverity.Error,
         summary: 'Ошибка сохранения подписи',
         detail: 'Отсутствует валидная подпись к файлу'
      });
   }

   public static signSessionDeleted(): void {
      addToast({
         severity: ToastSeverity.Success,
         summary: 'Успешно',
         detail: 'Сессия подписания удалена'
      });
   }

   public static multipleSigns(): void {
      addToast({
         severity: ToastSeverity.Error,
         summary: 'Ошибка сохранения подписи',
         detail: 'Можно загрузить только 1 подпись'
      });
   }

   public static noAccessToManageVacations(): void {
      addToast({
         severity: ToastSeverity.Error,
         summary: 'Ошибка при открытии страницы',
         detail: 'У вас нет доступа для редактирования отпусков'
      });
   }

   public static vacationsSaved(): void {
      addToast({
         severity: ToastSeverity.Success,
         summary: 'Успешно',
         detail: 'Изменения отпусков сохранены'
      });
   }

   public static failedVacationSave(): void {
      addToast({
         severity: ToastSeverity.Error,
         summary: 'Ошибка',
         detail: 'Произошла ошибка при обработке отпуска'
      });
   }

   public static noAccessToCreateConference(): void {
      addToast({
         severity: ToastSeverity.Error,
         summary: 'Ошибка при открытии страницы',
         detail: 'У вас нет доступа для создания совещаний'
      });
   }

   public static getCertListError(errorMessage: string): void {
      addToast({
         severity: ToastSeverity.Error,
         summary: 'Ошибка при получении списка сертификатов',
         detail: errorMessage
      });
   }

   public static externalSignValidationError(errorMessage: string): void {
      addToast({
         severity: ToastSeverity.Error,
         summary: 'Ошибка при валидации открепленной подписи',
         detail: errorMessage
      });
   }

   public static signHashError(errorMessage: string): void {
      addToast({
         severity: ToastSeverity.Error,
         summary: 'Ошибка',
         detail: errorMessage
      });
   }
}

