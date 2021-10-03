import { UploadCallback } from '@/modules/api/ApiHandler';
import { FileFieldRules, UserResponse } from '@/types/api';
import { DefaultHandler } from '@/modules/api/handlers/DefaultHandler';
import { InternalSignValidation } from '@/modules/api/handlers/files/InternalSignValidation';
import { ExternalSignValidation, SignValidation } from '@/modules/api/handlers/files/ExternalSignValidation';
import { MiscItem } from '@/store/modules/misc';
import { Uploader } from '@/modules/api/handlers/files/Uploader';
import { GeFile } from '@/store/modules/modals/files/uploader';
import { SignSessionReadGetter } from '@/modules/api/handlers/sign-sessions/SignSessionReadGetter';
import {
   SignSessionsWaitingActionItemsGetter
} from '@/modules/api/handlers/sign-sessions/SignSessionsWaitingActionItemsGetter';
import { LogicError } from '@/modules/errors/LogicError';
import { SignSessionsInWorkItemsGetter } from '@/modules/api/handlers/sign-sessions/SignSessionsInWorkItemsGetter';
import { SignSessionSigningGetter } from '@/modules/api/handlers/sign-sessions/SignSessionSigningGetter';
import store from '@/store';
import { SignSessionsFinishedItemsGetter } from '@/modules/api/handlers/sign-sessions/SignSessionsFinishedItemsGetter';
import { transformSignSession, transformSignSessionSigning } from '@/modules/forms/mapping/sign-sessions';
import { formatToJsonDate, View } from '@/modules/forms/formatting';
import { transformVacation, transformVacationManagement } from '@/modules/forms/mapping/vacations';
import { VacationsReadGetter } from '@/modules/api/handlers/vacations/VacationsReadGetter';
import { PollAtWorkGetter } from '@/modules/api/handlers/polls/PollAtWorkGetter';
import {
   transformConference, transformConferenceEdit
} from '@/modules/forms/mapping/conferences';
import { ConferenceViewItemsGetter } from '@/modules/api/handlers/conferences/ConferenceViewItemsGetter';
import { VacationManagementGetter } from '@/modules/api/handlers/vacations/VacationManagementGetter';
import { BirthdaysGetter, UserBirthday } from '@/modules/api/handlers/birthdays/BirthdaysGetter';
import { ConferenceReadGetter } from '@/modules/api/handlers/conferences/ConferenceReadGetter';
import { ConferenceEditGetter } from '@/modules/api/handlers/conferences/ConferenceEditGetter';
import { UsersOnVacationGetter } from '@/modules/api/handlers/vacations/UsersOnVacationGetter';
import { SignSession, SignSessionDetails, SignSessionRead, SignSessionViews } from '@/types/sign-sessions';
import { ConferenceForm, ConferenceFormEdit, ConferenceRead, ConferenceViews } from '@/types/conferences';
import { PollAtWork } from '@/types/polls';
import { VacationForm, VacationFormEdit, VacationManagementCategories, VacationRead } from '@/types/vacations';
import { ViewName } from '@/store/modules/navigation';
import { TestHandler } from '@/modules/api/handlers/TestHandler';

export class Api {
   public static refreshToken(): Promise<void> {
      const handler = new DefaultHandler<void>();

      return handler
         .buildPostJSONConfig('/api/auth/refresh', {})
         .send();
   }

   /**
    * Полученния данных текущего пользователя
    */
   public static fetchUser(): Promise<UserResponse> {
      const handler = new DefaultHandler<UserResponse>();

      return handler
         .buildGetConfig('/api/auth/me', {})
         .send();
   }

   /**
    * Получение сотрудников Госэкспертизы
    */
   public static async getUsers(): Promise<MiscItem[]> {
      const handler = new DefaultHandler<MiscItem[]>();

      return handler
         .buildGetConfig('/api/users', {})
         .send();
   }

   /**
    * Получение одиночного справочника
    *
    * @param alias - наименование справочника
    */
   public static async getSingleMisc(alias: string): Promise<MiscItem[]> {
      const handler = new DefaultHandler<MiscItem[]>();

      return handler
         .buildGetConfig('/api/miscs/single', { alias })
         .send();
   }

   /**
    * Получения правил для загружаемых файлов
    *
    * @param mapping - маппинг файлового поля
    */
   public static getFileFieldRules(mapping: string): Promise<FileFieldRules> {
      const handler = new DefaultHandler<FileFieldRules>();

      return handler
         .buildGetConfig('/api/files/rule', { mapping })
         .send();
   }

