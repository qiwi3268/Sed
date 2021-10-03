import { ApiHandler } from '@/modules/api/ApiHandler';

export class TestHandler extends ApiHandler {
   protected handleSuccessResponse(data: unknown): unknown {
      return Promise.reject(new Error());
      // return super.handleSuccessResponse(data);
   }
}
