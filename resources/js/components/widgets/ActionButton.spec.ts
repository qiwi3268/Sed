import { mount } from '@vue/test-utils';
import ActionButton from '@/components/widgets/ActionButton.vue';
import Ripple from 'primevue/ripple';
import ProgressSpinner from 'primevue/progressspinner';

describe('action button component', () => {
   let wrapper;
   const buildWrapper = (options = {}) => {
      wrapper = mount(
         ActionButton,
         Object.assign({
            global: {
               directives: {
                  ripple: Ripple
               }
            }
         }, options)
      );
   };
   const findLoadingSpinner = () => wrapper.findComponent(ProgressSpinner);

   afterEach(() => {
      wrapper.unmount();
      wrapper = null;
   });

   it('Рендер компонента', () => {
      buildWrapper({
         props: {
            label: 'Label'
         }
      });
      expect(wrapper.text()).toContain('Label');
      expect(findLoadingSpinner().exists()).toBe(false);
   });

   it('Отображение компонента в момент загрузки ', () => {
      buildWrapper({
         props: {
            label: 'Label',
            loading: true
         }
      });
      expect(wrapper.text()).not.toContain('Label');
      expect(findLoadingSpinner().exists()).toBe(true);
   });
});