   /**
    * Загрузка файлов на сервер
    *
    * @param mapping - маппинг файлового поля
    * @param files - добавленные файлы
    * @param uploadCallback - колбэк для обработки запроса в процессе отправки данных
    */
   public static uploadFiles(
      mapping: string,
      files: File[],
      uploadCallback: UploadCallback | null = null
   ): Promise<GeFile[]> {
      const handler = new Uploader();

      const formData = new FormData();
      formData.append('mapping', mapping);
      files.forEach(file => formData.append('files[]', file));

      return handler
         .buildPostConfig('/api/files/upload', formData, uploadCallback)
         .setTimeout(20000)
         .send();
   }

   /**
    * Получение хэша файла для создания подписи
    *
    * @param signatureAlgorithm - алгоритм хэширования
    * @param starPath - идентификатор файла
    */
   public static getFileHash(signatureAlgorithm: string, starPath: string): Promise<string> {
      const handler = new DefaultHandler<string>();

      return handler
         .buildGetConfig('/api/files/csp/hash', { signatureAlgorithm, starPath })
         .send();
   }

   /**
    * Проверка файла на сервере
    *
    * @param starPath - идентификатор файла
    */
   public static checkFile(starPath: string): Promise<void> {
      const handler = new DefaultHandler<void>();

      return handler
         .buildGetConfig('/api/files/check', { starPath })
         .send();
   }

   /**
    * Валидация встроенной подписи
    *
    * @param internalSignatureStarPath - идентификатор файла с встроенной подписью
    */
   public static validateInternalSign(internalSignatureStarPath: string): Promise<SignValidation> {
      const handler = new InternalSignValidation();

      return handler
         .buildGetConfig('/api/files/csp/internalSignatureValidation', { internalSignatureStarPath })
         .send();
   }

   /**
    * Валидация файла открепленной подписи
    *
    * @param originalStarPath - идентификатор файла к которому относится подпись
    * @param file - файл открепленной подписи
    */
   public static validateExternalSign(originalStarPath: string, file: File): Promise<SignValidation> {
      const handler = new ExternalSignValidation();

      const formData = new FormData();
      formData.append('originalStarPath', originalStarPath);
      formData.append('file', file);

      return handler
         .buildPostConfig('/api/files/csp/externalSignatureValidation', formData)
         .setTimeout(20000)
         .send();
   }

   /**
    * Сохранение сессии подписания
    *
    * @param signSession - форма сессии
    */
   public static saveSignSession(signSession: SignSession): Promise<void> {
      const handler = new DefaultHandler<void>();

      return handler
         .buildPostJSONConfig('/api/signatureSessions/create', transformSignSession(signSession))
         .setTimeout(20000)
         .send();
   }

   /**
    * Получение сессии подписания для просмотра
    *
    * @param signatureSessionUuid - идентификатор сессии
    */
   public static getSignSessionRead(signatureSessionUuid: string): Promise<SignSessionRead> {
      const handler = new SignSessionReadGetter();

      return handler
         .buildGetConfig('/api/signatureSessions/show', { signatureSessionUuid })
         .send();
   }

   /**
    * Проверка возможности текущего пользователя удалять сессию подписания
    *
    * @param signatureSessionUuid - идентификатор сессии
    */
   public static async canUserDeleteSignSession(signatureSessionUuid: string): Promise<{ can: boolean }> {
      const handler = new DefaultHandler<{ can: boolean }>();

      const params = {
         signatureSessionUuid,
         userUuid: await store.dispatch('user/getUserUuid')
      };

      return handler
         .buildGetConfig('/api/signatureSessions/guards/canUserDelete', params)
         .send();
   }

   /**
    * Удаление сессии подписания
    *
    * @param signatureSessionUuid - идентификатор сессии
    */
   public static deleteSignSession(signatureSessionUuid: string): Promise<void> {
      const handler = new DefaultHandler<void>();

      return handler
         .buildPostJSONConfig('/api/signatureSessions/delete', { signatureSessionUuid })
         .send();
   }

