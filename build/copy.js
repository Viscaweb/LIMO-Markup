
function copyTasks(gulp, config) {

    gulp.task('copy-ls-scripts', () => {
        return gulp.src([config.srcPath + '/assets/js/app.js'])
            .pipe(gulp.dest(config.dist + '/js'));
    });

    /*
     * Deprecated
     */
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
}


export {copyTasks};