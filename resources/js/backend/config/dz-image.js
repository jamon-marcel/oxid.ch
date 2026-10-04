export default {
  url: "/api/media/upload",
  method: 'post',
  maxFilesize: 8,
  maxFiles: 1,
  createImageThumbnails: false,
  acceptedFiles: '.png, .jpg',
  headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
  }
}