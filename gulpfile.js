/* eslint-env node */
const path = require('path')
const buffer = require('vinyl-buffer')
const sourceStream = require('vinyl-source-stream')
const mergeStream = require('merge-stream')
const merge = require('merge2')
const plumber = require('gulp-plumber')
const browserify = require('browserify')
const babelify = require('babelify')
const gulp = require('gulp')
const sass = require('gulp-sass')
const autoprefixer = require('gulp-autoprefixer')
const cleanCSS = require('gulp-clean-css')
const rename = require('gulp-rename')
const watch = require('gulp-watch')
const batch = require('gulp-batch')
const sourcemaps = require('gulp-sourcemaps')
const eslint = require('gulp-eslint')
const readme = require('gulp-readme-to-markdown')

const assetPath = path.join(__dirname, 'assets')
const srcPath = {
  SCSS: path.join(assetPath, 'src', 'scss'),
  JS: path.join(assetPath, 'src', 'js')
}
const dstPath = {
  CSS: path.join(assetPath, 'css'),
  JS: path.join(assetPath, 'js')
}

const sassFiles = [
  'admin',
  'subscription-form-editor',
  'subscription-form'
]

const jsFiles = [
  'admin',
  'subscription-form-editor',
  'subscription-form'
]

/**
 * Task to compile the JSX files to JS.
 */
gulp.task('scripts', () => {
  return mergeStream(ecmaScriptFiles.map(file => {
    let fileName = path.basename(file, '.es')
    return browserify({
      'entries': `./assets/js/${file}`,
      'debug': true,
      'transform': [ babelify ]
    })
      .bundle()
      .on('error', err => {
        console.error(err)
        this.emit('end')
      })
      .pipe(sourceStream(`${fileName}.js`))
      .pipe(buffer())
      .pipe(sourcemaps.init({'loadMaps': true}))
      .pipe(sourcemaps.write('./'))
      .pipe(gulp.dest('./assets/js'))
  }))
})

gulp.task('eslint', () => {
  return gulp.src('./assets/js/**/*.es')
    .pipe(eslint())
    .pipe(eslint.format())
})

/**
 * Task to compile the SCSS files to CSS.
 *
 * We will loop through each files and put the compiled CSS to the
 * destination directory as listed in `styleSources`
 */
gulp.task('build:styles', () => {
  const sassConfig = {
    outputStyle: 'expanded'
  }

  const autoprefixerConfig = {
    browsers: [ 'last 3 versions', 'Firefox < 20' ],
    cascade: false
  }

  const cleanCSSConfig = {
    compatibility: 'ie8'
  }

  const renameConfig = {
    suffix: '.min'
  }

  return merge(
    sassFiles.map(file => {
      return gulp.src(path.join(srcPath.SCSS, `${file}.scss`))
        .pipe(plumber())
        .pipe(sass(sassConfig))
        .pipe(autoprefixer(autoprefixerConfig))
        .pipe(sourcemaps.init())
        .pipe(gulp.dest(dstPath.CSS))
        .pipe(cleanCSS(cleanCSSConfig))
        .pipe(rename(renameConfig))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(dstPath.CSS))
    })
  )
})

/**
 * ---------------------------------------------------------------
 * Misc tasks
 *
 * Define default task that can be called by just running `gulp`
 * from cli
 * ---------------------------------------------------------------
 */

gulp.task('readme', () => {
  gulp.src([ 'README.txt' ])
    .pipe(readme({
      details: false,
      screenshot_ext: ['jpg', 'png']
    }))
    .pipe(gulp.dest('.'))
})

/**
 * Watch files
 */
gulp.task('watch:files', () => {
  watch(path.join(srcPath.SCSS, '*.scss'), batch((events, done) => {
    gulp.start('build:styles', done)
  }))
})

/**
 * Task to build all scripts, styles, translation file, and readme.
 */
gulp.task('build', [
  'build:scripts',
  'build:styles'
])

/**
 * Define default task that can be called by just running `gulp` from cli.
 */
gulp.task('default', ['watch:files'])
