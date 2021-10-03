export type Month = {
   number: number
   shortName: string
   fullName: string
}

export const getMonthList = (): Month[] => {
   return [
      { number: 0, shortName: 'Янв', fullName: 'Январь' },
      { number: 1, shortName: 'Фев', fullName: 'Февраль' },
      { number: 2, shortName: 'Мар', fullName: 'Март' },
      { number: 3, shortName: 'Апр', fullName: 'Апрель' },
      { number: 4, shortName: 'Май', fullName: 'Май' },
      { number: 5, shortName: 'Июн', fullName: 'Июнь' },
      { number: 6, shortName: 'Июл', fullName: 'Июль' },
      { number: 7, shortName: 'Авг', fullName: 'Август' },
      { number: 8, shortName: 'Сен', fullName: 'Сентябрь' },
      { number: 9, shortName: 'Окт', fullName: 'Октябрь' },
      { number: 10, shortName: 'Ноя', fullName: 'Ноябрь' },
      { number: 11, shortName: 'Дек', fullName: 'Декабрь' }
   ];
};

const monthNames = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
   'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
];

const inflectedMonthNames = ['Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня',
   'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'
];

export const getFullMonthNameByNumber = (monthNumber: number): string => {
   return monthNames[monthNumber];
};

export const getInflectedMonthNameByNumber = (monthNumber: number): string => {
   return inflectedMonthNames[monthNumber];
};

export const getWeekDay = (date: Date): string => {
   let result;

   switch (date.getDay()) {
   case 0:
      result = 'Воскресенье';
      break;
   case 1:
      result = 'Понедельник';
      break;
   case 2:
      result = 'Вторник';
      break;
   case 3:
      result = 'Среда';
      break;
   case 4:
      result = 'Четверг';
      break;
   case 5:
      result = 'Пятница';
      break;
   case 6:
      result = 'Суббота';
      break;
   }

   return result;
};

export const getCurrentYear = (): number => {
   return new Date().getFullYear();
};

export const getCurrentMonth = (): number => {
   return new Date().getMonth();
};
