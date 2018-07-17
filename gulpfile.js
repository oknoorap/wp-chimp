/* eslint-env node */
const argv         = require( 'yargs' ).argv;
const grunt        = require( 'grunt' );
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

const deployStatus = argv.status;
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

grunt.initConfig({

  pkg: grunt.file.readJSON( 'package.json' ),

  /* eslint-disable */
  wp_deploy: {

    // Deploys a new release to the WordPress SVN repo.
    release: {
      options: {
        plugin_slug: '<%= pkg.name %>',
        build_dir: 'dist',
        assets_dir: 'svn-assets'
      }
    },

    // Only commit the assets directory.
    assets: {
      options: {
        plugin_slug: '<%= pkg.name %>',
        build_dir: 'dist',
        assets_dir: 'svn-assets',
        deploy_trunk: false
      }
    },

    // Only deploy to trunk (e.g. when only updating the 'Tested up to' value and not deploying a release).
    trunk: {
      options: {
        plugin_slug: '<%= pkg.name %>',
        build_dir: 'dist',
        assets_dir: 'svn-assets',
        deploy_tag: false
      }
    }
  }
  /* eslint-disable */
});

grunt.loadNpmTasks('grunt-wp-deploy');

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
  gulp.watch([ 'assets/js/**/*.js', '!assets/js/**/*.min.js' ], gulp.series( 'eslint', 'scripts' ) );
  gulp.watch([ 'assets/css/*.scss' ], gulp.series( 'styles' ) );
});

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
    '!dist/**',
    '!bin/**',
    '!vendor/**',
    '!svn-assets/**',
    '!tests/**',
    '!pipelines/**',
    '!localhost/**',
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
gulp.task( 'dist', gulp.series( 'build', 'copy' ) );

/**
 * ---------------------------------------------------------------
 * Deploy tasks
 *
 * Defining the tasks to deploy the plugin to WordPress SVN
 * repository.
 * ---------------------------------------------------------------
 */

// Copy the files into a ./dist directory.
gulp.task( 'deploy', () => {
  grunt.tasks( [ 'wp_deploy:' + deployStatus ], {
    gruntfile: false
  } );
} );

/**
 * ---------------------------------------------------------------
 * Default tasks
 *
 * Defining default task that can be called by just running `gulp`
 * from cli
 * ---------------------------------------------------------------
 */

gulp.task( 'default', gulp.parallel( 'build', 'watch' ) );
