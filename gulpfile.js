/*
    Usage :
    $ gulp
    $ gulp sass
    $ gulp js
    $ gulp css
 */

var gulp = require('gulp');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var minifyCSS = require('gulp-minify-css');

gulp.task('default', ['vue']);

// Using gulp-sass
gulp.task('sass', function () {
    return gulp.src('resources/scss/**/*.scss')
        .pipe(sass())
        .pipe(gulp.dest('public/css'))
});

// Using gulp-uglify
gulp.task('js', function () {
    return gulp.src(['resources/js/**/*.js', 'resources/js/*.js'])
        .pipe(concat('bundle.js'))
        .pipe(uglify())
        .pipe(rename({
            basename: 'bundle',
            suffix: '.min',
        }))
        .pipe(gulp.dest('public/js'))
});

// Using gulp-minify-css
gulp.task('css', function () {
    return gulp.src('resources/css/**/*.css')
        .pipe(minifyCSS())
        .pipe(concat('styles.css'))
        .pipe(rename({
            basename: 'styles',
            suffix: '.min',
        }))
        .pipe(gulp.dest('public/css'))
});

// Watchers
gulp.task('watch', function () {
    gulp.watch('resources/scss/**/*.scss', ['sass']);
    gulp.watch('resources/js/**/*.js', ['js']);
    gulp.watch('resources/css/**/*.css', ['css']);
});

