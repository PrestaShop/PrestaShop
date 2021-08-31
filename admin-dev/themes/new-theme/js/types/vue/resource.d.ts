import {Store} from 'vuex';
import {Http} from 'vue-resource/types/vue_resource';

declare module 'vue/types/vue' {
  interface VueConstructor {
    resource: any;
    http: Http;
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
