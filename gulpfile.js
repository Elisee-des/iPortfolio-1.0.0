const gulp = require('gulp');
const cleanCSS = require('gulp-clean-css');
const uglify = require('gulp-uglify');
const rename = require('gulp-rename');

// Minify CSS
gulp.task('minify-css', function() {
  return gulp.src('assets/css/*.css')
    .pipe(cleanCSS())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest('assets/css/'));
});

// Minify JS
gulp.task('minify-js', function() {
  return gulp.src('assets/js/*.js')
    .pipe(uglify())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest('assets/js/'));
});

// Default task
gulp.task('default', gulp.parallel('minify-css', 'minify-js'));