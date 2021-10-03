module.exports = {
   root: true,
   env: {
      node: true
   },
   extends: [
      'plugin:vue/vue3-essential',
      '@vue/standard',
      '@vue/typescript/recommended'
   ],
   parserOptions: {
      ecmaVersion: 2020
   },
   ignorePatterns: [
      '*.js',
      'resources/js/modules/signatures/GeCades.ts'
   ],
   globals: {
      defineProps: 'readonly',
      defineEmits: 'readonly',
      defineExpose: 'readonly',
      withDefaults: 'readonly'
   },
   rules: {
      'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
      'no-debugger': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
      'no-multiple-empty-lines': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
      'no-trailing-spaces': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
      'no-unused-vars': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
      '@typescript-eslint/no-unused-vars': process.env.NODE_ENV === 'production' ? 'warn' : 'off',

      '@typescript-eslint/member-delimiter-style': ['error', {
         multiline: {
            delimiter: 'none',
            requireLast: true
         },
         singleline: {
            delimiter: 'comma',
            requireLast: false
         },
         multilineDetection: 'brackets'
      }],

      'space-before-function-paren': ['warn', 'never'],
      'func-call-spacing': ['warn', 'never'],
      'padded-blocks': 'warn',
      'spaced-comment': 'warn',
      semi: ['warn', 'always'],
      indent: ['warn', 3],
      'comma-dangle': 'warn',
      'no-new': 'warn',
      '@typescript-eslint/no-empty-function': 'warn',

      camelcase: 'off',
      'no-prototype-builtins': 'off',
      'no-return-assign': 'off',
      '@typescript-eslint/no-non-null-assertion': 'off',
      '@typescript-eslint/ban-types': [
         'error',
         {
            types: {
               Function: false
            }
         }
      ],

      'vue/no-unused-components': 'warn',

      // 'vue/component-api-style': ['error', ['script-setup']],
      'vue/valid-template-root': 'error',
      'vue/no-boolean-default': ['error', 'default-false'],
      'vue/v-on-function-call': 'error',
      'vue/valid-v-slot': 'error',
      'vue/array-bracket-spacing': 'error',
      'vue/arrow-spacing': 'error',
      'vue/block-spacing': 'error',
      'vue/brace-style': 'error',
      'vue/camelcase': 'error',
      'vue/comma-dangle': ['error', 'always-multiline'],
      'vue/component-name-in-template-casing': 'error',
      'vue/dot-location': ['error', 'property'],
      'vue/eqeqeq': 'error',
      'vue/key-spacing': 'error',
      'vue/keyword-spacing': 'error',
      'vue/no-deprecated-scope-attribute': 'error',
      'vue/no-empty-pattern': 'error',
      'vue/object-curly-spacing': ['error', 'always'],
      'vue/padding-line-between-blocks': 'error',
      'vue/space-infix-ops': 'error',
      'vue/space-unary-ops': 'error',
      'vue/block-lang': ['error',
         {
            script: {
               lang: 'ts'
            }
         }
      ],

   }
};
