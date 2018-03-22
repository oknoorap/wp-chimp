/* eslint-env node */
const gulp = require( 'gulp' );
const babel = require( 'gulp-babel' );
const sass = require( 'gulp-sass' );
const autoprefixer = require( 'gulp-autoprefixer' );
const sourcemaps = require( 'gulp-sourcemaps' );
const mergeStream = require( 'merge-stream' );

const autoprefixerConfig = {
  browsers: [ 'last 3 versions' ],
  cascade: false
};

const scriptSources = [
  {
    'src': 'blocks/form/*.es',
    'dest': 'blocks/form'
  }, {
    'src': 'admin/js/*.es',
    'dest': 'admin/js'
  }, {
    'src': 'public/js/*.es',
    'dest': 'public/js'
  }
];

const styleSources = [
  {
    'src': 'blocks/form/*.scss',
    'dest': 'blocks/form'
  }, {
    'src': 'admin/css/*.scss',
    'dest': 'admin/css'
  }, {
    'src': 'public/css/*.scss',
    'dest': 'public/css'
  }
];

gulp.task( 'script', () => {

  var stream = mergeStream();
  var script = [];

  scriptSources.forEach( ( value, index ) => {
    script[index] = gulp.src( value.src )
      .pipe( babel() )
      .pipe( gulp.dest( value.dest ) );

    stream.add( script[index]);
  });

  return stream;
});

gulp.task( 'style', () => {

  var stream = mergeStream();
  var style = [];

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

gulp.task( 'default', [ 'script', 'style' ], () => {

  var watchScripts = [];
  var watchStyles = [];

  scriptSources.forEach( ( value, index ) => {
    watchScripts[index] = value.src;
  });

  styleSources.forEach( ( value, index ) => {
    watchStyles[index] = value.src;
  });

  gulp.watch( watchScripts, [ 'script' ]);
  gulp.watch( watchStyles, [ 'style' ]);
});
