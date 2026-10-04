/**
 * Url of an uploaded image for the admin. The ImageController keeps these
 * three fixed renditions for the admin (see 05-image-pipeline.md, "Admin");
 * the public site uses signed URLs.
 *
 * template: 'thumbnail' | 'large' | 'original'
 */
export function imageUrl(image, template = 'large') {
  const name = typeof image === 'string' ? image : image.name;
  return `/img/${template}/${name}`;
}

/**
 * Resolves once the browser has loaded the image at url.
 */
export function preloadImage(url) {
  return new Promise((resolve, reject) => {
    const img = new Image();
    img.onload = () => resolve(url);
    img.onerror = reject;
    img.src = url;
  });
}
