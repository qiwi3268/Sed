import { ApiHandler } from '@/modules/api/ApiHandler';
import { GeFile } from '@/store/modules/modals/files/uploader';
import { getFileSizeString } from '@/modules/modals/files/utils';
import { SignState } from '@/store/modules/modals/files/signer';

export type UploadedFile = {
   readonly starPath: string
   readonly originalName: string
   readonly size: number
}

export class Uploader extends ApiHandler<UploadedFile[], GeFile[]> {
   protected handleSuccessResponse(data: UploadedFile[]): GeFile[] {
      return data.map(uploadedFile => ({
         sizeString: getFileSizeString(uploadedFile.size),
         originalName: uploadedFile.originalName,
         starPath: uploadedFile.starPath,
         validationResult: [],
         signState: SignState.NotSigned
      }));
   }
}
