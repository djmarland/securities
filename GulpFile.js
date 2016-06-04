'use strict';

var gulp = require('gulp'),
    sass = require('gulp-sass'),
    uglify = require('gulp-uglify'),
    hash = require('gulp-hash'),
    concat = require('gulp-concat'),
    browserify = require('browserify'),
    source = require('vinyl-source-stream'),
    gutil = require('gulp-util'),
    babelify = require('babelify'),
    streamify = require('gulp-streamify'),
    staticPathSrc = 'public/static/src/',
    staticPathDist = 'public/static/dist/',
    manifestFile = 'assets.json',
    manifestPath = 'app/config/',
    scriptsCount = 0,
    dependencies = [
        'react',
        'react-dom'
    ];

gulp.task('apply-prod-environment', function() {
    process.stdout.write("Setting NODE_ENV to 'production'" + "\n");
    process.env.NODE_ENV = 'production';
    if (process.env.NODE_ENV != 'production') {
        throw new Error("Failed to set NODE_ENV to production!!!!");
    } else {
        process.stdout.write("Successfully set NODE_ENV to production" + "\n");
    }
});

gulp.task('sass', function() {
    gulp.src(staticPathSrc + 'scss/**/*.scss')
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(hash())
        .pipe(gulp.dest(staticPathDist))
        .pipe(hash.manifest(manifestFile))
        .pipe(gulp.dest(manifestPath));
});

gulp.task('js-app', function() {
    gulp.src([
        staticPathSrc + 'js/vendor/stickyfill.js',
        staticPathSrc + 'js/bootstrap.js'
    ])
        .pipe(concat('app.js'))
        .pipe(uglify())
        .pipe(hash())
        .pipe(gulp.dest(staticPathDist))
        .pipe(hash.manifest(manifestFile))
        .pipe(gulp.dest(manifestPath));
});

gulp.task('js-admin', function() {
    bundleApp('admin', true);
});

gulp.task('img', function() {
    gulp.src(staticPathSrc + 'img/**/*.*')
        .pipe(hash())
        .pipe(gulp.dest(staticPathDist))
        .pipe(hash.manifest(manifestFile))
        .pipe(gulp.dest(manifestPath));
});

gulp.task('default', ['apply-prod-environment', 'sass', 'js-app', 'js-admin', 'img']);

gulp.task('watch',function() {
    gulp.watch(staticPathSrc + 'scss/**/*.scss',['default']);
    gulp.watch(staticPathSrc + 'js/**/*.js',['default']);
});

// Private Functions
// ----------------------------------------------------------------------------
function bundleApp(bootstrapFile, isProduction) {
    scriptsCount++;
    // Browserify will bundle all our js files together in to one and will let
    // us use modules in the front end.
    var appBundler = browserify({
        entries: staticPathSrc + 'js/' + bootstrapFile + '.js',
        debug: true
    });

    // If it's not for production, a separate vendors.js file will be created
    // the first time gulp is run so that we don't have to rebundle things like
    // react everytime there's a change in the js file
    // If it's not for production, a separate vendors.js file will be created
    // the first time gulp is run so that we don't have to rebundle things like
    // react everytime there's a change in the js file
    if (!isProduction && scriptsCount === 1){
        // create vendors.js for dev environment.
        browserify({
            require: dependencies,
            debug: true
        })
            .bundle()
            .on('error', gutil.log)
            .pipe(source('vendors.js'))
            .pipe(hash())
            .pipe(gulp.dest(staticPathDist))
            .pipe(hash.manifest(manifestFile))
            .pipe(gulp.dest(manifestPath));
    }
    if (!isProduction){
        // make the dependencies external so they dont get bundled by the
        // app bundler. Dependencies are already bundled in vendor.js for
        // development environments.
        dependencies.forEach(function(dep){
            appBundler.external(dep);
        })
    }

    appBundler
    // transform ES6 and JSX to ES5 with babelify
        .transform("babelify", {presets: ["es2015", "react"]})
        .bundle()
        .on('error',gutil.log)
        .pipe(source(bootstrapFile + '.js'))
        // .pipe(streamify(uglify()))
        // .pipe(hash())
        .pipe(gulp.dest(staticPathDist))
        // .pipe(hash.manifest(manifestFile))
        // .pipe(gulp.dest(manifestPath));
}