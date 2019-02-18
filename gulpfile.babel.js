'use strict';

import gulp from 'gulp';
import path from 'path';
// const npmInstall = require('gulp-dependency-install');
// import yarn from 'gulp-yarn';
// import glob from 'glob';

// const copy = require('gulp-copy');

// require("./build/clean");


const config = {
    'mdlPath' : path.resolve('./node_modules/material-design-lite/') + '/',
    'srcPath' : path.resolve('./src/') + '/',
    livescoreComponentsPath: path.resolve('./src/components/livescore/') + '/',

    dist: path.resolve('./dist')
};

console.log(config.mdlPath);

gulp.task('clean', function () {
    return gulp.src([
        'external/',
        'dist/css',
        'dist/fonts',
        'dist/html',
        'dist/images'
    ], {read: false, allowEmpty: true})
        .pipe(clean());
});


// require('./build/mdl-download')(gulp, config);
require('./build/build')(gulp, config, function (path) { return config.mdlPath + path; });

// gulp.task('build', ['clean']);

gulp.task('default', ['styles']);