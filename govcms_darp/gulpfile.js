// Fancyness
console.log();
console.log('\x1b[41m%s\x1b[0m', '                        ');
console.log('\x1b[41m\x1b[1m   OXIDE\x1b[21m build system   \x1b[0m\x1b[90m  check the README.md for full specs');
console.log('\x1b[41m%s\x1b[0m', '                        ');
console.log();

// Paths
var paths = {
  cssSource: 'sass/',
  cssDestination: 'css/'
}

// Local configuration
// Define defaults first..
var localConf = {
  "url": false,
  "open": false,
  "notify": false
};
// ...then load the local configuration if exists
try {
  localConf = require('./localconf.json');
} catch (ex) {
  console.log('\x1b[35mlocalconf.json\x1b[31m not found\x1b[0m: Can\'t load local configuration.');
  console.log('\x1b[90m%s\x1b[0m', 'Browsersync will not work, unless you copy and rename localconf.default.json and edit the settings.\n');
}

var gulp = require('gulp'),
  sass = require('gulp-sass'),
  sourcemaps = require('gulp-sourcemaps'),
  postcss = require('gulp-postcss'),
  // TODO: replace with new postcss-preset-env -> https://moox.io/blog/deprecating-cssnext/
  cssnext = require('postcss-cssnext'),
  rucksack = require('rucksack-css'),
  lost = require('lost'),
  plumber = require('gulp-plumber'),
  notify = require('gulp-notify'),
  inlineSvg =require('postcss-inline-svg'),
  svgo = require('postcss-svgo'),
  browserSync = require('browser-sync').create()

var reportError = function (error) {
  // TODO: print more descriptive error message
  notify({
    title: 'Gulp Task Error',
    message: 'Check the console.'
  }).write(error)
  console.log(error.toString())
  this.emit('end')
}

gulp.task('compile', function () {
  return gulp.src(paths.cssSource + '**/*.scss')
    .pipe(sourcemaps.init())
    .pipe(plumber({
      errorHandler: reportError
    }))
    .pipe(sass({
      outputStyle: 'nested', // [nested|expanded|compact|compressed]
      sourceComments: false,
      indentedSyntax: false
    }))
    .pipe(postcss([
      rucksack(),
      inlineSvg({
        removeFill: true
      }),
      svgo(),
      lost(),
      cssnext({
        browsers: ['ie >= 10', 'last 3 versions']
      })
    ]))
    .on('error', reportError)
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest(paths.cssDestination))
    .pipe(browserSync.stream({
      match: '**/*.css'
    }))
})

gulp.watch(paths.cssSource + '**/*.scss', ['compile'])

gulp.task('browser-sync', function () {
  if (localConf.url) {
    browserSync.init({
      proxy: localConf.url,
      open: localConf.open,
      notify: localConf.notify
    })
  }
})

gulp.task('default', [
  'compile',
  'browser-sync'
])
