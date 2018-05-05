var gulp = require('gulp');
var less = require('gulp-less');
var babel = require('gulp-babel');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var cleanCSS = require('gulp-clean-css');
var del = require('del');

var paths = {
    styles: {
        src: [
            // our styles
            'resources/assets/css/*.css',

            // compiled bootstrap scss
            'public/css/app.css'
        ],
        dest: 'public/assets/'
    },

    scripts: {
        src: [
            'public/js/app.js'
        ],
        dest: 'public/assets/'
    }
}

function clean() {
    // You can use multiple globbing patterns as you would with `gulp.src`,
    // for example if you are using del 2.0 or above, return its promise
    return del([ 'public/vendor' ]);
}

/*
 * Define our tasks using plain functions
 */
function styles() {
    return gulp.src(paths.styles.src)
        .pipe(less())
        .pipe(cleanCSS())
        .pipe(concat('mangapie.css'))
        .pipe(gulp.dest(paths.styles.dest));
}

function scripts() {
    return gulp.src(paths.scripts.src, { sourcemaps: true })
        // .pipe(babel())
        .pipe(uglify())
        .pipe(concat('mangapie.js'))
        .pipe(gulp.dest(paths.scripts.dest));
}

function watch() {
    gulp.watch(paths.scripts.src, scripts);
    gulp.watch(paths.styles.src, styles);
}

exports.clean = clean;
exports.styles = styles;
exports.scripts = scripts;
exports.watch = watch;

/*
 * You can still use `gulp.task` to expose tasks
 */
gulp.task('clean', clean);

gulp.task('styles', styles);

gulp.task('scripts', scripts);

/*
 * Define default task that can be called by just running `gulp` from cli
 */
gulp.task('default', ['clean', 'styles', 'scripts']);