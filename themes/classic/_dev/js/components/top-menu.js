import $ from 'jquery';
import DropDown from './drop-down';

export default class TopMenu extends DropDown {
  init() {
    this.el.hover(()=>{
      this.el.trigger('show.bs.dropdown');
    });

    $('.header-top').mouseleave((e)=>{
      if(!$(e.currentTarget).hasClass('.header-top') || !$(e.currentTarget).hasClass('.row')){
        this.el.trigger('hide.bs.dropdown');
      }
    });
    this.el.on('click',(e)=>{
      e.stopPropagation();
    });
    
    super.init();
  }
}
