import git from 'gulp-git';
import yarn from 'gulp-yarn';

module.exports = function (gulp, config) {

    // Material Design Lite - related
    // ========================
    gulp.task('mdl-clone', function () {
        return git.clone(
            'https://github.com/google/material-design-lite.git',
            {args: '-b v1.1.3 ' + config.mdlPath },
            function (err) {
                if (err) {
                    throw err;
                }
            });
    });


    gulp.task('mdl-install', function () {
        return gulp.src([config.mdlPath +'package.json'])
            .pipe(yarn());
    });

    gulp.task('mdl-init', ['mdl-clone', 'mdl-install']);






    // --------------

// Inject our styles in MDL
// ========================
    gulp.task('mdl-copy-assets', function () {
        return gulp.src('./src/assets/*')
            .pipe(gulp.dest(config.mdlPath +'docs/_assets'));
    });

    gulp.task('mdl-copy-components', function () {
        // return gulp.src('./src/components/livescore/*')
        //     .pipe(gulp.dest(config.mdlPath + 'src/'));
    });

    gulp.task('mdl-inject-livescore', function () {
        var cssToAdd = "\n // Livescore\n";
        glob(config.livescoreComponentsPath + '**/*.scss', (err, files) => {
            if (err) {
                console.error(err);
                return;
            }

            files.forEach( (file) => {
                cssToAdd+= '@import "./../../.' + file + '";' + "\n";
            });

            const fs = require('fs');
            try {
                fs.appendFileSync(config.mdlPath + 'src/material-design-lite.scss', cssToAdd);
            } catch(err) {
                console.error('Could not append styles');
            }
        });
    });


    gulp.task('mdl-inject-livescore-mdl', function () {
        var cssToAdd = "\n // Livescore\n";
        glob(config.livescoreComponentsPath + '**/*.scss', (err, files) => {
            if (err) {
                console.error(err);
                return;
            }

            files.forEach( (file) => {
                cssToAdd+= '@import "./../../.' + file + '";' + "\n";
            });

            const fs = require('fs');
            try {
                fs.appendFileSync(config.mdlPath + 'src/material-design-lite.scss', cssToAdd);
            } catch(err) {
                console.error('Could not append styles');
            }
        });
    });

    gulp.task('mdl-copy', ['mdl-copy-assets', 'mdl-copy-components', 'mdl-inject-livescore']);
};