// Makes TS happy because it doesn't know about Vue SFC (not like webpack and the loader)
// so it will treat *.vue files as defineComponent
declare module '*.vue' {
  import {defineComponent} from 'vue';

  export default defineComponent;
}
