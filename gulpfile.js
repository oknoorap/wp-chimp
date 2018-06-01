/* eslint-env node */

const buffer       = require( 'vinyl-buffer' );
const sourceStream = require( 'vinyl-source-stream' );
const mergeStream  = require( 'merge-stream' );
const browserify   = require( 'browserify' );
const babelify     = require( 'babelify' );

const gulp         = require( 'gulp' );
const babel        = require( 'gulp-babel' );
const sass         = require( 'gulp-sass' );
const autoprefixer = require( 'gulp-autoprefixer' );
const sourcemaps   = require( 'gulp-sourcemaps' );
const uglify       = require( 'gulp-uglifyes' );

/**
 * ---------------------------------------------------------------
 * Script tasks
 *
 * Define configurations and tasks to handle transpiling,
 * bundling, and minifying of our JavaScript files.
 * ---------------------------------------------------------------
 */

/**
 * Compile and bundle the 'admin/script.es' which will be loaded
 * in the administration side of the plugin.
 */
gulp.task( 'script-admin', () => {

  let file = './admin/js/admin.es';

  return browserify({
    'entries': [ file ],
    'debug': true,
    'transform': [ babelify ]
  })
    .bundle()
    .on( 'error', function( err ) {
      console.error( err );
      this.emit( 'end' );
    })
    .pipe( sourceStream( 'admin.js' ) )
    .pipe( buffer() )
      .pipe( sourcemaps.init({'loadMaps': true}) )
      .pipe( sourcemaps.write( './' ) )
    .pipe( gulp.dest( './admin/js' ) );
});

gulp.task( 'script-subscription-form', () => {

  let files = [
    'block',
    'script'
  ];

  return mergeStream( files.map( ( file ) => {
      return browserify({
        'entries': `./subscription-form/assets/${file}.es`,
        'debug': true,
        'transform': [ babelify ]
    })
      .bundle()
        .on( 'error', function( err ) {
          console.error( err );
          this.emit( 'end' );
        })
      .pipe( sourceStream( `${file}.js` ) )
      .pipe( buffer() )
        .pipe( sourcemaps.init({'loadMaps': true}) )
        .pipe( sourcemaps.write( './' ) )
      .pipe( gulp.dest( './subscription-form/assets' ) );
  }) );
});

gulp.task( 'scripts', [ 'script-admin', 'script-subscription-form' ]);

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

  const styleSources = [
    {
      'src': 'subscription-form/assets/*.scss',
      'dest': 'subscription-form/assets'
    }, {
      'src': 'admin/css/*.scss',
      'dest': 'admin/css'
    }
  ];

  var stream = mergeStream();
  var style  = [];

  styleSources.forEach( ( value, index ) => {
    style[index] = gulp.src( value.src )
      .pipe( sass().on( 'error', sass.logError ) )
        .pipe( sourcemaps.init() )
        .pipe( autoprefixer( autoprefixerConfig ) )
        .pipe( sourcemaps.write( '.' ) )
      .pipe( gulp.dest( value.dest ) );

    stream.add( style[index]);
  });

  return stream;
});

/**
 * ---------------------------------------------------------------
 * Default tasks
 *
 * Define default task that can be called by just running `gulp`
 * from cli
 * ---------------------------------------------------------------
 */

gulp.task( 'default', [ 'scripts', 'styles' ], () => {
  gulp.watch([ '**/*.es' ], [ 'scripts' ]);
  gulp.watch([ '**/*.scss' ], [ 'styles' ]);
});
