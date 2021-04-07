// assets/js/app.js
import Vue from 'vue';
import '../css/app.css';
//import {Button,Row,Dialog} from '../element-ui/index.js';
import {Button,Row,Dialog,Tree,Container,Aside,Main,Col,Radio,RadioButton,RadioGroup,Loading} from 'element-ui';
//require('element-ui-style.css');
import Example from './components/Example.vue';
import DocumentTreeView from './components/DocumentTreeView.vue';

import axios from 'axios';

import lang from 'element-ui/lib/locale/lang/ru-RU';
import locale from 'element-ui/lib/locale';

//import ElementUI from '../element-ui/index.js';
//import '../element-ui/theme-chalk/index.css';

//import '../css/element-variables.scss';

locale.use(lang);

// !!!!!!!!! http://element.eleme.io/#/en-US/component/quickstart

Vue.component(Row.name, Row);
Vue.component(Button.name, Button);
Vue.component(Dialog.name, Dialog);
Vue.component(Tree.name, Tree);
Vue.component(Container.name, Container);
Vue.component(Aside.name, Aside);
Vue.component(Main.name, Main);
Vue.component(Col.name, Col);
Vue.component(Radio.name,Radio);
Vue.component(RadioButton.name,RadioButton);
Vue.component(RadioGroup.name,RadioGroup);

Vue.use(Loading.directive);
Vue.prototype.$loading = Loading.service;

//Vue.directive(Loading.directive,Loading);
//Vue.component(Loading.directive,Loading);

/**
* Create a fresh Vue Application instance
*/
new Vue({
  el: '#app',
  components: {Example,DocumentTreeView}
});