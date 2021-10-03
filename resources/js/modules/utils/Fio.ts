import { LogicError } from '@/modules/errors/LogicError';
import { ucFirst } from '@/modules/lib';

export class Fio {
   private readonly lastName: string;
   private readonly firstName: string;
   private readonly middleName: string | null;

   public constructor(lastName: string | null, firstName: string | null, middleName: string | null = null) {
      if (!lastName || !firstName) {
         throw new LogicError('Некорректное ФИО');
      }

      this.lastName = ucFirst(lastName.toLowerCase());
      this.firstName = ucFirst(firstName.toLowerCase());
      this.middleName = middleName ? ucFirst(middleName.toLowerCase()) : middleName;
   }

   public getLongFio(): string {
      const result = `${this.lastName} ${this.firstName}`;
      return this.middleName ? `${result} ${this.middleName}` : result;
   }

   public getShortFio(): string {
      const result = `${this.lastName} ${this.firstName.slice(0, 1)}.`;
      return this.middleName ? `${result}${this.middleName.slice(0, 1)}.` : result;
   }
}
