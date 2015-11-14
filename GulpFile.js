'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var staticPath = 'public/static/';

gulp.task('sass', function() {
    gulp.src(staticPath + 'scss/**/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest(staticPath + 'css/'))
});

gulp.task('default', ['sass']);

gulp.task('watch',function() {
    gulp.watch(staticPath + 'scss/**/*.scss',['sass']);
});