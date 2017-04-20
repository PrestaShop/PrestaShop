export default {
  methods: {
    trans(key) {
      return this.$store.getters.translations[key];
    }
  }
};
