'use strict';

import gulp from 'gulp';
import path from 'path';
import runSequence from 'run-sequence';
import {copyTasks} from './build/copy';
import concat from 'gulp-concat';
import postcss from 'gulp-postcss';
import cssnano from 'cssnano';
import urlRebase from 'postcss-url'

const config = {
    mdlPath : path.resolve('./node_modules/material-design-lite/') + '/',
    srcPath : path.resolve('./src/') + '/',
    stylesPath: path.resolve('./src/styles/') + '/',
    livescoreComponentsPath: path.resolve('./src/styles/livescore/') + '/',
    dist: path.resolve('./dist')
};

require('./build/build')(gulp, config, function (path) { return config.mdlPath + path; });

gulp.task('final_css', () => {
    return gulp.src([
        config.dist + '/assets/fonts/lsicons/style.css',
        config.dist + '/css/material.min.css'
    ])
        .pipe(concat('style.css'))
        .pipe(postcss([
            urlRebase([
                {filter: '**/fonts/*.eot', url: 'rebase'},
                {filter: '**/fonts/*.ttf', url: 'rebase'},
                {filter: '**/fonts/*.woff', url: 'rebase'},
                {filter: '**/fonts/*.svg', url: 'rebase'},
            ]),
            cssnano()
        ], {
            from: config.dist + "/assets/fonts/lsicons/style.css",
            to: config.dist + "/css/style.css"
        }))
        .pipe(gulp.dest('./dist/css'));
});


// Install copy tasks
copyTasks(gulp, config);

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

gulp.task('default', ['clean'], cb => {
    runSequence('styles', 'styles-grid', 'scripts', 'mocha', 'copy', 'twig', 'final_css', cb)
});