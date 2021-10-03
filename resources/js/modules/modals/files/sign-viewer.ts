import store from '@/store';
import { GeFile } from '@/store/modules/modals/files/uploader';

export const openSignViewer = (file: GeFile): void => {
   if (file.validationResult.length > 0) {
      store.dispatch('signViewer/open', file);
   }
};
