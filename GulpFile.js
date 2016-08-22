'use strict';

var gulp = require('gulp'),
    sass = require('gulp-sass'),
    uglify = require('gulp-uglify'),
    hash = require('gulp-hash'),
    concat = require('gulp-concat'),
    // babel = require('gulp-babel'),
    browserify = require('browserify'),
    source = require('vinyl-source-stream'),
    // gutil = require('gulp-util'),
    // eslint = require('gulp-eslint'),
    babelify = require('babelify'),
    // streamify = require('gulp-streamify'),
    staticPathSrc = 'public/static/src/',
    staticPathDist = 'public/static/dist/',
    manifestJsApp = 'assets-js-app.json',
    manifestJsAdmin = 'assets-js-admin.json',
    manifestJsVendor = 'assets-js-vendor.json',
    manifestCss = 'assets-css.json',
    manifestImages = 'assets-images.json',
    manifestPath = 'app/config/',
    // sourcemaps   = require('gulp-sourcemaps'),
    // transform = require('vinyl-transform'),
    scriptsCount = 0,
    dependencies = [
        'react',
        'react-dom'
    ];

var jsFiles = {
    vendor: [
        staticPathSrc + 'js/vendor/react.min.js',
        staticPathSrc + 'js/vendor/react-dom.min.js',
        staticPathSrc + 'js/vendor/react.js',
        staticPathSrc + 'js/vendor/react-dom.js',
    ],
    source: [
        staticPathSrc + 'js/admin/DataEditor/DataEditor.jsx',
        staticPathSrc + 'js/admin/DataEditor/Menu.jsx',
        staticPathSrc + 'js/admin/admin.jsx',
    ]
};

// Lint JS/JSX files
// gulp.task('eslint', function() {
//     return gulp.src(jsFiles.source)
//         .pipe(eslint({
//             baseConfig: {
//                 "ecmaFeatures": {
//                     "jsx": true
//                 }
//             }
//         }))
//         .pipe(eslint.format())
//         .pipe(eslint.failAfterError());
// });

// Copy assets/js/vendor/* to assets/js
// gulp.task('copy-js-vendor', function() {
//     return gulp
//         .src([
//             staticPathSrc + 'js/vendor/react.js',
//             staticPathSrc + 'js/vendor/react-dom.js'
//         ])
//         .pipe(gulp.dest(staticPathDist));
// });

// gulp.task('concat', ['eslint'], function() {
//     return gulp.src(jsFiles.vendor.concat(jsFiles.source))
//         .pipe(sourcemaps.init())
//         .pipe(babel({
//             only: [
//                 staticPathSrc + 'js/admin',
//             ],
//             compact: false
//         }))
//         .pipe(concat('admin.js'))
//         .pipe(sourcemaps.write('./'))
//         .pipe(gulp.dest(staticPathDist));
// });

// gulp.task('apply-prod-environment', function() {
//     process.stdout.write("Setting NODE_ENV to 'production'" + "\n");
//     process.env.NODE_ENV = 'production';
//     if (process.env.NODE_ENV != 'production') {
//         throw new Error("Failed to set NODE_ENV to production!!!!");
//     } else {
//         process.stdout.write("Successfully set NODE_ENV to production" + "\n");
//     }
// });

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

// gulp.task('js-admin', function() {
//     bundleApp('admin', true);
// });

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
    return browserify({
        entries: staticPathSrc + 'js/admin/admin.jsx',
        extensions: ['.jsx'],
        debug: true
    })
        .transform('babelify', {presets: ['es2015', 'react']})
        .bundle()
        .pipe(source('admin.js'))
        .pipe(gulp.dest(staticPathDist));


    // var browserified = transform(function(filename) {
    //     var b = browserify(filename, {
    //         extensions: ['.jsx'],
    //         debug : false
    //     });
    //     return b.bundle();
    // });
    //
    // return gulp.src([staticPathSrc + 'js/admin/admin.jsx'])
    //     .pipe(browserified)
    //     .transform("babelify", {presets: ["es2015", "react"]})
    //     // .pipe(uglify())
    //     .pipe(gulp.dest(staticPathDist + 'admin.js'));
    //
    // // gulp.src()
    // //     .pipe(browserify({
    // //         extensions: ['.jsx'],
    // //         debug : false
    // //     }))
    // //     .transform("babelify", {presets: ["es2015", "react"]})
    // //     .pipe(gulp.dest());
    //
    //
    // // return browserify({entries: staticPathSrc + 'js/admin/admin.jsx', extensions: ['.jsx'], debug: true})
    //     .transform('babelify', {presets: ['es2015', 'react']})
    //     .bundle()
    //     .pipe(source('admin.js'))
    //     .pipe(gulp.dest(staticPathDist));
});

// gulp.task('default', ['apply-prod-environment', 'sass', 'js-admin', 'js-app', 'img']);
gulp.task('default', ['sass', 'vendor', 'admin-js', 'js-app', 'img']);

gulp.task('watch',function() {
    gulp.watch(staticPathSrc + 'scss/**/*.scss',['sass']);
    gulp.watch(staticPathSrc + 'js/admin/**/*.js',['concat']);
    gulp.watch(staticPathSrc + 'js/admin/**/*.jsx',['admin-js']);
    gulp.watch(staticPathSrc + 'js/app/**/*.js',['js-app']);
});

// Private Functions
// ----------------------------------------------------------------------------
// function bundleApp(bootstrapFile, isProduction) {
//     scriptsCount++;
//     // Browserify will bundle all our js files together in to one and will let
//     // us use modules in the front end.
//     var appBundler = browserify({
//         entries: staticPathSrc + 'js/admin/' + bootstrapFile + '.js',
//         debug: true
//     });
//
//     // If it's not for production, a separate vendors.js file will be created
//     // the first time gulp is run so that we don't have to rebundle things like
//     // react everytime there's a change in the js file
//     // If it's not for production, a separate vendors.js file will be created
//     // the first time gulp is run so that we don't have to rebundle things like
//     // react everytime there's a change in the js file
//     if (!isProduction && scriptsCount === 1){
//         // create vendors.js for dev environment.
//         browserify({
//             require: dependencies,
//             debug: true
//         })
//             .bundle()
//             .on('error', gutil.log)
//             .pipe(source('vendors.js'))
//             .pipe(hash())
//             .pipe(gulp.dest(staticPathDist))
//             .pipe(hash.manifest(manifestJsVendor))
//             .pipe(gulp.dest(manifestPath));
//     }
//     if (!isProduction){
//         // make the dependencies external so they dont get bundled by the
//         // app bundler. Dependencies are already bundled in vendor.js for
//         // development environments.
//         dependencies.forEach(function(dep){
//             appBundler.external(dep);
//         })
//     }
//
//     appBundler
//     // transform ES6 and JSX to ES5 with babelify
//         .transform("babelify", {presets: ["es2015", "react"]})
//         .bundle()
//         .on('error',gutil.log)
//         .pipe(source(bootstrapFile + '.js'))
//         // .pipe(streamify(uglify()))
//         // .pipe(hash())
//         .pipe(gulp.dest(staticPathDist));
//         // .pipe(hash.manifest(manifestJsAdmin))
//         // .pipe(gulp.dest(manifestPath));
// }