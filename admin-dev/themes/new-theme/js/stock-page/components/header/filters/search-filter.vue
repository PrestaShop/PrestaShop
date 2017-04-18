<template>
  <div>
    <form @submit.prevent="onSubmit" @keyup="onKeyUp" @keyup.enter="onSubmit">
      <Tags
        ref="tags"
        class="form-control search search-input"
        :tags="tags"
        :placeholder="placeholder"
        @tagChange="onTagChanged"
      />
    </form>
  </div>
</template>

<script>
  import Tags from '../../utils/tags';

  export default {
    props: ['placeholder', 'match', 'label'],
    methods: {
      onTagChanged(tags, index, tag, splice) {
        if(splice) {
          this.tags.splice(index, 1);
        }
        this.$emit('tagChanged', this.match);
      },
      onKeyUp() {
        let children = this.$refs.tags.$refs.tags.$children;
        let text = children[children.length - 1].text;
        this.$emit('typing', text);
      },
      onSubmit(event) {
        if(this.match) {
          $(this.$el).find('.input').blur();
          this.$emit('submit', this.match[this.label]);
        }
        setTimeout(() => $(this.$el).find('.gap:last-of-type .input').focus() , 15);
      }
    },
    components: {
      Tags
    },
    data() {
      return {
        tags: []
      }
    }
  }
</script>
