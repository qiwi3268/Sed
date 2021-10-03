const esModules = ['@websanova'].join('|');

module.exports = {
   preset: 'ts-jest',
   testEnvironment: 'jsdom',
   transform: {
      '^.+\\.vue$': '@vue/vue3-jest',
      '^.+\\.(ts)$': 'ts-jest',
      '^.+\\.(js)$': 'babel-jest'
   },
   moduleNameMapper: { '^@/(.*)$': '<rootDir>/resources/js/$1' },
   transformIgnorePatterns: [
      `/node_modules/(?!${esModules})`
   ],



   moduleFileExtensions: ['vue', 'js', 'json', 'jsx', 'ts', 'tsx', 'node']
};

