'use strict';

import gulp from 'gulp';
import path from 'path';
import runSequence from 'run-sequence';

const config = {
    'mdlPath' : path.resolve('./node_modules/material-design-lite/') + '/',
    'srcPath' : path.resolve('./src/') + '/',
    livescoreComponentsPath: path.resolve('./src/components/livescore/') + '/',
    dist: path.resolve('./dist')
};

gulp.task('copy-ls-scripts', () => {
    return gulp.src([config.srcPath + '/assets/js/app.js'])
        .pipe(gulp.dest(config.dist + '/js'));
});

gulp.task('copy-ls-datepicker', () => {
    return gulp.src(config.srcPath + '/assets/js/material-datepicker/**/*')
        .pipe(gulp.dest(config.dist + '/js/material-datepicker'));
});

gulp.task('copy-assets', () => {
    return gulp.src([
        config.srcPath + '/assets/**/*',
        config.mdlPath + '/dist/assets/**/*'
    ])
        .pipe(gulp.dest(config.dist + '/assets'));
});

gulp.task('copy', ['copy-ls-scripts', 'copy-ls-datepicker', 'copy-assets']);

gulp.task('twig', () => {
    const { exec } = require('child_process');
    exec('php application.php compile', (err, stdout, stderr) => {
        if (err) {
            // node couldn't execute the command
            console.log('ERROR: ' + stderr);
            return;
        }
    });
});

require('./build/build')(gulp, config, function (path) { return config.mdlPath + path; });

gulp.task('default', ['clean'], cb => {
    runSequence(['styles', 'styles-grid', 'scripts', 'mocha', 'copy', 'twig'], cb)
});