   /**
    * Проверка возможности текущего пользователя просматривать сессию подписания
    *
    * @param signatureSessionUuid - идентификатор сессии
    */
   public static async canUserSignSignSession(signatureSessionUuid: string): Promise<{ can: boolean }> {
      const handler = new DefaultHandler<{ can: boolean }>();

      const params = {
         signatureSessionUuid,
         userUuid: await store.dispatch('user/getUserUuid')
      };

      return handler
         .buildGetConfig('/api/signatureSessions/guards/canUserSign', params)
         .send();
   }

   /**
    * Действие сохранение подписи к сессии подписания
    *
    * @param signSession - данные сессии
    */
   public static saveSignSessionSigning(signSession: SignSessionDetails): Promise<SignSessionRead> {
      const handler = new DefaultHandler<SignSessionRead>();

      return handler
         .buildPostJSONConfig('/api/signatureSessions/sign', transformSignSessionSigning(signSession))
         .send();
   }

   /**
    * Получение сессии подписания для страницы подписания
    *
    * @param signatureSessionUuid - идентификатор сессии
    */
   public static getSignSessionSigning(signatureSessionUuid: string): Promise<SignSessionDetails> {
      const handler = new SignSessionSigningGetter();

      return handler
         .buildGetConfig('/api/signatureSessions/signing', { signatureSessionUuid })
         .send();
   }

   /**
    * Получение коллекции сессий для отображения во вью
    *
    * @param viewName - наименование вью
    * @param page - выбранная страница
    */
   public static getSignSessionsViewItems(viewName: SignSessionViews, page: number): Promise<View> {
      let handler;

      switch (viewName) {
      case SignSessionViews.WaitingAction:
         handler = new SignSessionsWaitingActionItemsGetter();
         break;
      case SignSessionViews.InWork:
         handler = new SignSessionsInWorkItemsGetter();
         break;
      case SignSessionViews.Finished:
         handler = new SignSessionsFinishedItemsGetter();
         break;
      default:
         throw new LogicError('Не определен обработчик запроса на view');
      }

      return handler
         .buildGetConfig(`/api/signatureSessions/navigation/items/${viewName}`, { page })
         .send();
   }

   /**
    * Получение счетчиков сессий
    *
    * @param viewName - наименование вью
    */
   public static getSignSessionsViewCounter(viewName: ViewName): Promise<{ count: number }> {
      const handler = new DefaultHandler<{ count: number }>();

      return handler
         .buildGetConfig(`/api/signatureSessions/navigation/counters/${viewName}`, {})
         .send();
   }

   /**
    * Сохранение отпуска
    *
    * @param vacation - форма отпуска
    */
   public static saveVacation(vacation: VacationForm): Promise<void> {
      const handler = new DefaultHandler<void>();

      return handler
         .buildPostJSONConfig('/api/vacations/create', transformVacation(vacation))
         .setTimeout(20000)
         .send();
   }

   /**
    * Редактирование отпуска
    *
    * @param vacation - форма отпуска
    */
   public static updateVacation(vacation: VacationFormEdit): Promise<void> {
      const handler = new DefaultHandler<void>();

      return handler
         .buildPostJSONConfig('/api/vacations/update', transformVacationManagement(vacation))
         .setTimeout(20000)
         .send();
   }

   /**
    * Удаление отпуска
    *
    * @param vacationId - id отпуска
    */
   public static deleteVacation(vacationId: number): Promise<void> {
      const handler = new DefaultHandler<void>();

      return handler
         .buildPostJSONConfig('/api/vacations/delete', { vacationId })
         .setTimeout(20000)
         .send();
   }

   public static async canUserManageVacations(): Promise<{ can: boolean }> {
      const handler = new DefaultHandler<{ can: boolean }>();

      const params = { userUuid: await store.dispatch('user/getUserUuid') };

      return handler
         .buildGetConfig('/api/vacations/guards/canUserManage', params)
         .send();
   }

   public static async getUserOnVacationPercent(): Promise<{ percent: number }> {
      const handler = new DefaultHandler<{ percent: number }>();

      return handler
         .buildGetConfig('/api/vacations/percentForCurrentDate', {})
         .send();
   }

   public static getVacationManagement(viewName: VacationManagementCategories): Promise<VacationFormEdit[]> {
      const handler = new VacationManagementGetter();

      return handler
         .buildGetConfig(`/api/vacations/show/${viewName}`, {})
         .send();
   }

   public static getForNext30DaysVacationsRead(): Promise<VacationRead[]> {
      const handler = new VacationsReadGetter();

      return handler
         .buildGetConfig('/api/vacations/show/forNext30Days', {})
         .send();
   }

