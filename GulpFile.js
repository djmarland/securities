'use strict';

var gulp = require('gulp'),
    sass = require('gulp-sass'),
    uglify = require('gulp-uglify'),
    staticPathSrc = 'public/static/src/',
    staticPathDist = 'public/static/dist/';

gulp.task('sass', function() {
    gulp.src(staticPathSrc + 'scss/**/*.scss')
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(gulp.dest(staticPathDist + 'css/'))
});

gulp.task('js', function() {
    gulp.src(staticPathSrc + 'js/**/*.js')
        .pipe(uglify())
        .pipe(gulp.dest(staticPathDist + 'js/'))
});

gulp.task('default', ['sass', 'js']);

gulp.task('watch',function() {
    gulp.watch(staticPathSrc + 'scss/**/*.scss',['sass']);
    gulp.watch(staticPathSrc + 'js/**/*.js',['js']);
});