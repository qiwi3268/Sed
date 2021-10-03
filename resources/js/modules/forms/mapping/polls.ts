import { Fio } from '@/modules/utils/Fio';
import { LogicError } from '@/modules/errors/LogicError';
import { safeMapGetter } from '@/modules/lib';
import {
   IconDescription,
   PollAtWorkOption,
   PollAtWorkOptionResponse,
   PollAtWorkResponse,
   PollAtWorkUser, PollAtWorkUserResponse
} from '@/types/polls';

export const transformPollAtWorkUsers = (options: PollAtWorkOptionResponse[], users: PollAtWorkUserResponse[]): PollAtWorkUser[] => {
   try {
      return users.map(user => ({
         fio: new Fio(user.last_name, user.first_name, user.middle_name).getShortFio(),
         optionId: user.answer_id
            ? options.find(option => option.answer_id === user.answer_id)!.id
            : null,
         onVacation: user.on_vacation
      }));
   } catch (exc) {
      throw new LogicError(`Ошибка при маппинге пользователя: ${(exc as Error).message}`);
   }
};

export const transformPollAtWorkOptions = (data: PollAtWorkResponse): PollAtWorkOption[] => {
   const options = new Map();

   data.options.forEach(option => {
      options.set(option.id, {
         name: option.name,
         users: [],
         icon: getPollAtWorkOptionIconDetails(option.id)
      });
   });

   const onVacationId = Math.max(...data.options.map(option => option.id)) + 1;
   options.set(onVacationId, {
      name: 'В отпуске',
      users: [],
      icon: {
         name: 'umbrella-beach',
         modifier: 'green'
      }
   });

   const notVotedId = onVacationId + 1;
   options.set(notVotedId, {
      name: 'Не проголосовал',
      users: [],
      icon: {
         name: 'comment-slash',
         modifier: 'red'
      }
   });

   transformPollAtWorkUsers(data.options, data.users).forEach(user => {
      if (user.onVacation) {
         safeMapGetter(options, onVacationId).users.push(user.fio);
      } else if (user.optionId) {
         safeMapGetter(options, user.optionId).users.push(user.fio);
      } else {
         safeMapGetter(options, notVotedId).users.push(user.fio);
      }
   });

   return Array.from(options.keys())
      .sort()
      .map(optionId => options.get(optionId));
};

export const getPollAtWorkOptionIconDetails = (optionId: number): IconDescription => {
   switch (optionId) {
   case 1:
      return {
         name: 'building',
         modifier: 'blue'
      };
   case 2:
      return {
         name: 'laptop-house',
         modifier: 'blue'
      };
   case 3:
      return {
         name: 'times',
         modifier: 'red'
      };
   default:
      return {
         name: 'bars',
         modifier: 'red'
      };
   }
};
