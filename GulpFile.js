'use strict';

var gulp = require('gulp'),
    sass = require('gulp-sass'),
    uglify = require('gulp-uglify'),
    hash = require('gulp-hash'),
    staticPathSrc = 'public/static/src/',
    staticPathDist = 'public/static/dist/',
    manifestFile = 'assets.json',
    manifestPath = 'app/config/';

gulp.task('sass', function() {
    gulp.src(staticPathSrc + 'scss/**/*.scss')
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(hash())
        .pipe(gulp.dest(staticPathDist))
        .pipe(hash.manifest(manifestFile))
        .pipe(gulp.dest(manifestPath));
});

gulp.task('js', function() {
    gulp.src(staticPathSrc + 'js/**/*.js')
        .pipe(uglify())
        .pipe(hash())
        .pipe(gulp.dest(staticPathDist))
        .pipe(hash.manifest(manifestFile))
        .pipe(gulp.dest(manifestPath));
});

gulp.task('default', ['sass', 'js']);

gulp.task('watch',function() {
    gulp.watch(staticPathSrc + 'scss/**/*.scss',['sass']);
    gulp.watch(staticPathSrc + 'js/**/*.js',['js']);
});