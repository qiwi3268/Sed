import { ApiHandler } from '@/modules/api/ApiHandler';
import {
   transformSignSessionDetails,
   transformSignSessionSigners
} from '@/modules/forms/mapping/sign-sessions';
import { SignSessionRead, SignSessionReadResponse } from '@/types/sign-sessions';

export class SignSessionReadGetter extends ApiHandler<SignSessionReadResponse, SignSessionRead> {
   protected handleSuccessResponse(data: SignSessionReadResponse): SignSessionRead {
      return {
         id: data.id,
         ...transformSignSessionDetails(data),
         signers: transformSignSessionSigners(data.signers),
         zipArchiveStarPath: data.zip_archive_star_path
      };
   }
}
