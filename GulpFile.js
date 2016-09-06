'use strict';

var gulp = require('gulp'),
    sass = require('gulp-sass'),
    uglify = require('gulp-uglify'),
    hash = require('gulp-hash'),
    concat = require('gulp-concat'),
    browserify = require('browserify'),
    source = require('vinyl-source-stream'),
    staticPathSrc = 'public/static/src/',
    staticPathDist = 'public/static/dist/',
    manifestJsApp = 'assets-js-app.json',
    manifestCss = 'assets-css.json',
    manifestImages = 'assets-images.json',
    manifestPath = 'app/config/';

var jsFiles = {
    vendor: [
        // staticPathSrc + 'js/vendor/react.min.js',
        // staticPathSrc + 'js/vendor/react-dom.min.js',
        staticPathSrc + 'js/vendor/react.js',
        staticPathSrc + 'js/vendor/react-dom.js',
    ],
    source: [
        staticPathSrc + 'js/admin/DataEditor/DataEditor.jsx',
        staticPathSrc + 'js/admin/DataEditor/Menu.jsx',
        staticPathSrc + 'js/admin/admin.jsx',
    ]
};

gulp.task('sass', function() {
    gulp.src(staticPathSrc + 'scss/**/*.scss')
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(hash())
        .pipe(gulp.dest(staticPathDist))
        .pipe(hash.manifest(manifestCss))
        .pipe(gulp.dest(manifestPath));
});

gulp.task('js-app', function() {
    gulp.src([
        staticPathSrc + 'js/vendor/stickyfill.js',
        staticPathSrc + 'js/app/app.js'
    ])
        .pipe(concat('app.js'))
        .pipe(uglify())
        .pipe(hash())
        .pipe(gulp.dest(staticPathDist))
        .pipe(hash.manifest(manifestJsApp))
        .pipe(gulp.dest(manifestPath));
});

gulp.task('img', function() {
    gulp.src(staticPathSrc + 'img/**/*.*')
        .pipe(hash())
        .pipe(gulp.dest(staticPathDist))
        .pipe(hash.manifest(manifestImages))
        .pipe(gulp.dest(manifestPath));
});

gulp.task('vendor', function() {
    gulp.src(jsFiles.vendor)
        .pipe(concat('vendor.js'))
        .pipe(gulp.dest(staticPathDist));
});

gulp.task('admin-js', function () {
    var appBundler = browserify({
        entries: staticPathSrc + 'js/admin/admin.jsx',
        extensions: ['.jsx'],
        debug: false
    })
        .transform('babelify', {presets: ['es2015', 'react']})
        .require('./' + staticPathSrc + 'js/import-react.js', {expose: 'react'})
        .require('./' + staticPathSrc + 'js/import-react-dom.js', {expose: 'react-dom'});

    return appBundler.bundle()
        .pipe(source('admin.js'))
        .pipe(gulp.dest(staticPathDist));
});

gulp.task('default', ['sass', 'vendor', 'admin-js', 'js-app', 'img']);

gulp.task('watch',function() {
    gulp.watch(staticPathSrc + 'scss/**/*.scss',['sass']);
    gulp.watch(staticPathSrc + 'js/admin/**/*.js',['concat']);
    gulp.watch(staticPathSrc + 'js/admin/**/*.jsx',['admin-js']);
    gulp.watch(staticPathSrc + 'js/app/**/*.js',['js-app']);
});
