<template>
  <span>
    <a v-show="multi" v-on:click="changePage($event, index)" class="page-link" :class="classObject">
      <span v-show="showFirstDots">...</span>
      {{ index }}
      <span v-show="showLastDots">...</span>
    </a>
  </span>
</template>

<script>
  export default {
    props: ['index','current','isMulti','total','pagesToDisplay'],
    computed: {
      showFirstDots() {
        if(this.isMulti && this.multi) {
          if(this.index === this.total && this.current <= this.total - this.pagesToDisplay) {
            this.hasRightDots = true;
            return true;
          }
          this.hasRightDots = false;
        }
      },
      showLastDots() {
        if(this.isMulti && this.multi) {
          if(this.index === 1 && this.current > this.pagesToDisplay) {
            this.hasLeftDots = true;
            return true;
          }
          this.hasLeftDots = false;
        }
      },
      multi() {
        if(this.isMulti) {
          if(this.index === 1 || this.index === this.current - 1) {
            return true;
          }
          if(this.current <= this.pagesToDisplay && (this.index <= this.pagesToDisplay || this.index === this.total)){
            return true;
          }
          if(this.current > this.pagesToDisplay) {
            if((this.index < this.current - 1 || this.index > this.current + 1) && this.index !== this.total) {
              if((this.current > this.total - this.pagesToDisplay) && (this.index > this.total - this.pagesToDisplay)) {
                return true;
              }
              return false;
            }
            return true;
          }
          return false;
        }
        return false;
      },
      classObject() {
        return {
          current: (this.current === this.index),
          'has-dots has-right-dots': this.hasRightDots,
          'has-dots has-left-dots': this.hasLeftDots
        }
      },
    },
    data() {
      return {
        hasRightDots: false,
        hasLeftDots: false
      }
    },
    methods: {
      changePage(event, pageIndex) {
        event.preventDefault();
        this.$emit('pageChanged', pageIndex);
      }
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .page-link, .page-item.active .page-link {
    background-color: transparent;
    text-decoration: none;
    &.current {
      color: $brand-primary;
    }
  }
  .has-dots {
    span {
      color: $gray-medium;
      display: inline-block;
    }
    &.has-right-dots {
      padding-left: 0;
      span {
        margin-right: 5px;
      }
    }
    &.has-left-dots {
      padding-right: 0;
      span {
        margin-left: 5px;
      }
    }
  }
</style>
