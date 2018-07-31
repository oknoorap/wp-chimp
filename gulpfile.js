/* eslint-env node */
const path = require('path')
const merge = require('merge2')
const plumber = require('gulp-plumber')
const gulp = require('gulp')
const sass = require('gulp-sass')
const autoprefixer = require('gulp-autoprefixer')
const cleanCSS = require('gulp-clean-css')
const rename = require('gulp-rename')
const watch = require('gulp-watch')
const batch = require('gulp-batch')
const sourcemaps = require('gulp-sourcemaps')
const wpPot = require('gulp-wp-pot')
const gettext = require('gulp-gettext')
const readme = require('gulp-readme-to-markdown')
const getFileHeader = require('wp-get-file-header')
const rollup = require('gulp-rollup')
const babel = require('rollup-plugin-babel')
const uglify = require('gulp-uglify')
const resolve = require('rollup-plugin-node-resolve')
const jsx = require('rollup-plugin-jsx')
const prettier = require('rollup-plugin-prettier')

const assetPath = path.join(__dirname, 'assets')

const srcPath = {
  SCSS: path.join(assetPath, 'src', 'scss'),
  JS: path.join(assetPath, 'src', 'js'),
  REDOM: path.join(__dirname, 'node_modules', 'redom', 'dist')
}

const dstPath = {
  CSS: path.join(assetPath, 'css'),
  JS: path.join(assetPath, 'js'),
  LANG: path.join(__dirname, 'languages')
}

const externalModules = {
  redom: 'redom'
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
gulp.task('build:scripts', () => {
  const compileSource = gulp.src(path.join(srcPath.JS, '**', '*.js'))
    .pipe(plumber())
    .pipe(rollup({
      input: jsFiles.map(file => path.join(srcPath.JS, `${file}.js`)),
      output: {
        format: 'iife',
        globals: externalModules
      },
      external: Object.keys(externalModules),
      plugins: [
        jsx({ factory: 'wp.element.createElement', passUnknownTagsToFactory: true }),
        resolve({
          jsnext: true,
          browser:true,
        }),
        babel({
          plugins: ['external-helpers'],
          runtimeHelpers: true
        }),
        prettier({
          singleQuote: true
        })
      ]
    }))
    .pipe(sourcemaps.init())
    .pipe(gulp.dest(dstPath.JS))
    .pipe(rollup({
      input: jsFiles.map(file => path.join(dstPath.JS, `${file}.js`)),
      output: {
        format: 'iife',
        globals: externalModules
      },
      external: Object.keys(externalModules),
      plugins: [
        babel()
      ]
    }))
    .pipe(rename({ suffix: '.min' }))
    .pipe(uglify())
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(dstPath.JS))

  const copyReDOM = gulp.src(path.join(srcPath.REDOM, 'redom.js'))
    .pipe(gulp.dest(dstPath.JS))

  const copyMinifiedReDOM = gulp.src(path.join(srcPath.REDOM, 'redom.min.js'))
    .pipe(gulp.dest(dstPath.JS))

  return merge(compileSource, [
    copyReDOM,
    copyMinifiedReDOM
  ])
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
 * Compile translation to .pot and .mo
 */
gulp.task('build:lang', async () => {
  const { textDomain: domain, pluginName } = await getFileHeader(path.join(__dirname, 'wp-chimp.php'))
  const buildPOT = gulp.src(path.join(__dirname, '**', '*.php'))
    .pipe(plumber())
    .pipe(wpPot({
      domain,
      package: pluginName
    }))
    .pipe(gulp.dest(path.join(dstPath.LANG, `${domain}.pot`)))

  const buildMO = gulp.src(path.join(dstPath.LANG, '*.pot'))
    .pipe(plumber())
    .pipe(gettext())
    .pipe(gulp.dest(dstPath.LANG))

  return merge(buildPOT, buildMO)
})

/**
 * Convert README.md to WordPress README.txt
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
  gulp.start('build', () => {
    watch(path.join(srcPath.SCSS, '*.scss'), batch((events, done) => {
      gulp.start('build:styles', done)
    }))

    watch(path.join(srcPath.JS, '**', '*.js'), batch((events, done) => {
      gulp.start('build:scripts', done)
    }))

    watch(path.join(__dirname, '**', '*.php'), batch((events, done) => {
      gulp.start('build:lang', done)
    }))
  })
})

/**
 * Task to build all scripts, styles, translation file, and readme.
 */
gulp.task('build', [
  'build:scripts',
  'build:styles',
  'build:lang'
])

/**
 * Define default task that can be called by just running `gulp` from cli.
 */
gulp.task('default', ['watch:files'])
