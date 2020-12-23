Nova.booting((Vue, router, store) => {
  Vue.component('index-video-upload', require('./components/IndexField'))
  Vue.component('detail-video-upload', require('./components/DetailField'))
  Vue.component('form-video-upload', require('./components/FormField'))
})
