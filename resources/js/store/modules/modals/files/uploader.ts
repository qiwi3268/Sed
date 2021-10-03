import { ActionContext } from 'vuex';
import store, { RootState } from '@/store';
import { Api } from '@/modules/api/Api';
import { safeMapGetter, safeMapSetter } from '@/modules/lib';
import { FileFieldRules } from '@/types/api';
import { checkFiles } from '@/modules/modals/files/checker';
import { ValidationResult } from '@/modules/api/handlers/files/ExternalSignValidation';
import { SignState } from '@/store/modules/modals/files/signer';
import { safeExecute } from '@/modules/api/wrappers';

export type GeFile = {
   readonly starPath: string
   readonly originalName: string
   readonly sizeString: string
   validationResult: ValidationResult[]
   signState: SignState
   signStarPath?: string
}

export type GeFileRead = GeFile & {
   readonly isOriginal: boolean
   readonly mapping: string
}

type Mapping = string;

export type FilesUploaderState = {
   opened: boolean
   isUploading: boolean
   rules: Map<Mapping, FileFieldRules>
   mapping: string
   model: GeFile[]
   multiple: boolean
   isFetchedRules: Set<Mapping>
}

export type UploadFilesPayload = {
   files: File[]
   uploadProgressCallback: (event: ProgressEvent) => void
}

export type FileFieldPayload = {
   mapping: string
   model: GeFile[]
   multiple: boolean
}

type Context = ActionContext<FilesUploaderState, RootState>

export default {
   namespaced: true,
   state: (): FilesUploaderState => ({
      opened: false,
      isUploading: false,
      rules: new Map<string, FileFieldRules>(),
      mapping: '',
      model: [],
      multiple: false,
      isFetchedRules: new Set()
   }),
   mutations: {},
   actions: {
      open({ state }: Context, { mapping, model }: FileFieldPayload): void {
         state.opened = true;
         state.mapping = mapping;
         state.model = model;
      },
      close({ state }: Context): void {
         state.opened = false;
         state.mapping = '';
         state.model = [];
      },
      async getFieldRules({ state, dispatch }: Context): Promise<FileFieldRules> {
         if (!state.rules.has(state.mapping)) {
            await dispatch('fetchFieldRules', state.mapping);
         }
         return Promise.resolve(safeMapGetter(state.rules, state.mapping));
      },
      async fetchFieldRules({ state }: Context, mapping: string): Promise<void> {
         if (!state.isFetchedRules.has(mapping)) {
            state.isFetchedRules.add(mapping);
            const rules = await safeExecute(Api.getFileFieldRules(mapping));
            safeMapSetter(state.rules, mapping, rules);
         }
      },
      async upload({ state, dispatch }: Context, { files, uploadProgressCallback }: UploadFilesPayload): Promise<void> {
         if (!state.isUploading) {
            state.isUploading = true;
            const rules = await dispatch('getFieldRules');
            if (checkFiles(files, rules)) {
               await safeExecute(dispatch('sendFiles', { files, uploadProgressCallback }));
            }
            state.isUploading = false;
         }
      },
      async sendFiles({ state }: Context, { files, uploadProgressCallback }: UploadFilesPayload): Promise<void> {
         const uploadedFiles = await Api.uploadFiles(state.mapping, files, uploadProgressCallback);
         uploadedFiles.forEach(file => state.model.push(file));
      }
   },
   getters: {
      isMultiple(state: FilesUploaderState): (mapping: string) => boolean {
         return (mapping) => !!mapping &&
            state.rules.has(mapping) &&
            safeMapGetter(state.rules, mapping).multipleAllowed;
      }
   }

};
