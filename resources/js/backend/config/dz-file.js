export default {
  url: "/api/media/upload",
  method: 'post',
  maxFilesize: 8,
  maxFiles: 1,
  createImageThumbnails: false,
  acceptedFiles: '.pdf',
  headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
  }
}