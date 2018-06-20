/* eslint-env node */

const path         = require( 'path' );
const buffer       = require( 'vinyl-buffer' );
const sourceStream = require( 'vinyl-source-stream' );
const mergeStream  = require( 'merge-stream' );
const browserify   = require( 'browserify' );
const babelify     = require( 'babelify' );

const gulp         = require( 'gulp' );
const sass         = require( 'gulp-sass' );
const autoprefixer = require( 'gulp-autoprefixer' );
const sourcemaps   = require( 'gulp-sourcemaps' );
const eslint       = require( 'gulp-eslint' );
const uglify       = require( 'gulp-uglifyes' );

const sassFiles = [
  'admin.scss',
  'subscription-form-editor.scss',
  'subscription-form.scss'
];

const ecmaScriptFiles = [
  'admin.es',
  'subscription-form-editor.es',
  'subscription-form.es'
];

/**
 * ---------------------------------------------------------------
 * Script tasks
 *
 * Define configurations and tasks to handle linting, transpiling,
 * bundling, and minifying of our JavaScript files.
 * ---------------------------------------------------------------
 */

gulp.task( 'scripts', () => {

  return mergeStream( ecmaScriptFiles.map( ( file ) => {

    let fileName = path.basename( file, '.es' );

    return browserify({
        'entries': `./assets/js/${file}`,
        'debug': true,
        'transform': [ babelify ]
    })
    .bundle()
      .on( 'error', function( err ) {
        console.error( err );
        this.emit( 'end' );
      })
    .pipe( sourceStream( `${fileName}.js` ) )
    .pipe( buffer() )
      .pipe( sourcemaps.init({'loadMaps': true}) )
      .pipe( sourcemaps.write( './' ) )
    .pipe( gulp.dest( './assets/js' ) );
  }) );
});

gulp.task('eslint', () => {
  return gulp.src('./assets/js/**/*.es')
    .pipe( eslint() )
    .pipe( eslint.format() )
});

/**
 * ---------------------------------------------------------------
 * Style tasks
 *
 * Define configurations and tasks to convert SCSS to plain CSS
 * and minify the output.
 * ---------------------------------------------------------------
 */

/**
 * Task to compile the SCSS files to CSS.
 *
 * We will loop through each files and put the compiled CSS to the
 * destination directory as listed in `styleSources`
 */
gulp.task( 'styles', () => {

  const autoprefixerConfig = {
    browsers: [ 'last 3 versions' ],
    cascade: false
  };

  return mergeStream( sassFiles.map( ( file ) => {
    return gulp.src( `./assets/css/${file}` )
      .pipe( sass().on( 'error', sass.logError ) )
        .pipe( sourcemaps.init() )
        .pipe( autoprefixer( autoprefixerConfig ) )
        .pipe( sourcemaps.write( '.' ) )
      .pipe( gulp.dest( './assets/css' ) );
  } ) );
});

/**
 * ---------------------------------------------------------------
 * Default tasks
 *
 * Define default task that can be called by just running `gulp`
 * from cli
 * ---------------------------------------------------------------
 */

gulp.task( 'default', [ 'eslint', 'scripts', 'styles' ], () => {
  gulp.watch([ '**/*.es' ], [ 'eslint', 'scripts' ]);
  gulp.watch([ '**/*.scss' ], [ 'styles' ]);
});
