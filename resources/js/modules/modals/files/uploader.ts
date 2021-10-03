import store from '@/store';
import { computed } from 'vue';
import { GeFile } from '@/store/modules/modals/files/uploader';

/**
 * Открывает модальное окно загрузчика
 *
 */
export const openFileUploader = (mapping: string, model: GeFile[]): void => {
   store.dispatch('filesUploader/open', { mapping, model });
};

/**
 * Открыто ли модальное окно загрузчика
 */
export const isFileUploaderOpened = computed<boolean>(() => store.state.filesUploader.opened);
