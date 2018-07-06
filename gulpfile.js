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
const shell        = require( 'gulp-shell' );

const sassFiles = [
  'admin.scss',
  'subscription-form-editor.scss',
  'subscription-form.scss'
];

const ecmaScriptFiles = [
  'admin.js',
  'subscription-form-editor.js',
  'subscription-form.js'
];

/**
 * ---------------------------------------------------------------
 * Script tasks
 *
 * Defining configurations and tasks to handle linting, transpiling,
 * bundling, and minifying of our JavaScript files.
 * ---------------------------------------------------------------
 */

gulp.task( 'scripts', () => {

  return mergeStream( ecmaScriptFiles.map( ( file ) => {

    let fileName = path.basename( file, '.js' );

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
    .pipe( sourceStream( `${fileName}.min.js` ) )
    .pipe( buffer() )
    .pipe( sourcemaps.init({'loadMaps': true}) )
      .pipe( uglify() )
    .pipe( sourcemaps.write( './' ) )
    .pipe( gulp.dest( './assets/js' ) );
  }) );
});

// Lint JavaScript files with ESLint
gulp.task( 'eslint', () => {
  return gulp.src( './assets/js/**/*' )
    .pipe( eslint() )
    .pipe( eslint.format() );
});

/**
 * ---------------------------------------------------------------
 * Style tasks
 *
 * Defining configurations and tasks to convert SCSS to plain CSS
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
      .pipe( sass({ outputStyle: 'compressed' }).on( 'error', sass.logError ) )
        .pipe( sourcemaps.init() )
          .pipe( autoprefixer( autoprefixerConfig ) )
        .pipe( sourcemaps.write( '.' ) )
      .pipe( gulp.dest( './assets/css' ) );
  }) );
});

/**
 * ---------------------------------------------------------------
 * Build tasks
 *
 * Defining tasks to build the plugin into a distributeable
 * plugin.
 * ---------------------------------------------------------------
 */

// Watch changes
gulp.task( 'watch', () => {
  gulp.watch( '**/*', gulp.series( 'eslint', 'scripts' ) );
  gulp.watch( '**/*.scss', gulp.series( 'styles' ) );
});

// Tasks associated with Composer
gulp.task( 'composer:no-dev', shell.task( 'composer install --no-dev' ) );
gulp.task( 'composer', shell.task( 'composer install' ) );

// Define distributable files to copy.
gulp.task( 'copy', () => {

  return gulp.src([
    '**/*',
    '!**/*.json',
    '!**/*.html',
    '!**/*.lock',
    '!**/*.yml',
    '!**/*.xml.dist',
    '!**/*.md',
    '!**/*.zip',
    '!**/*.map',
    '!bin/**',
    '!vendor/bin/**',
    '!svn-assets/**',
    '!tests/**',
    '!node_modules/**',
    '!gulpfile.js',
    '!LICENSE'
  ], {
    dot: false
  })
  .pipe( gulp.dest( './dist' ) );
});

// Compile JavaScript and CSS.
gulp.task( 'build', gulp.series( 'eslint', 'scripts', 'styles' ) );

// Copy the files into a ./dist directory.
gulp.task( 'dist', gulp.series( 'build', 'composer:no-dev', 'copy', 'composer' ) );

/**
 * ---------------------------------------------------------------
 * Default tasks
 *
 * Defining default task that can be called by just running `gulp`
 * from cli
 * ---------------------------------------------------------------
 */

gulp.task( 'default', gulp.parallel( 'eslint', 'scripts', 'styles', 'watch' ) );
