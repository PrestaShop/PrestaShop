import {Store} from 'vuex';

declare module 'vue/types/vue' {
  interface VueConstructor {
    resource: any;
  }
  interface Vue {
    resource: any;
    trans: (text: string) => string;
  }
}

declare module '@vue/runtime-core' {
  // provide typings for `this.$store`
  interface ComponentCustomProperties {
    $store: Store<any>
  }
}
