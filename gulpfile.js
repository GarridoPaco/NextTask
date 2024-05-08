const { src, dest, watch, series, parallel } = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const autoprefixer = require('autoprefixer');
const postcss = require('gulp-postcss');
const sourcemaps = require('gulp-sourcemaps');
const cssnano = require('cssnano');
const terser = require('gulp-terser-js');
const rename = require('gulp-rename');
const imagemin = require('gulp-imagemin');
const notify = require('gulp-notify');
const cache = require('gulp-cache');
const clean = require('gulp-clean');
const webp = require('gulp-webp');
const imageResize = require('gulp-image-resize'); // Agregado

const paths = {
    scss: 'src/scss/**/*.scss',
    js: 'src/js/**/*.js',
    imagenes: 'src/img/**/*'
}
function css() {
    return src(paths.scss)
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(postcss([autoprefixer(), cssnano()]))
        .pipe(sourcemaps.write('.'))
        .pipe(dest('public/build/css'));
}
function javascript() {
    return src(paths.js)
        .pipe(terser())
        .pipe(sourcemaps.write('.'))
        .pipe(dest('public/build/js'));
}

function resizeImages() {
    return src(paths.imagenes)
        .pipe(imageResize({
            width: 100,
            height: 100,
            crop: true,
            upscale: false
        }))
        .pipe(dest('public/build/img'))
        .pipe(notify({ message: 'Imagenes redimensionadas a 100x100' }));
}

function imagenes() {
    return src(paths.imagenes)
        .pipe(cache(imagemin({ optimizationLevel: 3 })))
        .pipe(dest('public/build/img'))
        .pipe(notify({ message: 'Imagen Completada en jpg' }));
}

function versionWebp() {
    return src(paths.imagenes)
        .pipe(webp())
        .pipe(dest('public/build/img'))
        .pipe(notify({ message: 'Imagen Completada en webp' }));
}

// function watchArchivos() {
//     watch(paths.scss, css);
//     watch(paths.js, javascript);
//     watch(paths.imagenes, versionWebp);
//     watch(paths.imagenes, resizeImages);
// }
function watchArchivos() {
    watch(paths.scss, css);
    watch(paths.js, javascript);
}

exports.css = css;
exports.watchArchivos = watchArchivos;
// exports.default = parallel(css, javascript, resizeImages, versionWebp, watchArchivos);
exports.default = parallel(css, javascript, watchArchivos);
// exports.build = parallel(css, javascript, resizeImages, versionWebp);
exports.build = parallel(css, javascript);