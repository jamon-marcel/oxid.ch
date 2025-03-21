export default {

  methods: {
    // Change tabs
    changeTab(tab) {
      // set all tabs inactive and remove errors if any
      for (let prop in this.tabs) {
        this.tabs[prop].active = false;
        this.tabs[prop].error = false;
      }

      // set active tab
      this.tabs[tab].active = true;
    },

    // Show the validation errors
    validationError() {
      this.$notify({
        type: 'error',
        text: 'Bitte markierte Felder prüfen!'
      });
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    },

    // Remove error per field
    removeError(field, language) {
      if (language) {
        this.errors[field][language] = false;
      } 
      else {
        this.errors[field] = false;
      }
    },

    // Remove all errors at once
    removeErrors() {
      this.$el.querySelectorAll('.has-error').forEach(el => {
        el.classList.remove('has-error');
      });
    },

    getSource(asset, size) {
      return `/img/${size}/${asset}`;
    }
  }
};
