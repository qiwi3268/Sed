import store from '@/store';
import { GeFile } from '@/store/modules/modals/files/uploader';

/**
 * Открывает модальное окно подписания
 */
export const openFileSigner = (file: GeFile): void => {
   store.dispatch('fileSigner/open', file);
};