   public static getVacationsReadForYearAndMonth(year: number, month: number): Promise<VacationRead[]> {
      const handler = new VacationsReadGetter();

      return handler
         .buildGetConfig('/api/vacations/show/forYearAndMonth', { year, month })
         .send();
   }

   public static getUsersOnVacation(): Promise<string[]> {
      const handler = new UsersOnVacationGetter();

      return handler
         .buildGetConfig('/api/vacations/show/forCurrentDate', {})
         .send();
   }

   public static async getPollAtWork(date: Date): Promise<PollAtWork | null> {
      const handler = new PollAtWorkGetter();

      return await handler
         .buildGetConfig('/api/polls/atWork/showForDate', { date: formatToJsonDate(date) })
         .send();
   }

   public static async canUserCreateConferences(): Promise<{ can: boolean }> {
      const handler = new DefaultHandler<{ can: boolean }>();

      const params = { userUuid: await store.dispatch('user/getUserUuid') };

      return handler
         .buildGetConfig('/api/conferences/guards/canUserCreate', params)
         .send();
   }

   public static async canUserUpdateConference(conferenceUuid: string): Promise<{ can: boolean }> {
      const handler = new DefaultHandler<{ can: boolean }>();

      const params = {
         conferenceUuid,
         userUuid: await store.dispatch('user/getUserUuid')
      };

      return handler
         .buildGetConfig('/api/conferences/guards/canUserUpdate', params)
         .send();
   }

   public static async canUserDeleteConference(conferenceUuid: string): Promise<{ can: boolean }> {
      const handler = new DefaultHandler<{ can: boolean }>();

      const params = {
         conferenceUuid,
         userUuid: await store.dispatch('user/getUserUuid')
      };
      return handler
         .buildGetConfig('/api/conferences/guards/canUserDelete', params)
         .send();
   }

   public static saveConference(form: ConferenceForm): Promise<void> {
      const handler = new DefaultHandler<void>();

      return handler
         .buildPostJSONConfig('/api/conferences/create', transformConference(form))
         .setTimeout(20000)
         .send();
   }

   public static updateConference(form: ConferenceFormEdit): Promise<void> {
      const handler = new DefaultHandler<void>();

      return handler
         .buildPostJSONConfig('/api/conferences/update', transformConferenceEdit(form))
         .setTimeout(20000)
         .send();
   }

   public static getConferenceViewItems(viewName: ConferenceViews): Promise<ConferenceRead[]> {
      const handler = new ConferenceViewItemsGetter();

      return handler
         .buildGetConfig(`/api/conferences/navigation/items/${viewName}`, {})
         .send();
   }

   public static getConferenceViewCounter(viewName: ViewName): Promise<{ count: number }> {
      const handler = new DefaultHandler<{ count: number }>();

      return handler
         .buildGetConfig(`/api/conferences/navigation/counters/${viewName}`, {})
         .send();
   }

   public static getDatesWithConferencesByYear(year: string): Promise<string[]> {
      const handler = new DefaultHandler<string[]>();

      return handler
         .buildGetConfig('/api/conferences/navigation/all/datesWithConferencesForYear', { year: year })
         .send();
   }

   public static getConferenceForDateItems(date: Date): Promise<ConferenceRead[]> {
      const handler = new ConferenceViewItemsGetter();

      return handler
         .buildGetConfig('/api/conferences/navigation/items/all/forDate', { date: formatToJsonDate(date) })
         .send();
   }

   public static getBirthdays(): Promise<UserBirthday[]> {
      const handler = new BirthdaysGetter();

      return handler
         .buildGetConfig('/api/birthdays', {})
         .send();
   }

   public static getConferenceRead(conferenceUuid: string): Promise<ConferenceRead> {
      const handler = new ConferenceReadGetter();

      return handler
         .buildGetConfig('/api/conferences/show', { conferenceUuid })
         .send();
   }

   public static deleteConference(conferenceUuid: string): Promise<void> {
      const handler = new DefaultHandler<void>();

      return handler
         .buildPostJSONConfig('/api/conferences/delete', { conferenceUuid })
         .send();
   }

   public static getConferenceEdit(conferenceUuid: string): Promise<ConferenceFormEdit> {
      const handler = new ConferenceEditGetter();

      return handler
         .buildGetConfig('/api/conferences/showForUpdate', { conferenceUuid })
         .send();
   }
}
