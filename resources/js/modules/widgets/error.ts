import store from '@/store';

/**
 * Вызывает модальное окно с ошибкой для отображения пользователю
 *
 * @param title - заголовок ошибки
 * @param messages - сообщения ошибок
 * @param code - код ошибки
 */
export const openError = (title: string, messages: string | string[], code: string | null = null): void => {
   store.dispatch('errorModal/open', { title, messages, code });
};
