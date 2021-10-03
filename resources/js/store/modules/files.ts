import { ActionContext } from 'vuex';
import { RootState } from '@/store';
import { Api } from '@/modules/api/Api';

export type FilesState = {

}

type Context = ActionContext<FilesState, RootState>

export default {
   namespaced: true,
   state: (): FilesState => ({}),
   mutations: {},
   actions: {
      downloadFile(context: Context, starPath: string): void {
         Api.checkFile(starPath)
            .then(() => {
               const searchParams = new URLSearchParams({ starPath: starPath });
               location.href = `${location.origin}/files/download?${searchParams}`;
            });
      }
   },
   getters: {}
};
